<?php 
/********************************************************
 * TrickyFileInputHelper for CakePHP 1.2.something or whenever
 * 
 * Sorry for the non-conformity to standard documentation guidelines, I usually do my commenting
 * all at the end of development, and I usually just want to finish the damn thing so I can either
 * get it up and running, or share it.  Sooo yeah, I tried to make them as understandable as possible
 * so just keep that in mind.
 * 
 * I was creating stylized file inputs, and found out that there was a bit of a problem here: I wanted
 * this functionality usable everywhere as a generic file input object.  So I created a helper for the 
 * section, and started making it more and more general, until I realized I should post this as my work
 * in the bakery.  If you think this code is useful, let let me know.
 * 
 * This software is a CakePHP Helper variation on a whole host of immensely knowledgeable authors and
 * amazingly resourceful online documentation.  Among them are:
 *  Shaun Inmann: http://www.shauninman.com/archive/2007/09/10/styling_file_inputs_with_css_and_the_dom
 *  Michael McGrady: whom I have not been able to pull a site of, but basically thought up the premise.  kudos, dude.
 * 
 * So, use the examples provided on the Bakery page, or go to www.vibrantinstincts.com to check out this helper.
 * Or, get in touch with me via AIM @ vibrantinstincts.  I'd open up some other channel of communication, but I'm
 * just motivated enough to write this.
 * 
 * @author 			Michael Marcos
 * @name			TrickyFileInput.php
 * @version			0.8.10.20 (I've always wanted to make a product with 3 sub-revisions :p)
 * @last-updated	Fri July 3, 2009 4:13 PM
 */
class TrickyFileInputHelper extends AppHelper {
	/**
	 * Chooses the type of Tricky Input to print by default using draw(array());  Must be included in the
	 * switch statements in the draw functions, or in the list below:
	 * 
	 * picker
	 * pickerWithName
	 * 
	 */
	var $defaultType = 'picker';
	
	/**
	 * Set debug to 'yes' to show debug, and 'no' to hide it.  Set 'displayOnFail' to
	 * 'regular' to display a regular elements (no onverlay) with the default error_classes.
	 * This way, if it fails, you can still get the element displayed to the user.  You can 
	 * choose 'autosubmit', which creates a form wrapped file input that submits onchange.  
	 * Or you can set it to the default: 'none' which displays, shockingly, nothing.
	 */
	var $debug = 'no';
	var $displayOnFail = 'autosubmit';
	
  	/**
 	 * The array of styles and function of the form elements.  Some are defined 
	 * as an enumerated list, please make sure your passed in array follows the 
	 * examples provided, and follows the conventions below:
	 *
	 * @index ['form'] can be either an array defined below, or false to not display (default: false)
	 * @index ['form']['id'] the id of the form (default: tricky_file_form)
	 * @index ['form']['name'] the name of the form (default: tricky_file_form)
	 * @index ['form']['action'] the form's action (default: ?)
	 * @index ['form']['method'] the method of the form, can be either get or post (lowercase) (default: post)
	 * @index ['input'] the array of options for the input
	 * @index ['input']['id'] the id of the file input (default: tricky_file_input)
	 * @index ['input']['name'] the name of the file input (default: tricky_file_input)
	 * @index ['input']['submitOnChange'] either true or false.  determines if the form (required for use) is automatically submitted on change 
	 * 
	 */
	var $defaultOptions = array(
		'form' => array(
			'id' => 'tricky_file_form',
			'name' => 'tricky_file_form',
			'action' => '?',
			'method' => 'post'),
		'input' => array(
			'id' => 'tricky_file_input',
			'name' => 'tricky_file_input',
			'submitOnChange' => ''),
		'name' => array(
			'id' => 'tricky_file_name',
			'name' => 'tricky_file_name'),
		'image' => '/img/buttons/choose_image.png');
	
	/**
	 * The options array that contains the defaults to the same structure as $defaultOptions.
	 */
	var $options = array();			
		
	/**
	 * The number of runs that have been made using the program, to increment HTML values
	 */
	var $runs = 0;
		
