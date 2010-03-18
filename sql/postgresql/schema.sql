CREATE TABLE ezlightbox (
  ID          SERIAL   NOT NULL,
  owner_id    INT   DEFAULT '0'   NOT NULL,
  created     INT   DEFAULT '0'   NOT NULL,
  NAME        VARCHAR(255)   DEFAULT 'New Lightbox'   NOT NULL,
  external_id VARCHAR(255)   NOT NULL,
  CONSTRAINT pk_ezlightbox PRIMARY KEY ( id ));

CREATE TABLE ezlightbox_object (
  lightbox_id INT   DEFAULT '0'   NOT NULL,
  item_id     INT   DEFAULT '0'   NOT NULL,
  created     INT   DEFAULT '0'   NOT NULL,
  type_id     INT   DEFAULT '1'   NOT NULL,
  priority    INT   DEFAULT '0',
  CONSTRAINT pk_ezlightbox_object PRIMARY KEY ( lightbox_id,item_id,type_id ));

CREATE TABLE ezlightbox_access (
  lightbox_id INT   DEFAULT '0'   NOT NULL,
  user_id     INT   DEFAULT '0'   NOT NULL,
  created     INT   DEFAULT '0'   NOT NULL,
  access_mask INT   DEFAULT '0'   NOT NULL,
  CONSTRAINT pk_ezlightbox_access PRIMARY KEY ( lightbox_id,user_id ));