<?php
//
// Created on: <2007-11-21 13:01:28 ab>
//
// SOFTWARE NAME: eZ Lightbox extension for eZ Publish
// SOFTWARE RELEASE: 0.x
// COPYRIGHT NOTICE: Copyright (C) 1999-2010 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//

require_once( 'autoload.php' );

class eZLightboxObject extends eZPersistentObject
{

    const TYPE_OBJECT_ID = 1;
    const TYPE_NODE_ID   = 2;

    function eZLightboxObject( $row = array() )
    {
        $this->eZPersistentObject( $row );
    }

    public static function definition()
    {
        return array( 'fields'              => array( 'lightbox_id' => array( 'name'              => 'LightboxID',
                                                                              'datatype'          => 'integer',
                                                                              'default'           => 0,
                                                                              'required'          => true,
                                                                              'foreign_class'     => 'eZLightbox',
                                                                              'foreign_attribute' => 'id',
                                                                              'multiplicity'      => '1..*'
                                                                            ),
                                                      'item_id'     => array( 'name'              => 'ItemID',
                                                                              'datatype'          => 'integer',
                                                                              'default'           => 0,
                                                                              'required'          => true
                                                                            ),
                                                      'created'     => array( 'name'              => 'Created',
                                                                              'datatype'          => 'integer',
                                                                              'default'           => 0,
                                                                              'required'          => true
                                                                            ),
                                                      'type_id'     => array( 'name'              => 'TypeID',
                                                                              'datatype'          => 'integer',
                                                                              'default'           => 1,
                                                                              'required'          => true
                                                                            ),
                                                      'priority'    => array( 'name'              => 'Priority',
                                                                              'datatype'          => 'integer',
                                                                              'default'           => 0,
                                                                              'required'          => false
                                                                            )
                                                    ),
                      'function_attributes' => array( 'available_items' => 'availableItems',
                                                      'item_type'       => 'itemType'
                                                    ),
                      'keys'                => array( 'lightbox_id', 'item_id', 'type_id' ),
                      'sort'                => array( 'priority' => 'asc', 'created' => 'desc' ),
                      'class_name'          => 'eZLightboxObject',
                      'name'                => 'ezlightbox_object'
                    );
    }

    public static function create( $lightboxID, $itemID, $typeID, $priority = 0 )
    {
        if ( !is_numeric( $lightboxID ) || $lightboxID <= 0 )
        {
            eZDebug::writeWarning( 'Invalid lightbox ID: ' . $lightboxID, 'eZLightboxObject::create' );
            return null;
        }
        $lightboxItemObject = eZLightboxObjectItem::fetch( $typeID );
        if ( !is_object( $lightboxItemObject ) )
        {
            eZDebug::writeWarning( 'Invalid type ID: ' . $typeID, 'eZLightboxObject::create' );
            return null;
        }
        if ( !$lightboxItemObject->itemIDisValid( $itemID ) )
        {
            eZDebug::writeWarning( 'The item ID ' . $itemID . ' is invalid for item of type ' . $typeID, 'eZLightboxObject::create' );
            return null;
        }
        $lightboxObject = new eZLightboxObject( array( 'lightbox_id' => $lightboxID,
                                                       'item_id'     => $itemID,
                                                       'created'     => time(),
                                                       'type_id'     => $typeID,
                                                       'priority'    => $priority
                                                     )
                                              );
        if ( !is_object( $lightboxObject ) )
        {
            eZDebug::writeWarning( 'Failed to create new lightbox_access object.', 'eZLightboxObject::create' );
            $lightboxObject = null;
        }
        if ( isset( $_SESSION ) )
        {
            $http = eZHTTPTool::instance();
            $http->removeSessionVariable( eZLightbox::PREFERENCE_SESSION_HASHKEY );
        }
        return $lightboxObject;
    }

    public static function fetch( $lightboxID, $itemID, $typeID, $asObject = true )
    {
        if ( !is_numeric( $lightboxID ) || $lightboxID <= 0 )
        {
            eZDebug::writeWarning( 'Invalid lightbox ID: ' . $lightboxID, 'eZLightboxObject::fetch' );
            return false;
        }
        $lightboxItemObject = eZLightboxObjectItem::fetch( $typeID );
        if ( !is_object( $lightboxItemObject ) )
        {
            eZDebug::writeWarning( 'Invalid type ID: ' . $typeID, 'eZLightboxObject::create' );
            return false;
        }
        if ( !$lightboxItemObject->itemIDisValid( $itemID ) )
        {
            eZDebug::writeWarning( 'The item ID ' . $itemID . ' is invalid for item of type ' . $typeID, 'eZLightboxObject::create' );
            return false;
        }
        $conditions = array( 'lightbox_id' => $lightboxID,
                             'item_id'     => $itemID,
                             'type_id'     => $typeID
                           );
        $persistentObject = eZPersistentObject::fetchObject( eZLightboxObject::definition(),
                                                             null,
                                                             $conditions,
                                                             $asObject
                                                           );
        return $persistentObject;
    }

