<?php
/** 
 * commentia.php
 *
 * A CakePHP Behavior that moderates / validates comments to check for spam.
 * Validates based on a point system. High flags is an automatic approval, where as low flags is marked as spam or deleted.
 * Based on Jonathon Snooks outline.
 *
 * Copyright 2006-2009, Miles Johnson - www.milesj.me
 * Licensed under The MIT License - Modification, Redistribution allowed but must retain the above copyright notice
 * @link 		http://www.opensource.org/licenses/mit-license.php
 *
 * @package		Commentia Behavior - Comment Spam Blocker
 * @created		February 8th 2008
 * @version 	1.3
 * @link		www.milesj.me/resources/script/commentia-behavior
 * @link		www.snook.ca/archives/other/effective_blog_comment_spam_blocker/
 * @changelog	www.milesj.me/files/logs/commentia-behavior
 */
 
class CommentiaBehavior extends ModelBehavior {

	/**
	 * Current version: www.milesj.me/files/logs/commentia-behavior
	 * @var string
	 */ 
	var $version = '1.3';
	
	/**
	 * Settings
	 * - Column name for the authors name
	 * - Column name for the comments body
	 * - Column name for the authors email
	 * - Column name for the authors website 
	 * - Column name of the foreign id that links to the article/entry/etc
	 * - Model name of the parent article/entry/etc
	 * - Link to the parent article, use :id for the permalink id
	 * - Email address where the notify emails should go
	 * - Should the flags be saved to the database?
	 * - Should you receive a notification email for each comment? 
	 * - How many flags till the comment is deleted (negative)
	 * @var array 
	 */  
	var $settings = array( 
		'column_author'		=> 'name',
		'column_content'	=> 'content',
		'column_email'		=> 'user_id',
		'column_website'	=> 'website',
		'column_foreign_id'	=> 'entry_id',
		'parent_model'		=> 'Post',
		'article_link'		=> '',
		'notify_email'		=> '',
		'save_flags'		=> true,
		'send_email'		=> true,
		'blacklist_keys'	=> '',
		'blacklist_words'	=> '',
		'deletion'			=> -5
	);
	
	/**
	 * Disallowed words within the comment body
	 * @var array
	 */
	var $blacklistKeywords = array('levitra', 'viagra', 'casino', 'sex', 'loan', 'finance', 'slots', 'debt', 'free');
	
	/**
	 * Disallowed words/chars within the url links
	 * @var array
	 */
	var $blacklistWords = array('.html', '.info', '?', '&', '.de', '.pl', '.cn');
	
	/**
	 * Startup hook from the model
	 * @param object $Model
	 * @param array $settings
	 * @return void
	 */
	function setup(&$Model, $settings = array()) {
		if (!empty($settings) && is_array($settings)) {
			$this->settings = array_merge($this->settings, $settings);
		}
		
		if (!empty($this->settings['blacklist_keys']) && is_array($this->settings['blacklist_keys'])) {
			$this->blacklistKeywords = array_merge($this->blacklistKeywords, $this->settings['blacklist_keys']);
		}
		
		if (!empty($this->settings['blacklist_words']) && is_array($this->settings['blacklist_words'])) {
			$this->blacklistWords = array_merge($this->blacklistWords, $this->settings['blacklist_words']);
		}
	}

