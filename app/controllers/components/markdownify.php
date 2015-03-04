<?php
	class MarkdownifyComponent extends Object {
		
		/**
		 * Define the Markdownify varaible.
		 */
		public $markdownify;
		
		/**
		 * Import the Markdownify vendor files and instantiate the object.
		 */
		public function __construct() {
			
			/**
			 * Import the Markdownify vendor files.
			 */
			App::import('Vendor', 'markdownify/markdownify');
			
			/**
			 * instantiate the Mardownify object.
			 */
			$this->markdownify = new Markdownify;
		}
		
	  /**
	   * parse a HTML string
	   *
	   * @param string $html
	   * @return string markdown formatted
	   */
		public function parseString($htmlInput) {
			return $this->markdownify->parseString($htmlInput);
		}
	}
?>