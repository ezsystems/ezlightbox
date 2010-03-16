<?php

require_once( 'autoload.php' );

interface ieZLightboxObjectItem
{

    public function getID();

    public function itemIDisValid( $itemID );

}

class eZLightboxObjectItem
{

    private static $definition   = null;
    private static $itemNameList = null;
    private static $itemIDList   = null;

    public function __construct( $definition = null )
    {
        if ( $definition !== null && is_array( $definition ) )
        {
            if ( !isset( $definition['function_attributes'] ) )
            {
                $definition['function_attributes'] = array( 'id' => 'getID' );
            }
            else
            {
                $definition['function_attributes']['id'] = 'getID';
            }
            eZLightboxObjectItem::$definition = $definition;
        }
        else
        {
            eZLightboxObjectItem::$definition = array( 'function_attributes' => array( 'id' => 'getID' ) );
        }
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

    public static function fetch( $itemID )
    {
        $itemObjectList = eZLightboxObjectItem::itemsByID();
        if ( isset( $itemObjectList[ $itemID ] ) )
        {
            return $itemObjectList[ $itemID ];
        }
        return null;
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

    private static function fetchItems()
    {
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
                            $itemID = $lightboxItemObject->getID();
                            if ( !is_int( $itemID ) )
                            {
                                eZDebug::writeWarning( 'The ID of an item must be an integer value but not ' . $itemID . '.', 'eZLightboxObjectItem::lightboxobject' );
                            }
                            elseif ( isset( eZLightboxObjectItem::$itemIDList[ $itemID ] ) )
                            {
                                eZDebug::writeWarning( 'There is already a lightbox item that uses the ID ' . $itemID . '.', 'eZLightboxObjectItem::lightboxobject' );
                            }
                            else
                            {
                                eZLightboxObjectItem::$itemNameList[ strtolower( $itemName ) ] = $lightboxItemObject;
                                eZLightboxObjectItem::$itemIDList[ $itemID ]                   = $lightboxItemObject;
                            }
                        }
                        else
                        {
                            eZDebug::writeWarning( 'No object of the class ' . $className . ' can be instantiated.', 'eZLightboxObjectItem::lightboxobject' );
                        }
                    }
                    else
                    {
                        eZDebug::writeWarning( 'The class ' . $className . ' for the lightbox object item can not be found.', 'eZLightboxObjectItem::lightboxobject' );
                    }
                }
            }
            else
            {
                eZDebug::writeWarning( 'There are no available lightbox object items.', 'eZLightboxObjectItem::lightboxobject' );
            }
        }
        else
        {
            eZDebug::writeWarning( 'The lightbox object items are not configured.', 'eZLightboxObjectItem::lightboxobject' );
        }
    }

}

?>