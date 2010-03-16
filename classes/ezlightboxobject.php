<?php

require_once( 'autoload.php' );

class eZLightboxObject extends eZPersistentObject
{

    function eZLightboxObject( $row = array() )
    {
        $this->eZPersistentObject( $row );
    }

    public static function definition()
    {
        return array( 'fields'              => array( 'lightbox_id'      => array( 'name'              => 'LightboxID',
                                                                                   'datatype'          => 'integer',
                                                                                   'default'           => 0,
                                                                                   'required'          => true,
                                                                                   'foreign_class'     => 'eZLightbox',
                                                                                   'foreign_attribute' => 'id',
                                                                                   'multiplicity'      => '1..*'
                                                                                 ),
                                                      'contentobject_id' => array( 'name'              => 'ContentObjectID',
                                                                                   'datatype'          => 'integer',
                                                                                   'default'           => 0,
                                                                                   'required'          => true,
                                                                                   'foreign_class'     => 'eZContentObject',
                                                                                   'foreign_attribute' => 'id',
                                                                                   'multiplicity'      => '1..*'
                                                                                 ),
                                                      'created'          => array( 'name'     => 'Created',
                                                                                   'datatype' => 'integer',
                                                                                   'default'  => 0,
                                                                                   'required' => true
                                                                                 ),
                                                    ),
                      'function_attributes' => array( ),
                      'keys'                => array( 'lightbox_id', 'contentobject_id' ),
                      'sort'                => array ( 'created' => 'desc' ),
                      'class_name'          => 'eZLightboxObject',
                      'name'                => 'ezlightbox_object' );
    }

    public static function create( $lightboxID, $contentObjectID )
    {
        if ( !is_numeric( $lightboxID ) || $lightboxID <= 0 )
        {
            eZDebug::writeWarning( 'Invalid lightbox ID: ' . $lightboxID, 'eZLightboxObject::create' );
            return null;
        }
        if ( !is_numeric( $contentObjectID ) || $contentObjectID <= 0 )
        {
            eZDebug::writeWarning( 'Invalid content object ID: ' . $contentObjectID, 'eZLightboxObject::create' );
            return null;
        }
        $lightboxObject = new eZLightboxObject( array( 'lightbox_id'      => $lightboxID,
                                                       'contentobject_id' => $contentObjectID,
                                                       'created'          => time()
                                                     )
                                              );
        if ( !is_object( $lightboxObject ) )
        {
            eZDebug::writeWarning( 'Failed to create new lightbox_access object.', 'eZLightboxObject::create' );
            $lightboxObject = null;
        }
        $http = eZHTTPTool::instance();
        $http->removeSessionVariable( eZLightbox::PREFERENCE_SESSION_HASHKEY );
        return $lightboxObject;
    }

    public static function fetch( $lightboxID, $contentObjectID, $asObject = true )
    {
        if ( !is_numeric( $lightboxID ) || $lightboxID <= 0 )
        {
            eZDebug::writeWarning( 'Invalid lightbox ID: ' . $lightboxID, 'eZLightboxObject::fetch' );
            return false;
        }
        if ( !is_numeric( $contentObjectID ) || $contentObjectID <= 0 )
        {
            eZDebug::writeWarning( 'Invalid content object ID: ' . $contentObjectID, 'eZLightboxObject::fetch' );
            return false;
        }
        $conditions = array( 'lightbox_id'      => $lightboxID,
                             'contentobject_id' => $contentObjectID
                           );
        $persistentObject = eZPersistentObject::fetchObject( eZLightboxObject::definition(),
                                                             null,
                                                             $conditions,
                                                             $asObject
                                                           );
        return $persistentObject;
    }

