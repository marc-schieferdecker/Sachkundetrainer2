CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_userlevel` tinyint(3) NOT NULL DEFAULT '0',
  `user_password` varchar(32) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_api_key` varchar(32) NOT NULL,
  `user_count_wrong` int(11) NOT NULL DEFAULT '0',
  `user_count_right` int(11) NOT NULL DEFAULT '0',
  `user_config` TEXT NOT NULL,
  `user_answered` LONGTEXT NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Create default user (login with api key!)
INSERT IGNORE INTO `user` SET `user_id` = 1,
  `user_userlevel` = 30,
  `user_password` = MD5(RAND()),
  `user_email` = 'ms@letsshootshow.de',
  `user_name` = 'Admin',
  `user_api_key` = MD5(RAND());

# Create test user for api documentation and guest login
INSERT IGNORE INTO `user` SET `user_id` = 2,
  `user_userlevel` = 10,
  `user_password` = MD5(RAND()),
  `user_email` = 'admin@letsshootshow.de',
  `user_name` = 'Gast',
  `user_api_key` = 'c0aa6c85ab5d92513398a28381c701e6';

CREATE TABLE IF NOT EXISTS `topic` (
  `topic_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `topic_name` varchar(255) NOT NULL,
  `topic_number` varchar(20) NOT NULL,
  PRIMARY KEY (`topic_id`)
);

CREATE TABLE IF NOT EXISTS `subtopic` (
  `subtopic_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subtopic_name` varchar(255) NOT NULL,
  `subtopic_number` varchar(20) NOT NULL,
  `subtopic_topic_parent_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`subtopic_id`)
);

CREATE TABLE IF NOT EXISTS `question` (
  `question_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `question_topic_id` int(11) NOT NULL DEFAULT '0',
  `question_subtopic_id` int(11) NOT NULL DEFAULT '0',
  `question_number` varchar(20) NOT NULL,
  `question_text` TEXT,
  `question_count_wrong` int(11) NOT NULL DEFAULT '0',
  `question_count_right` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`question_id`)
);

CREATE TABLE IF NOT EXISTS `answere` (
  `answere_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `answere_question_id` int(11) NOT NULL,
  `answere_number` varchar(20) NOT NULL,
  `answere_choice` TEXT,
  `answere_text` TEXT,
  `answere_correct` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`answere_id`)
);

CREATE TABLE IF NOT EXISTS `favourite` (
  `favourite_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `question_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`favourite_id`)
);

CREATE TABLE IF NOT EXISTS `comment` (
  `comment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `comment_question_id` int(11) NOT NULL DEFAULT '0',
  `comment_user_id` int(11) NOT NULL DEFAULT '0',
  `comment_text` TEXT,
  `comment_timestamp` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_id`)
);