	/**
	 * Runs before a save and marks the content as spam or regular comment
	 * @param object $Model
	 * @param boolean $created
	 * @return mixed
	 */
	function afterSave(&$Model, $created) {
		if ($created) {
			$data = $Model->data[$Model->name];
			$flags =  0;
			
			if (!empty($data)) {
				// Get links in the content
				$links = preg_match_all("#(^|[\n ])(?:(?:http|ftp|irc)s?:\/\/|www.)(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,4}(?:[-a-zA-Z0-9._\/&=+%?;\#]+)#is", $data[$this->settings['column_content']], $matches);
				$links = $matches[0];
				
				$totalLinks = count($links);
				$length = strlen($data[$this->settings['column_content']]);
		
				// How many links are in the body
				// +2 if less than 2, -1 per link if over 2
				if ($totalLinks > 2) {
					$flags = $flags - $totalLinks;
				} else {
					$flags = $flags + 2;
				}
				
				// How long is the body
				// +2 if more then 20 chars and no links, -1 if less then 20
				if ($length >= 20 && $totalLinks <= 0) {
					$flags = $flags + 2;
				} else if ($length >= 20 && $totalLinks == 1) {
					++$flags;
				} else if ($length < 20) {
					--$flags;
				}
				
				// Number of previous comments from email
				// +1 per approved, -1 per spam
				$comments = $Model->find('all', array(
					'fields' => array($Model->alias .'.id', $Model->alias .'.status'),
					'conditions' => array($Model->alias .'.'. $this->settings['column_email'] => $data[$this->settings['column_email']]),
					'recursive' => -1,
					'contain' => false
				));
				
				if (!empty($comments)) {
					foreach ($comments as $comment) {
						if ($comment[$Model->alias]['status'] == 'spam') {
							--$flags;
						}
						
						if ($comment[$Model->alias]['status'] == 'approved') {
							++$flags;
						}
					}
				}
				
				// Keyword search
				// -1 per blacklisted keyword
				foreach ($this->blacklistKeywords as $keyword) {
					if (stripos($data[$this->settings['column_content']], $keyword) !== false) {
						--$flags;
					}
				}
				
				// URLs that have certain words or characters in them
				// -1 per blacklisted word
				// URL length
				// -1 if more then 30 chars
				foreach ($links as $link) {
					foreach ($this->blacklistWords as $word) {
						if (stripos($link, $word) !== false) {
							--$flags;
						}
					}
					
					foreach ($this->blacklistKeywords as $keyword) {
						if (stripos($link, $keyword) !== false) {
							--$flags;
						}
					}
					
					if (strlen($link) >= 30) {
						--$flags;
					}
				}	
				
				// Body starts with...
				// -10 flags
				$firstWord = substr($data[$this->settings['column_content']], 0, stripos($data[$this->settings['column_content']], ' '));
				$firstDisallow = array_merge($this->blacklistKeywords, array('interesting', 'cool', 'sorry'));
				
				if (in_array(strtolower($firstWord), $firstDisallow)) {
					$flags = $flags - 10;
				} 
				
				// Author name has http:// in it
				// -2 flags
				if (stripos($data[$this->settings['column_author']], 'http://') !== false) {
					$flags = $flags - 2;
				}
				
				// Body used in previous comment
				// -1 per exact comment
				$previousComments = $Model->find('count', array(
					'conditions' => array($Model->alias .'.'. $this->settings['column_content'] => $data[$this->settings['column_content']]),
					'recursive' => -1,
					'contain' => false
				));
				
				if ($previousComments > 0) {
					$flags = $flags - $previousComments;
				}
				
				// Random character match
				// -1 point per 5 consecutive consonants
				$consonants = preg_match_all('/[^aAeEiIoOuU\s]{5,}+/i', $data[$this->settings['column_content']], $matches);
				$totalConsonants = count($matches[0]);
				
				if ($totalConsonants > 0) {
					$flags = $flags - $totalConsonants;
				}
				
				// Finalize and save
				if ($flags >= 1) {
					$status = 'approved';
				} else if ($flags == 0) {
					$status = 'pending';
				} else if ($flags <= $this->settings['deletion']) {
					$status = 'delete';
				} else {
					$status = 'spam';
				}
				
				if ($status == 'delete') {
					$Model->delete($Model->id, false);
				} else {
					$update = array();
					$update['status'] = $status;
					$update['flags'] = $flags;
					
					$save = array('status');
					if ($this->settings['save_flags'] === true) {
						$save[] = 'flags';
					}
					
					$Model->id = $Model->id;
					$Model->save($update, false, $save);
					
					if ($this->settings['send_email'] === true) {
						$this->notify($data, $update);
					}
				}		
			}
			
			return $flags;
		}
	}
	
	/**
	 * Sends out an email notifying you of a new comment
	 * @param array $data
	 * @param array $stats
	 * @return void
	 */
	function notify($data, $stats) {
		if (!empty($this->settings['parent_model']) && !empty($this->settings['article_link']) && !empty($this->settings['notify_email'])) {
			App::import('Component', 'Email');
			$Email = new EmailComponent();
			$Entry = ucfirst(strtolower($this->settings['parent_model']));
			
			// Get parent entry/blog
			$entry = ClassRegistry::init($Entry)->find('first', array(
				'fields' => array($Entry .'.id', $Entry .'.title'),
				'conditions' => array($Entry .'.id' => $data[$this->settings['column_foreign_id']])
			));
			
			// Config
			$entryLink 	= str_replace(':id', $entry[$Entry]['id'], $this->settings['article_link']);
			$entryTitle = $entry[$Entry]['title'];
			
			// Build message
			$message  = "A new comment has been posted for: ". $entryLink ."\n\n";
			$message .= 'Name: '. $data[$this->settings['column_author']] .' <'. $data[$this->settings['column_email']] .">\n";
			$message .= 'Status: '. ucfirst($stats['status']) .' ('. $stats['flags'] ." flags)\n";
			$message .= "Message:\n\n". $data[$this->settings['column_content']];
			
			// Send email
			$Email->to = $this->settings['notify_email'];
			$Email->from = $data[$this->settings['column_author']] .' <'. $data[$this->settings['column_email']] .'>';
			$Email->subject = 'Comment Approval: '. $entryTitle;
			$Email->send($message); 
		}
	}
	
}
