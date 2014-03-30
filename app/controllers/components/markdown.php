<?php
	class MarkdownComponent extends Object {
		
		/**
		 * Import the Markdownify vendor files and instantiate the object.
		 */
		public function __construct() {
			
			/**
			 * Import the Markdownify vendor files.
			 */
			App::import('Vendor', 'markdown/markdown');
		}
		
	  /**
	   * parse a Text string
	   *
	   * @param string $textInput
	   * @return string markdown formatted
	   */
		public function parseString($textInput) {
			return Markdown($textInput);
		}
	}
?>