ALTER TABLE ezlightbox_object CHANGE contentobject_id item_id int(11) NOT NULL default '0';
ALTER TABLE ezlightbox_object ADD COLUMN type_id  int(11) NOT NULL default '1' AFTER created;
ALTER TABLE ezlightbox_object ADD COLUMN priority int(11)     NULL default '0' AFTER type_id;
ALTER TABLE ezlightbox_object DROP PRIMARY KEY, ADD PRIMARY KEY( `lightbox_id`, `item_id`, `type_id` );

