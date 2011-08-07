<?php /**
 * Tag Behavior class file.
 *
 * Model Behavior to support tags.
 *
 * @filesource
 * @package    app
 * @subpackage    models.behaviors
 */
 
/**
 * Add tag behavior to a model.
 * 
 */
class TagBehavior extends ModelBehavior {
    /**
     * Initiate behaviour for the model using specified settings.
     *
     * @param object $model    Model using the behaviour
     * @param array $settings    Settings to override for model.
     *
     * @access public
     */
    function setup(&$model, $settings = array()) {

    
        $default = array( 'table_label' => 'tags', 'tag_label' => 'tag', 'separator' => ',');
        
        if (!isset($this->settings[$model->name])) {
            $this->settings[$model->name] = $default;
        }
        
    $this->settings[$model->name] = array_merge($this->settings[$model->name], ife(is_array($settings), $settings, array()));

    }
    
    /**
     * Run before a model is saved, used to set up tag for model.
     *
     * @param object $model    Model about to be saved.
     *
     * @access public
     * @since 1.0
     */
    function beforeSave(&$model) {
    // Define the new tag model
    $Tag =& new Tag;
        if ($model->hasField($this->settings[$model->name]['table_label']) 
        && $Tag->hasField($this->settings[$model->name]['tag_label'])) {


        // Parse out all of the 
        $tag_list = $this->_parseTag($model->data[$model->name][$this->settings[$model->name]['table_label']], $this->settings[$model->name]);
        $tag_info = array(); // New tag array to store tag id and names from db
        foreach($tag_list as $t) {
            if ($res = $Tag->find($this->settings[$model->name]['tag_label'] . " LIKE '" . $t . "'")) {
                $tag_info[] = $res['Tag']['id'];
            } else {
                $Tag->save(array('id'=>'',$this->settings[$model->name]['tag_label']=>$t));
                $tag_info[] = sprintf($Tag->getLastInsertID());
            }
            unset($res);
        }
		

        // This prepares the linking table data...
        $model->data['Tag']['Tag'] = $tag_info;
        // This formats the tags field before save...
        $model->data[$model->name][$this->settings[$model->name]['table_label']] = implode(', ', $tag_list);
    }
    //return true;
    }

	/**
	 * Checks Spam in Tags
	 * Returns a severity number.
	 *
	 * @param string $content 
	 * @return $flags A severity count of how many times an item was flagged.
	 */
	public function _getSpamFlags($content) {
		$flags = 0;
		// Get links in the content
		$links = preg_match_all("#(^|[\n ])(?:(?:http|ftp|irc)s?:\/\/|www.)(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,4}(?:[-a-zA-Z0-9._\/&=+%?;\#]+)#is", $content, $matches);
		$links = $matches[0];

		$totalLinks = count($links);
		$length = strlen($content);

		// How many links are in the body
		// +2 if less than 2, -1 per link if over 2
		if ($totalLinks > 2) {
			$flags = $flags + $totalLinks;
		} else {
			$flags = $flags - 1;
		}

		// Keyword search
		// -1 per blacklisted keyword
		$blacklistKeywords = array('levitra', 'viagra', 'casino', 'sex', 'loan', 'lol', 'nigs', 'nig', 'finance', 'slots', 'debt', 'free', 'nigger','nigga','jews', 'fucker',
									'ass', 'bitch','fucker','fuck','penis','vagina','erection','die', 'http://', '.com');
		foreach ($blacklistKeywords as $keyword) {
			if (stripos($content, $keyword) !== false) {
				$flags = $flags + 5;
			}
		}
		
		// Random character match
		// -1 point per 5 consecutive consonants
		$consonants = preg_match_all('/[^aAeEiIoOuU\s]{5,}+/i', $content, $matches);
		$totalConsonants = count($matches[0]);

		if ($totalConsonants > 0) {
			$flags = $flags + ($totalConsonants * 2);
		}

		return $flags;
	}


    /**
     * Parse the tag string and return a properly formatted array
     *
     * @param string $string    String.
     * @param array $settings    Settings to use (looks for 'separator' and 'length')
     *
     * @return string    Tag for given string.
     *
     * @access private
     */
    function _parseTag($string, $settings) {
        $string = strtolower($string);
       
        $string = preg_replace('/[^a-z0-9-' . $settings['separator'] . ' ]/i', '', $string);
        $string = preg_replace('/' . $settings['separator'] . '[' . $settings['separator'] . ']*/', $settings['separator'], $string);

    $string_array = preg_split('/' . $settings['separator'] . '/', $string);
    $return_array = array();

    foreach($string_array as $t) {
		$tFlags = $this->_getSpamFlags($t);
		$t = str_replace(' ', '-', $t);
        $t = strtolower(trim($t));
        if (strlen($t)>0 && $tFlags < 2) {
            $return_array[] = $t;
        }
    }
    
        return $return_array;
    }
}

?>