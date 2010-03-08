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
require_once( 'kernel/common/i18n.php' );

class eZLightboxAccess extends eZPersistentObject
{

    const VIEW  = 1,
          EDIT  = 2,
          GRANT = 4,
          ADD   = 8,
          SEND  = 16,
          MAX_ACCESS_VALUE = 32; // Sum of all the above +1

    function eZLightboxAccess( $row = array() )
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
                                                      'user_id'     => array( 'name'              => 'UserID',
                                                                              'datatype'          => 'integer',
                                                                              'default'           => 0,
                                                                              'required'          => true,
                                                                              'foreign_class'     => 'eZUser',
                                                                              'foreign_attribute' => 'id',
                                                                              'multiplicity'      => '1..*'
                                                                            ),
                                                      'created'     => array( 'name'     => 'Created',
                                                                              'datatype' => 'integer',
                                                                              'default'  => 0,
                                                                              'required' => true
                                                                            ),
                                                      'access_mask' => array( 'name'     => 'AccessMask',
                                                                              'datatype' => 'integer',
                                                                              'default'  => 0,
                                                                              'required' => true
                                                                            ),
                                                    ),
                      'function_attributes' => array( 'user'        => 'fetchUser',
                                                      'owner'       => 'fetchOwner',
                                                      'flags'       => 'flags',
                                                      'access_keys' => 'accessKeys',
                                                      'lightbox'    => 'fetchLightbox'
                                                    ),
                      'keys'                => array( 'lightbox_id', 'user_id' ),
                      'sort'                => array( 'created' => 'desc' ),
                      'class_name'          => 'eZLightboxAccess',
                      'name'                => 'ezlightbox_access' );
    }

    public static function accessKeys()
    {
        return array( eZLightboxAccess::VIEW  => ezi18n( 'class/ezlightboxaccess/accessKeys', 'View'  ),
                      eZLightboxAccess::EDIT  => ezi18n( 'class/ezlightboxaccess/accessKeys', 'Edit'  ),
                      eZLightboxAccess::GRANT => ezi18n( 'class/ezlightboxaccess/accessKeys', 'Grant' ),
                      eZLightboxAccess::ADD   => ezi18n( 'class/ezlightboxaccess/accessKeys', 'Add'   ),
                      eZLightboxAccess::SEND  => ezi18n( 'class/ezlightboxaccess/accessKeys', 'Send'  )
                    );
    }

    public static function accessKeyByName( $accessKeyName )
    {
        switch ( strtolower( trim( $accessKeyName ) ) )
        {
            case 'view':
                return eZLightboxAccess::VIEW;
            case 'edit':
                return eZLightboxAccess::EDIT;
            case 'grant':
                return eZLightboxAccess::GRANT;
            case 'add':
                return eZLightboxAccess::ADD;
            case 'send':
                return eZLightboxAccess::SEND;
            default:
                eZDebug::writeWarning( 'Unkown access key name "' . $accessKeyName . '". Should be one of "view", "edit" or "grant".' );
                return null;
        }
    }

    public static function accessKeyFlags()
    {   // ToDo: Add "SEND" here
        return array( eZLightboxAccess::VIEW  => 'v',
                      eZLightboxAccess::EDIT  => 'e',
                      eZLightboxAccess::GRANT => 'g',
                      eZLightboxAccess::ADD   => 'a',
                      eZLightboxAccess::SEND  => 's'
                    );
    }

    public static function accessKeyByFlag( $accessKeyFlag )
    {
        switch ( strtolower( trim( $accessKeyFlag ) ) )
        {   // ToDo: Add "SEND" here
            case 'v':
                return eZLightboxAccess::VIEW;
            case 'e':
                return eZLightboxAccess::EDIT;
            case 'g':
                return eZLightboxAccess::GRANT;
            case 'a':
                return eZLightboxAccess::ADD;
            case 's':
                return eZLightboxAccess::SEND;
            default:
                eZDebug::writeWarning( 'Unkown access key name "' . $accessKeyFlag . '". Should be one of "v", "e" or "g".' );
                return null;
        }
    }

    public static function create( $lightboxID, $userID, $accessMask )
    {
        if ( !is_numeric( $lightboxID ) || $lightboxID <= 0 )
        {
            eZDebug::writeWarning( 'Invalid lightbox ID: ' . $lightboxID, 'eZLightboxAccess::create' );
            return null;
        }
        if ( !is_numeric( $userID ) || $userID <= 0 )
        {
            eZDebug::writeWarning( 'Invalid user ID: ' . $userID, 'eZLightboxAccess::create' );
            return null;
        }
        if ( $accessMask > 0 && $accessMask < eZLightboxAccess::MAX_ACCESS_VALUE )
        {
            $lightboxAccessObject = new eZLightboxAccess( array( 'lightbox_id' => $lightboxID,
                                                                 'user_id'     => $userID,
                                                                 'created'     => time(),
                                                                 'access_mask' => $accessMask
                                                               )
                                                        );
            if ( !is_object( $lightboxAccessObject ) )
            {
                eZDebug::writeWarning( 'Failed to create new lightbox_access object.', 'eZLightboxAccess::create' );
                $lightboxAccessObject = null;
            }
            return $lightboxAccessObject;
        }
        else
        {
            eZDebug::writeWarning( 'Invalid access mask: ' . $accessMask, 'eZLightboxAccess::create' );
        }
        return null;
    }

    public static function fetch( $lightboxID, $userID, $asObject = true )
    {
        if ( !is_numeric( $lightboxID ) || $lightboxID <= 0 )
        {
            eZDebug::writeWarning( 'Invalid lightbox ID: ' . $lightboxID, 'eZLightboxAccess::fetch' );
            return false;
        }
        if ( !is_numeric( $userID ) || $userID <= 0 )
        {
            eZDebug::writeWarning( 'Invalid user ID: ' . $userID, 'eZLightboxAccess::fetch' );
            return false;
        }
        $conditions = array( 'lightbox_id' => $lightboxID,
                             'user_id'     => $userID
                           );
        $persistentObject = eZPersistentObject::fetchObject( eZLightboxAccess::definition(),
                                                             null,
                                                             $conditions,
                                                             $asObject
                                                           );
        return $persistentObject;
    }

    public static function fetchListByLightbox( $lightboxID, $asObject = true, $countOnly = false,
                                                $sortBy = false, $limit = false, $offset = false,
                                                $userID = false
                                              )
    {
        if ( !is_numeric( $lightboxID ) || $lightboxID <= 0 )
        {
            eZDebug::writeWarning( 'Invalid lightbox ID: ' . $lightboxID, 'eZLightboxAccess::fetchByLightbox' );
            return false;
        }
        $conditions = array( 'lightbox_id' => $lightboxID );
        if ( $countOnly )
        {
            $rows = eZPersistentObject::fetchObjectList( eZLightboxAccess::definition(),
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

            $objectList = eZPersistentObject::fetchObjectList( eZLightboxAccess::definition(),
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

    public static function fetchListByUser( $userID, $asObject = true, $countOnly = false, $sortBy = false,
                                            $limit = false, $offset = false, $masks = array()
                                          )
    {
        if ( !is_numeric( $userID ) || $userID <= 0 )
        {
            eZDebug::writeWarning( 'Invalid user ID: ' . $userID, 'eZLightboxAccess::fetchByUser' );
            return false;
        }
        $custom_condition = null;
        if ( is_array( $masks ) && count( $masks ) > 0 )
        {
            $db               = eZDB::instance();
            $customConditions = array();
            foreach ( $masks as $maskValue )
            {
                if ( is_numeric( $maskValue ) )
                {
                    $bitAnd = $db->bitAnd( 'access_mask', $maskValue );
                    $customConditions[] = $bitAnd . ' = ' . $maskValue;
                }
            }
            if ( count( $customConditions ) > 0 )
            {
                $custom_condition = ' AND ( ' . implode( ' OR ', $customConditions ) . ' )';
            }
        }
        $conditions  = array( 'user_id' => $userID );
        if ( $countOnly )
        {
            $rows = eZPersistentObject::fetchObjectList( eZLightboxAccess::definition(),
                                                         array(),
                                                         $conditions,
                                                         null,
                                                         null,
                                                         false,
                                                         false,
                                                         array( array( 'operation' => 'count( user_id )',
                                                                       'name'      => 'count'
                                                                     )
                                                              ),
                                                         null,
                                                         $custom_condition
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

            $objectList = eZPersistentObject::fetchObjectList( eZLightboxAccess::definition(),
                                                               null,
                                                               $conditions,
                                                               $sortBy,
                                                               $limitations,
                                                               $asObject,
                                                               false,
                                                               false,
                                                               null,
                                                               $custom_condition
                                                             );
            return $objectList;
        }
        return null;
    }

    public function fetchLightbox()
    {
        return eZLightbox::fetch( $this->attribute( 'lightbox_id' ), false );
    }

    public function fetchUser()
    {
        $userObject = eZUser::fetch( $this->attribute( 'user_id' ) );
        return $userObject;
    }

    public function fetchOwner()
    {
        $userObject = null;
        $lightbox   = eZLightbox::fetch( $this->attribute( 'lightbox_id' ), false );
        if ( is_array( $lightbox ) && array_key_exists( 'owner_id', $lightbox ) )
        {
            $userObject = eZUser::fetch( $lightbox['owner_id'] );
        }
        return $userObject;
    }

    public function purge()
    {
        $conditions = array( 'lightbox_id' => $this->attribute( 'lightbox_id' ),
                             'user_id'     => $this->attribute( 'user_id' ),
                             'created'     => $this->attribute( 'created' ),
                             'access_mask' => $this->attribute( 'access_mask' )
                           );
        $this->remove( $conditions );
    }

    public function checkAccess( $access )
    {
        if ( $access > 0 && $access < eZLightboxAccess::MAX_ACCESS_VALUE )
        {
            return ( $this->attribute( 'access_mask' ) & $access ) == true;
        }
        else
        {
            eZDebug::writeWarning( 'Access key "' . $access . '" out of range',
                                   'eZLightboxAccess::checkAccess'
                                 );
        }
        return false;
    }

    public function flags()
    {
        $accessMask = $this->attribute( 'access_mask' );
        $flags      = array();
        foreach ( eZLightboxAccess::accessKeyFlags() as $key => $flag )
        {
            if ( ( $accessMask & $key ) == true )
            {
                $flags[$key] = $flag;
            }
        }
        return $flags;
    }

}

?>