  	/**
 	 * The array of css styling to avoid conflicts with existing css styles.  The styles array
	 * indexes are as follows:
	 * 
	 * @index ['div'] the div wrapping the input field (default: pick_file_wrapper)
	 * @index ['input'] the input field behind the image (default: pick_file)
	 * @index ['image'] the image above the input (default: pick_file_overlay)
	 * @index ['name'] the span next to the input, if included
	 */
	var $styles = array(
		'div' => 'pick_file_wrapper',
		'input' => 'pick_file',
		'image' => 'pick_file_overlay',
		'name' => 'pick_file_name');
			
  	/**
 	 * The CSS classes that will be displayed if displayOnFail != 'none'
	 */
	var $errorStyles = array(
		'div' => 'pick_file_wrapper_error',
		'input' => 'pick_file_error',
		'image' => 'pick_file_overlay_error',
		'name' => 'pick_file_name_error');


	/**
	 * Array that contains the errors found inside the _findErrors() method.
	 */
    private $errors = array();

	/**
	 * This method can be called using from a view to print a Tricky Element to the
	 * page.  This method accepts a custom type, that must be included in the $this->types
	 * array.  If the element is not found, an error will be displayed.
	 * 
	 * @param $type the type of tricky element to print
	 * @param $params the options array that affects the element's attributes
	 */
	function draw($type = null, $params = array()) {		
		if($type != null) {
			switch($type) {
				case 'picker':
					$this->__picker($params);
					break;
				case 'pickerWithName':
					$this->__pickerWithName($params);
					break;
			}
		} else {
			switch($this->defaultType) {
				case 'picker':
					$this->__picker($params);
					break;
				case 'pickerWithName':
					$this->__pickerWithName($params);
					break;
			}
		}
	}



	/**
	 * Called from the draw() function based on the $type parameter to draw
	 * a file picking button without any other elements.
	 */
    function __picker($params = array()) {
		if(!$this->__startup('picker', $params)) {
			return;
		}
		
		if($this->options['form'] != false) { ?>
			<?php $this->__htmlForm(); ?>
				<?php $this->__htmlDiv(); ?>
					<?php $this->__htmlInput(); ?>
					<?php $this->__htmlImage(); ?>
				<?php $this->__htmlEndTag('div'); ?>
			<?php $this->__htmlEndTag('form'); ?>
		<?php } else { ?>
			<?php $this->__htmlDiv(); ?>
				<?php $this->__htmlInput(); ?>
				<?php $this->__htmlImage(); ?>
			<?php $this->__htmlEndTag('div'); ?>
		<?php }
    }



	/**
	 * Called from the draw() function based on the $type parameter to draw
	 * a file picking button with a disabled text field to show the name of the file.
	 */
	function __pickerWithName($params = array()) {
		if(!$this->__startup('pickerWithName', $params)) {
			return;
		}

		if($this->options['form'] != false) {
			$this->__htmlForm();
			$this->__htmlDiv();
			$this->__htmlInput();
			$this->__htmlImage();
			$this->__htmlEndTag('div');
			$this->__htmlName();
			$this->__htmlEndTag('form');
		} else {
			$this->__htmlDiv();
				$this->__htmlInput();
				$this->__htmlImage();
			$this->__htmlEndTag('div');
		}
		
		$this->__javascriptNameChange();
		
	}	
	
	
	
	/**
	 * Displays a regular file element if the program fails with errors and is requested.
	 */
	function __displayAutosubmit() { ?>
		<form 
			enctype="multipart/form-data" 
			action="<?=$this->options['form']['action']?>" 
			id="<?=$this->options['form']['id']?>" 
			name="<?=$this->options['form']['name']?>" 
			method="<?=$this->options['form']['method']?>"
		>
			<div class="<?=$this->errorStyles['div']?>">
				<input 
					type="file" 
					id="<?=$this->options['input']['id']?>" 
					name="<?=$this->options['input']['name']?>" 
					class="<?=$this->errorStyles['input']?>" 
					onchange="document.<?=$this->options['form']['name']?>.submit();"
				/>
			</div>
		</form>
	<?php }
	
	
	