    public static function fetchLightboxItemIDs( $lightboxID, $typeID = false )
    {
        if ( !is_numeric( $lightboxID ) || $lightboxID <= 0 )
        {
            eZDebug::writeWarning( 'Invalid lightbox ID: ' . $lightboxID, 'eZLightboxObject::fetchLighboxItemIDs' );
            return false;
        }
        $fields     = array( 'item_id', 'type_id' );
        $conditions = array( 'lightbox_id' => $lightboxID );
        if ( $typeID !== false )
        {
            $lightboxItemObject = eZLightboxObjectItem::fetch( $typeID );
            if ( !is_object( $lightboxItemObject ) )
            {
                eZDebug::writeWarning( 'Invalid type ID: ' . $typeID, 'eZLightboxObject::create' );
                return false;
            }
            $conditions['type_id'] = $typeID;
        }
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
            $result[ $row['item_id'] ] = $row['type_id'];
        }
        return $result;
    }

    public static function fetchListByLightbox( $lightboxID, $typeID = false, $asObject = true, $countOnly = false,
                                                $sortBy = false, $limit = false, $offset = false
                                              )
    {
        if ( !is_numeric( $lightboxID ) || $lightboxID <= 0 )
        {
            eZDebug::writeWarning( 'Invalid lightbox ID: ' . $lightboxID, 'eZLightboxObject::fetchListByLightbox' );
            return null;
        }
        $conditions = array( 'lightbox_id' => $lightboxID );
        if ( $typeID !== false )
        {
            $lightboxItemObject = eZLightboxObjectItem::fetch( $typeID );
            if ( !is_object( $lightboxItemObject ) )
            {
                eZDebug::writeWarning( 'Invalid type ID: ' . $typeID, 'eZLightboxObject::create' );
                return null;
            }
            $conditions['type_id'] = $typeID;
        }
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

    public static function fetchListByItem( $itemID, $typeID = false, $asObject = true, $countOnly = false,
                                            $sortBy = false, $limit = false, $offset = false
                                          )
    {
        if ( $typeID !== false )
        {
            $lightboxItemObject = eZLightboxObjectItem::fetch( $typeID );
            if ( !is_object( $lightboxItemObject ) )
            {
                eZDebug::writeWarning( 'Invalid type ID: ' . $typeID, 'eZLightboxObject::create' );
                return null;
            }
            if ( !$lightboxItemObject->itemIDisValid( $itemID ) )
            {
                eZDebug::writeWarning( 'The item ID ' . $itemID . ' is invalid for item of type ' . $typeID, 'eZLightboxObject::create' );
                return null;
            }
        }
        $conditions = array( 'item_id' => $itemID );
        if ( $typeID !== false )
        {
            $conditions['type_id'] = $typeID;
        }
        if ( $countOnly )
        {
            $rows = eZPersistentObject::fetchObjectList( eZLightboxObject::definition(),
                                                         array(),
                                                         $conditions,
                                                         null,
                                                         null,
                                                         false,
                                                         false,
                                                         array( array( 'operation' => 'count( item_id )',
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

    public function availableItems()
    {
        return eZLightboxObjectItem::items();
    }

    public function itemType()
    {
        return eZLightboxObjectItem::fetch( $this->attribute( 'type_id' ) );
    }

    public function purge()
    {
        $conditions = array( 'lightbox_id' => $this->attribute( 'lightbox_id' ),
                             'item_id'     => $this->attribute( 'item_id' ),
                             'type_id'     => $this->attribute( 'type_id' ),
                             'created'     => $this->attribute( 'created' )
                           );
        $this->remove( $conditions );
        if ( isset( $_SESSION ) )
        {
            $http = eZHTTPTool::instance();
            $http->removeSessionVariable( eZLightbox::PREFERENCE_SESSION_HASHKEY );
        }
    }

}

?>
