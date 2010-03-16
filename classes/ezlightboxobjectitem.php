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

interface ieZLightboxObjectItem
{

    public function getID();

    public function fetchItemObjectById( $itemID );

    public function itemIDisValid( $itemID );

    public function itemObjectIsValid( $itemObject );

    public function cleanUp();

    public function getPermissionFunctionListByName( $functionName );

    public function policyMatchItemObject( $policy, $itemObject );

}

class eZLightboxObjectItem
{

    private static $definition        = null,
                   $itemNameList      = null,
                   $itemIDList        = null,
                   $itemList          = null,
                   $itemIDAndNameList = null,
                   $itemsFetched      = false;

    protected $itemType = '';

    protected function __construct( $definition = null )
    {
        if ( $definition !== null && is_array( $definition ) )
        {
            if ( !isset( $definition['function_attributes'] ) )
            {
                $definition['function_attributes'] = array( 'id'   => 'getID',
                                                            'type' => 'getItemType'
                                                          );
            }
            else
            {
                $definition['function_attributes']['id']   = 'getID';
                $definition['function_attributes']['type'] = 'getItemType';
            }
            eZLightboxObjectItem::$definition = $definition;
        }
        else
        {
            eZLightboxObjectItem::$definition = array( 'function_attributes' => array( 'id'   => 'getID',
                                                                                       'type' => 'getItemType'
                                                                                     )
                                                     );
        }
    }

    public static function cleanUpAllItems()
    {
        $result = array();
        foreach ( eZLightboxObjectItem::itemsByName() as $itemName => $item )
        {
            $result[$itemName] = $item->cleanUp();
        }
        return $result;
    }

    public static function definition()
    {
        return eZLightboxObjectItem::$definition;
    }

    public function attributes()
    {
        return array_keys( eZLightboxObjectItem::$definition['function_attributes'] );
    }

    public function hasAttribute( $attribute )
    {
        return isset( eZLightboxObjectItem::$definition['function_attributes'][ $attribute ] );
    }

    public function attribute( $attribute )
    {
        if ( isset( eZLightboxObjectItem::$definition['function_attributes'][ $attribute ] ) )
        {
            $functionName = eZLightboxObjectItem::$definition['function_attributes'][ $attribute ];
            return $this->$functionName();
        }
        return false;
    }

    public function getItemType()
    {
        return $this->itemType;
    }

    public static function fetch( $itemID )
    {
        return eZLightboxObjectItem::fetchByID( $itemID );
    }

    public static function fetchByName( $itemName )
    {
        $itemObjectList = eZLightboxObjectItem::itemsByName();
        $itemName = strtolower( $itemName );
        if ( isset( $itemObjectList[ $itemName ] ) )
        {
            return $itemObjectList[ $itemName ];
        }
        return null;
    }

    public static function fetchByID( $itemID )
    {
        $itemObjectList = eZLightboxObjectItem::itemsByID();
        if ( isset( $itemObjectList[ $itemID ] ) )
        {
            return $itemObjectList[ $itemID ];
        }
        return null;
    }

    public static function itemsByIDAndType()
    {
        if ( eZLightboxObjectItem::$itemIDAndNameList === null )
        {
            eZLightboxObjectItem::fetchItems();
        }
        return eZLightboxObjectItem::$itemIDAndNameList;
    }

    public static function itemsByName()
    {
        if ( eZLightboxObjectItem::$itemNameList === null )
        {
            eZLightboxObjectItem::fetchItems();
        }
        return eZLightboxObjectItem::$itemNameList;
    }

    public static function itemsByID()
    {
        if ( eZLightboxObjectItem::$itemIDList === null )
        {
            eZLightboxObjectItem::fetchItems();
        }
        return eZLightboxObjectItem::$itemIDList;
    }

    public static function items()
    {
        if ( eZLightboxObjectItem::$itemList === null )
        {
            eZLightboxObjectItem::fetchItems();
        }
        return eZLightboxObjectItem::$itemList;
    }

    private static function fetchItems()
    {
        if ( eZLightboxObjectItem::$itemsFetched === true )
        {
            return;
        }
        $lightboxINI = eZINI::instance( 'lightbox.ini' );
        if ( $lightboxINI->hasVariable( 'LightboxItemSettings', 'AvailableItemList' ) )
        {
            $lightboxItemList = $lightboxINI->variable( 'LightboxItemSettings', 'AvailableItemList' );
            if ( count( $lightboxItemList ) > 0 )
            {
                eZLightboxObjectItem::$itemNameList = array();
                eZLightboxObjectItem::$itemIDList   = array();
                foreach ( $lightboxItemList as $index => $itemName )
                {
                    $className = $itemName . 'LightboxObjectItem';
                    if ( class_exists( $className ) )
                    {
                        $lightboxItemObject = new $className();
                        if ( is_object( $lightboxItemObject ) )
                        {
                            $itemID                       = $lightboxItemObject->getID();
                            $lightboxItemObject->itemType = $itemName;
                            if ( !is_int( $itemID ) )
                            {
                                eZDebug::writeWarning( 'The ID of an item must be an integer value but not ' . $itemID . '.', __METHOD__ );
                            }
                            elseif ( isset( eZLightboxObjectItem::$itemIDList[ $itemID ] ) )
                            {
                                eZDebug::writeWarning( 'There is already a lightbox item that uses the ID ' . $itemID . '.', __METHOD__ );
                            }
                            else
                            {
                                eZLightboxObjectItem::$itemNameList[ strtolower( $itemName ) ] = $lightboxItemObject;
                                eZLightboxObjectItem::$itemIDList[ $itemID ]                   = $lightboxItemObject;
                                eZLightboxObjectItem::$itemList[]                              = $lightboxItemObject;
                                eZLightboxObjectItem::$itemIDAndNameList[]                     = array( 'id'   => $lightboxItemObject->getID(),
                                                                                                        'name' => $lightboxItemObject->getItemType()
                                                                                                      );
                            }
                        }
                        else
                        {
                            eZDebug::writeWarning( 'No object of the class ' . $className . ' can be instantiated.', __METHOD__ );
                        }
                    }
                    else
                    {
                        eZDebug::writeWarning( 'The class ' . $className . ' for the lightbox object item can not be found.', __METHOD__ );
                    }
                }
            }
            else
            {
                eZDebug::writeWarning( 'There are no available lightbox object items.', __METHOD__ );
            }
        }
        else
        {
            eZDebug::writeWarning( 'The lightbox object items are not configured.', __METHOD__ );
        }
        eZLightboxObjectItem::$itemsFetched = true;
    }

}

?>
