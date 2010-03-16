ALTER TABLE ezlightbox_object RENAME COLUMN contentobject_id TO item_id;
ALTER TABLE ezlightbox_object ADD type_id  INTEGER DEFAULT '1' NOT NULL;
ALTER TABLE ezlightbox_object ADD priority INTEGER DEFAULT '0'     NULL;
ALTER TABLE ezlightbox_object DROP PRIMARY KEY
ALTER TABLE ezlightbox_object ADD PRIMARY KEY( lightbox_id, item_id, type_id );