	/**
	 * Displays a regular file element if the program fails with errors and is requested.
	 */
	function __displayRegular() { ?>
		<div class="<?=$this->errorStyles['div']?>">
			<input 
				type="file" 
				id="<?=$this->options['input']['id']?>" 
				name="<?=$this->options['input']['name']?>" 
				class="<?=$this->errorStyles['input']?>" 
				onchange="<?=$this->options['input']['submitOnChange']?>"
			/>
		</div>
	<?php }
	
	
	
	/**
	 * Increments the default values to prevent overwriting
	 */
	function __incrementDefaultOptionIds() {				
		if($this->options['form']['name'] == $this->defaultOptions['form']['name']) {
			$this->options['form']['name'] = $this->defaultOptions['form']['name'] . '_' . $this->runs;
		} if($this->options['form']['id'] == $this->defaultOptions['form']['id']) {
			$this->options['form']['id'] = $this->defaultOptions['form']['id'] . '_' . $this->runs;
		}
		
		if($this->options['input']['name'] == $this->defaultOptions['input']['name']) {
			$this->options['input']['name'] = $this->defaultOptions['input']['name'] . '_' . $this->runs;
		} if($this->options['input']['id'] == $this->defaultOptions['input']['id']) {
			$this->options['input']['id'] = $this->defaultOptions['input']['id'] . '_' . $this->runs;
		}

		if($this->options['name']['name'] == $this->defaultOptions['name']['name']) {
			$this->options['name']['name'] = $this->defaultOptions['name']['name'] . '_' . $this->runs;
		} if($this->options['name']['id'] == $this->defaultOptions['name']['id']) {
			$this->options['name']['id'] = $this->defaultOptions['name']['id'] . '_' . $this->runs;
		}
	}
	
	
	
	/**
	 * Called from the drawing functions to initiate the components.
	 */
	function __startup($type, $params) {
		$this->__resetElementOptions();
		$this->__getErrors($type, $params);
		
		if(!empty($this->errors)) {
			if($this->debug == 'yes') {
				$this->__printErrors();
			}
			
			if($this->displayOnFail == 'regular') {
				$this->__displayRegular();
			} else  if($this->displayOnFail == 'autosubmit') {
				$this->__displayAutosubmit();
			}
			
			return false;
		}
		
		$this->runs++;
		$this->__setOptions($params);
		$this->__incrementDefaultOptionIds();
		
		return true;
	}
	
	
	
	/**
	 * This method resets the options for the next field to be drawn.
	 */
	function __resetElementOptions() {
		$this->options = $this->defaultOptions;
	}
	
	
	
	/**
	 * This method prints founds errors.
	 */
	function __printErrors() {
		echo '<b>SNAKES IN YOUR PLANE!</b><br/>';
		foreach($this->errors as $err) {
			echo $err . '<br/>';
		}
	}
	
	/**
	 * This method finds any errors, and set the errors array with them.
	 */
	function __getErrors($type, $params) {
		if((!isset($params['form']) || $params['form'] == false) && (isset($params['input']['submitOnChange']) && $params['input']['submitOnChange'])) {
			$this->errors[] = '<b>Squawk!</b> You cannot autosubmit this element if form is turned off.';
		}
				
		if(isset($params['form']['method']) && !($params['form']['method'] == 'post' || $params['form']['method'] == 'get')) {
			$this->errors[] = '<b>Sqwawk!</b> Invalid method type (optional: get or post)!';
		}
		
		if($type == 'pickerWithName' && isset($params['input']['submitOnChange']) && $params['input']['submitOnChange']) {
			$this->errors[] = '<b>Squawk!</b> You cannot autosubmit an element with a name input field';
		}
		
		if($type == 'pickerWithName' && $params['form'] !== false) {
			$this->errors[] = '<b>Squawk!</b> You cannot create a pickerWithName with a form wrapper!  You have to create your own submit.';
		}

	}
	
