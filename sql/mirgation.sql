#1
CREATE TABLE `post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `public_url` varchar(255) DEFAULT NULL,
  `public_post_id` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `text` text DEFAULT NULL,
  `status` ENUM(  'parsed',  'posted' ) NULL DEFAULT 'parsed',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,

  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8; 

#2

CREATE TABLE `image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,

  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8; 

