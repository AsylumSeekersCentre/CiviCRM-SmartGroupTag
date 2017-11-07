-- /*******************************************************
-- *
-- * civicrm_smarttag_map
-- *
-- * Tag-SmartGroup mapping
-- *
-- *******************************************************/

DROP TABLE IF EXISTS `civicrm_smarttag_map`;

CREATE TABLE `civicrm_smarttag_map` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Unique Smarttag mapping ID',
     `tag_id` int unsigned    COMMENT 'FK to tag',
     `group_id` int unsigned    COMMENT 'FK to group' 
,
        PRIMARY KEY (`id`)
 
 
,          CONSTRAINT FK_civicrm_smarttag_map_tag_id FOREIGN KEY (`tag_id`) REFERENCES `civicrm_tag`(`id`) ON DELETE CASCADE,          CONSTRAINT FK_civicrm_smarttag_map_group_id FOREIGN KEY (`group_id`) REFERENCES `civicrm_group`(`id`) ON DELETE CASCADE  
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;


