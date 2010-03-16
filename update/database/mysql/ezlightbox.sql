CREATE TABLE ezlightbox (
 `id`          int(11)      NOT NULL auto_increment,
 `owner_id`    int(11)      NOT NULL default '0',
 `created`     int(11)      NOT NULL default '0',
 `name`        varchar(255) NOT NULL default 'New Lightbox',
 `external_id` varchar(255) default '',
  PRIMARY KEY ( `id` )
);

CREATE TABLE ezlightbox_object (
 `lightbox_id`      int(11) NOT NULL default '0',
 `contentobject_id` int(11) NOT NULL default '0',
 `created`          int(11) NOT NULL default '0',
  PRIMARY KEY ( `lightbox_id`, `contentobject_id` )
);

CREATE TABLE ezlightbox_access (
 `lightbox_id` int(11) NOT NULL default '0',
 `user_id`     int(11) NOT NULL default '0',
 `created`     int(11) NOT NULL default '0',
 `access_mask` int(11) NOT NULL default '0',
  PRIMARY KEY ( `lightbox_id`, `user_id` )
);