    public static function fetchLightboxObjectIDs( $lightboxID, $userID = false )
    {
        if ( !is_numeric( $lightboxID ) || $lightboxID <= 0 )
        {
            eZDebug::writeWarning( 'Invalid lightbox ID: ' . $lightboxID, 'eZLightboxObject::fetchLighboxObjectIDs' );
            return false;
        }
        $fields     = array( 'contentobject_id' );
        $conditions = array( 'lightbox_id' => $lightboxID );
        $rows       = eZPersistentObject::fetchObjectList( eZLightboxObject::definition(),
                                                           $fields,
                                                           $conditions,
                                                           null,
                                                           null,
                                                           false
                                                         );
        $result = array();
        foreach ( $rows as $row )
        {
            $result[] = $row['contentobject_id'];
        }
        return $result;
    }

    public static function fetchListByLightbox( $lightboxID, $asObject = true, $countOnly = false,
                                                $sortBy = false, $limit = false, $offset = false
                                              )
    {
        if ( !is_numeric( $lightboxID ) || $lightboxID <= 0 )
        {
            eZDebug::writeWarning( 'Invalid lightbox ID: ' . $lightboxID, 'eZLightboxObject::fetchListByLightbox' );
            return false;
        }
        $conditions = array( 'lightbox_id' => $lightboxID );
        if ( $countOnly )
        {
            $rows = eZPersistentObject::fetchObjectList( eZLightboxObject::definition(),
                                                         array(),
                                                         $conditions,
                                                         null,
                                                         null,
                                                         false,
                                                         false,
                                                         array( array( 'operation' => 'count( lightbox_id )',
                                                                       'name'      => 'count'
                                                                     )
                                                              )
                                                       );
            return $rows[0]['count'];
        }
        else
        {
            $limitations = null;
            if ( $offset != false && $limit != false )
            {
                $limitations = array( 'offset' => $offset,
                                      'length' => $limit
                                    );
            }
            if ( $sortBy == false )
            {
                $sortBy = null;
            }
            $objectList = eZPersistentObject::fetchObjectList( eZLightboxObject::definition(),
                                                               null,
                                                               $conditions,
                                                               $sortBy,
                                                               $limitations,
                                                               $asObject
                                                             );
            return $objectList;
        }
        return null;
    }

    public static function fetchListByContentObject( $contentObjectID, $asObject = true, $countOnly = false,
                                                     $sortBy = false, $limit = false, $offset = false
                                                   )
    {
        if ( !is_numeric( $contentObjectID ) || $contentObjectID <= 0 )
        {
            eZDebug::writeWarning( 'Invalid content object ID: ' . $contentObjectID, 'eZLightboxObject::fetchListByContentObject' );
            return false;
        }
        $conditions = array( 'contentobject_id' => $contentObjectID );
        if ( $countOnly )
        {
            $rows = eZPersistentObject::fetchObjectList( eZLightboxObject::definition(),
                                                         array(),
                                                         $conditions,
                                                         null,
                                                         null,
                                                         false,
                                                         false,
                                                         array( array( 'operation' => 'count( contentobject_id )',
                                                                       'name'      => 'count'
                                                                     )
                                                              )
                                                       );
            return $rows[0]['count'];
        }
        else
        {
            $limitations = null;
            if ( $offset != false && $limit != false )
            {
                $limitations = array( 'offset' => $offset,
                                      'length' => $limit
                                    );
            }
            if ( $sortBy == false )
            {
                $sortBy = null;
            }

            $objectList = eZPersistentObject::fetchObjectList( eZLightboxObject::definition(),
                                                               null,
                                                               $conditions,
                                                               $sortBy,
                                                               $limitations,
                                                               $asObject
                                                             );
            return $objectList;
        }
        return null;
    }

    public function purge()
    {
        $conditions = array( 'lightbox_id'      => $this->attribute( 'lightbox_id' ),
                             'contentobject_id' => $this->attribute( 'contentobject_id' ),
                             'created'          => $this->attribute( 'created' )
                           );
        $this->remove( $conditions );
        $http = eZHTTPTool::instance();
        $http->removeSessionVariable( eZLightbox::PREFERENCE_SESSION_HASHKEY );
    }

}

?>
