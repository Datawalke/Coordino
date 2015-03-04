<?php 	class HtmlfilterComponent extends Object {
		public $htmlFilter;
		/**
		 * Import the HTML Filter vendor files and instantiate the object.
		 */
		public function __construct() {
			
			/**
			 * Import the Markdownify vendor files.
			 */
			App::import('Vendor', 'htmlfilter/htmlfilter');
			$this->htmlFilter = new HtmlFilter;
		}
		
		public function filter($content) {
			return $this->htmlFilter->filter($content);
		}
	}
?>