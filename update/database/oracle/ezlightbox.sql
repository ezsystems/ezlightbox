CREATE TABLE ezlightbox (
 id          integer      NOT NULL,
 owner_id    integer      DEFAULT 0 NOT NULL,
 created     integer      DEFAULT 0 NOT NULL,
 name        varchar(255) DEFAULT 'New Lightbox' NOT NULL,
 external_id varchar(255) DEFAULT '',
 PRIMARY KEY ( id )
);

CREATE SEQUENCE s_lightbox
CREATE OR REPLACE TRIGGER ezlightbox_tr
BEFORE INSERT ON ezlightbox FOR EACH ROW WHEN (new.id IS NULL)
BEGIN
  SELECT s_lightbox.nextval INTO :new.id FROM dual;
END;
/

CREATE INDEX ezlightbox_owner_id ON ezlightbox (owner_id);
CREATE INDEX ezlightbox_name_id ON ezlightbox (name);
CREATE INDEX ezlightbox_external_id ON ezlightbox (external_id);

CREATE TABLE ezlightbox_object (
 lightbox_id      integer DEFAULT 0 NOT NULL,
 contentobject_id integer DEFAULT 0 NOT NULL,
 created          integer DEFAULT 0 NOT NULL,
 PRIMARY KEY ( lightbox_id, contentobject_id )
);

CREATE TABLE ezlightbox_access (
 lightbox_id integer DEFAULT 0 NOT NULL,
 user_id     integer DEFAULT 0 NOT NULL,
 created     integer DEFAULT 0 NOT NULL,
 access_mask integer DEFAULT 0 NOT NULL,
 PRIMARY KEY ( lightbox_id, user_id )
);

CREATE INDEX ezlightbox_access_mask ON ezlightbox_access (access_mask);