	/**
	 * This method accepts the parameters passed into this helper,
	 * in preparation for printing the element.
	 */
	function __setOptions($params) {
		if(!isset($params) && empty($params)) {
			return;
		}
				
		/* set form elements */	
		if(isset($params['form']) && $params['form'] !== false) {
			if(isset($params['form']['id'])) { $this->options['form']['id'] = $params['form']['id']; }
			if(isset($params['form']['name'])) { $this->options['form']['name'] = $params['form']['name']; }
			if(isset($params['form']['method'])) { $this->options['form']['method'] = $params['form']['method']; }
			if(isset($params['form']['action'])) { $this->options['form']['action'] = $params['form']['action']; }
		} else {
			$this->options['form'] = false;
		}
		
		/* set input elements */
		if(isset($params['input'])) {
			if(isset($params['input']['id'])) { $this->options['input']['id'] = $params['input']['id']; }
			if(isset($params['input']['name'])) { $this->options['input']['name'] = $params['input']['name']; }
			if(isset($params['input']['submitOnChange']) && $params['input']['submitOnChange']) { 
				$this->options['input']['submitOnChange'] = 'document.' . $this->options['form']['name'] . '.submit();'; 
			}
		}
		
		/* set name elements */
		if(isset($params['name'])) {
			if(isset($params['name']['id'])) { $this->options['name']['id'] = $params['name']['id']; }
			if(isset($params['name']['name'])) { $this->options['name']['name'] = $params['name']['name']; }
		}
		
		/* set styles elements */
		if(isset($params['styles'])) {
			if(isset($params['styles']['div'])) { $this->styles['div'] = $params['styles']['div']; }
			if(isset($params['styles']['input'])) { $this->styles['input'] = $params['styles']['input']; }
			if(isset($params['styles']['image'])) { $this->styles['image'] = $params['styles']['image']; }
			if(isset($params['styles']['name'])) { $this->styles['name'] = $params['styles']['name']; }
		}

		/* set the image */
		if(isset($params['image'])) { $this->options['image'] = $params['image']; }
	}
	
	/**
	 * Creates an HTML ending tag based on a tagName
	 */
	function __htmlEndTag($tagName = null) {
		echo '</' . $tagName . '>';
	}
	
	

	/**
	 * Creates a tricky input html form
	 */
	function __htmlForm() { ?>
		<form 
			enctype="multipart/form-data" 
			action="<?=$this->options['form']['action']?>" 
			id="<?=$this->options['form']['id']?>" 
			name="<?=$this->options['form']['name']?>" 
			method="<?=$this->options['form']['method']?>"
		>
	<?php }
	
	
	
	/**
	 * Creates a tricky input html div wrapper
	 */
	function __htmlDiv() { ?>
		<div class="<?=$this->styles['div']?>">
	<?php }
	
	

	/**
	 * Creates a tricky input html file input field
	 */
	function __htmlInput() { ?>
		<input 
			type="file" 
			id="<?=$this->options['input']['id']?>" 
			name="<?=$this->options['input']['name']?>" 
			class="<?=$this->styles['input']?>" 
			onchange="<?=$this->options['input']['submitOnChange']?>"
		/>
	<?php }
	

	/**
	 * Creates a tricky input html file input field
	 */
	function __htmlInputForName() { ?>
		<input 
			type="file" 
			id="<?=$this->options['input']['id']?>" 
			name="<?=$this->options['input']['name']?>" 
			class="<?=$this->styles['input']?>" 
			onchange="<?=$this->options['input']['submitOnChange']?>"
		/>
	<?php }
	
	
	
	/**
	 * Creates a tricky input html image
	 */
	function __htmlImage() { ?>
		<img 
			class="<?=$this->styles['image']?>" 
			src="<?=$this->options['image']?>"
		/>
	<?php }



	/**
	 * Creates a tricky input html name text field
	 */
	function __htmlName() { ?>
		<span 
			id="<?=$this->options['name']['id']?>" 
			class="<?=$this->styles['name']?>" 
		></span>
	<?php }
	
	
	
	/**
	 * Creates a javascript onchange
	 */
	function __javascriptNameChange() { ?>
		<script type="text/javascript">
			document.getElementById("<?=$this->options['input']['id']?>").onchange = function() {
				var name = document.getElementById("<?=$this->options['input']['id']?>");
				document.getElementById("<?=$this->options['name']['id']?>").innerHTML = name.value;
			}
		</script>
	<?php }
	
}
?>