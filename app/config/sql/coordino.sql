--
-- Table structure for table `badges`
--

CREATE TABLE IF NOT EXISTS `badges` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `timestamp` int(12) NOT NULL,
  `user_id` int(12) NOT NULL,
  `image` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `badges`
--


-- --------------------------------------------------------

--
-- Table structure for table `bugs`
--

CREATE TABLE IF NOT EXISTS `bugs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `status` enum('open','closed','invalid') NOT NULL DEFAULT 'open',
  `submitted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `bugs`
--


-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `related_id` int(255) NOT NULL,
  `content` text NOT NULL,
  `timestamp` int(100) NOT NULL,
  `votes` smallint(5) NOT NULL,
  `user_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `comments`
--


-- --------------------------------------------------------

--
-- Table structure for table `histories`
--

CREATE TABLE IF NOT EXISTS `histories` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `related_id` int(25) NOT NULL,
  `user_id` int(25) NOT NULL,
  `timestamp` int(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `histories`
--

INSERT INTO `histories` (`id`, `type`, `related_id`, `user_id`, `timestamp`) VALUES
(1, 'asked', 1, 1, 1250490231);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `type` enum('answer','question','approved','pending','spam') NOT NULL,
  `related_id` int(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `status` enum('open','closed','correct') NOT NULL,
  `timestamp` int(100) NOT NULL,
  `last_edited_timestamp` int(100) NOT NULL,
  `user_id` int(10) NOT NULL,
  `votes` smallint(5) NOT NULL,
  `url_title` varchar(255) NOT NULL,
  `public_key` varchar(255) NOT NULL,
  `views` int(20) NOT NULL DEFAULT '1',
  `tags` text NOT NULL,
  `flags` smallint(3) NOT NULL,
  `notify` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`,`content`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `type`, `related_id`, `title`, `content`, `status`, `timestamp`, `last_edited_timestamp`, `user_id`, `votes`, `url_title`, `public_key`, `views`, `tags`, `flags`, `notify`) VALUES
(1, 'question', 0, 'Test Coordino Question', '<p>This is a sample <a href="http://www.coordino.com">Coordino</a> question.</p>', 'open', 1250490231, 0, 1, 0, 'test-coordino-question', '4a88f7770778d', 2, '', -2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `posts_revs`
--

CREATE TABLE IF NOT EXISTS `posts_revs` (
  `version_id` int(255) NOT NULL AUTO_INCREMENT,
  `version_created` datetime NOT NULL,
  `id` int(255) NOT NULL,
  `type` enum('answer','question') NOT NULL,
  `related_id` int(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `status` enum('open','closed') NOT NULL,
  `timestamp` int(100) NOT NULL,
  `last_edited_timestamp` int(100) NOT NULL,
  `user_id` int(10) NOT NULL,
  `votes` smallint(5) NOT NULL,
  `url_title` varchar(255) NOT NULL,
  `public_key` varchar(255) NOT NULL,
  `views` int(20) NOT NULL DEFAULT '1',
  `tags` text NOT NULL,
  `flags` smallint(3) DEFAULT NULL,
  PRIMARY KEY (`version_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `posts_revs`
--

INSERT INTO `posts_revs` (`version_id`, `version_created`, `id`, `type`, `related_id`, `title`, `content`, `status`, `timestamp`, `last_edited_timestamp`, `user_id`, `votes`, `url_title`, `public_key`, `views`, `tags`, `flags`) VALUES
(1, '2009-08-17 02:23:51', 1, 'question', 0, 'Test Coordino Question', '<p>This is a sample <a href="http://www.coordino.com">Coordino</a> question.</p>', 'open', 1250490231, 0, 1, 0, 'test-coordino-question', '4a88f7770778d', 1, 'sample, question', -2);

-- --------------------------------------------------------

--
-- Table structure for table `post_tags`
--

CREATE TABLE IF NOT EXISTS `post_tags` (
  `post_id` int(255) NOT NULL,
  `tag_id` int(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `post_tags`
--

INSERT INTO `post_tags` (`post_id`, `tag_id`) VALUES
(1, 1),
(1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `autoload` smallint(1) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `name`, `value`, `autoload`, `description`) VALUES
(1, 'rep_vote_up', '0', 1, 'The amount of reputation required before a user can vote up a question or answer.'),
(2, 'rep_comment', '25', 1, 'The amount of reputation required before a user can comment.'),
(3, 'rep_vote_down', '75', 1, 'The amount of reputation required before a user can vote down a question or answer.'),
(4, 'rep_advertising', '150', 1, 'The amount of reputation required before a user will no longer be show advertisements.'),
(5, 'rep_edit', '1000', 1, 'The amount of reputation required before a user can edit another user''s question or answer.'),
(6, 'rep_flag', '0', 0, 'The amount of reputation required before a user can flag a question or answer.'),
(7, 'flag_display_limit', '5', 0, 'The number of Flags a post needs to get before it is removed from public listings.'),
(8, 'remote_auth_only', 'no', 0, 'If set to ''yes'' logins will only be available via  third party login script.'),
(9, 'remote_auth_login_url', '', 0, 'The URL that a user must login through to access the site.'),
(10, 'remote_auth_logout_url', '', 0, 'The URL a user gets redirected to once they logout.'),
(11, 'site_maintenance', 'no', 0, 'If set to ''yes'', all pages should redirect to a message that the site is being updated/maintained.');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(150) NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `tag`, `slug`) VALUES
(1, 'sample', ''),
(2, 'question', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(60) NOT NULL,
  `url_title` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `joined` int(100) NOT NULL,
  `public_key` varchar(255) NOT NULL,
  `registered` smallint(1) NOT NULL,
  `reputation` int(10) NOT NULL,
  `website` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `age` smallint(3) NOT NULL,
  `info` text NOT NULL,
  `permission` text NOT NULL,
  `ip` varchar(20) NOT NULL,
  `answer_count` int(12) NOT NULL,
  `comment_count` int(12) NOT NULL,
  `question_count` int(12) NOT NULL,
  `image` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `url_title`, `email`, `password`, `joined`, `public_key`, `registered`, `reputation`, `website`, `location`, `age`, `info`, `permission`, `ip`, `answer_count`, `comment_count`, `question_count`, `image`) VALUES
(1, '', '', '', '', 0, '', 0, 0, '', '', 0, '', '', '', 0, 0, 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE IF NOT EXISTS `votes` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `post_id` int(255) NOT NULL,
  `user_id` int(10) NOT NULL,
  `timestamp` int(100) NOT NULL,
  `type` enum('up','down','flag') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `votes`
--


-- --------------------------------------------------------

--
-- Table structure for table `widgets`
--

CREATE TABLE IF NOT EXISTS `widgets` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `page` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `global` smallint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `widgets`
--

INSERT INTO `widgets` (`id`, `page`, `location`, `title`, `content`, `global`) VALUES
(1, '/', 'right', 'Welcome to Coordino', '<p>Coordino is a collaboratively edited <strong>question</strong> and <strong>answer</strong> site built to suit your needs.</p>\r\n\r\n<p>It''s easy to use, with no registration required!</p>\r\n\r\n<p><a href="/questions/ask">ask a question</a></p>', 1);
