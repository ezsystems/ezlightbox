<?php
//
// Created on: <25-Aug-2007 11:11:11 dis>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// COPYRIGHT NOTICE: Copyright (C) 1999-2006 eZ systems AS
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
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

require_once( 'autoload.php' );

class eZLightbox extends eZPersistentObject
{

    const ERROR_NOT_FOUND        = 1;
    const ERROR_ACCESS_DENIED    = 2;
    const ERROR_OPERATION_FAILED = 3;

    const PREFERENCE_CURRENT_LIGHTBOX = 'currentLightboxID';
    const PREFERENCE_SESSION_HASHKEY  = 'lightboxSessionKey';

    private static $__lightbox_cache      = array( 'object' => array(), 'array' => array() ),
                   $__function_access_map = array( 'view'  => eZLightboxAccess::VIEW,
                                                   'edit'  => eZLightboxAccess::EDIT,
                                                   'grant' => eZLightboxAccess::GRANT
                                                 );


    private $__permissions = array();

    function eZLightbox( $row = array() )
    {
        $this->eZPersistentObject( $row );
    }

    function checkLimitations( $viewFunction, $persistentObject )
    {
        if ( !$persistentObject )
        {
            ezDebug::writeWarning( 'Invalid object reference.', 'eZLightbox::checkLimitations' );
            return false;
        }

        $user = eZUser::currentUser();

        if ( !is_object( $user ) )
        {
            ezDebug::writeWarning( 'Failed to fetch user object.', 'eZLightbox::checkLimitations' );
            return false;
        }

        $accessResults = $user->hasAccessTo( 'lightbox' , $viewFunction );

        if ( !$accessResults )
        {
            eZDebug::writeWarning( 'Failed to get access information.',
                                   'eZLightbox::checkLimitations' );
            return false;
        }
        if ( array_key_exists( 'accessWord', $accessResults ) )
        {
            if ( $accessResults['accessWord'] == 'no' )
            {
                $persistentObject->setPermission( $viewFunction, false );
            }
            else if ( $accessResults['accessWord'] == 'yes' )
            {
                $persistentObject->setPermission( $viewFunction, true );
            }
            else
            {
                $persistentObject->setPermission( $viewFunction, false );
                if ( array_key_exists( 'policies', $accessResults ) )
                {
                    $persistentObject->getLimitations( $viewFunction, $accessResults['policies'], $user );
                }
                else
                {
                    eZDebug::writeWarning( 'Failed to get policies.',
                                           'eZLightbox::checkLimitations' );
                    return false;
                }
            }
        }
        else
        {
            ezDebug::writeWarning( 'No access word in access results.', 'eZLightbox::checkLimitations' );
            return false;
        }
        return true;
    }

    public static function definition()
    {
        return array( 'fields'              => array( 'id'           => array( 'name'     => 'ID',
                                                                               'datatype' => 'integer',
                                                                               'default'  => 0,
                                                                               'required' => true
                                                                             ),
                                                      'owner_id'     => array( 'name'              => 'Owner',
                                                                               'datatype'          => 'integer',
                                                                               'default'           => 0,
                                                                               'required'          => true,
                                                                               'foreign_class'     => 'eZUser',
                                                                               'foreign_attribute' => 'id',
                                                                               'multiplicity'      => '1..*'
                                                                             ),
                                                      'created'      => array( 'name'     => 'Created',
                                                                               'datatype' => 'integer',
                                                                               'default'  => 0,
                                                                               'required' => true
                                                                             ),
                                                      'name'         => array( 'name'     => 'Name',
                                                                               'datatype' => 'string',
                                                                               'default'  => '',
                                                                               'required' => true
                                                                             ),
                                                      'external_id'  => array( 'name'     => 'ExternalID',
                                                                               'datatype' => 'string',
                                                                               'default'  => '',
                                                                               'required' => false
                                                                             )
                                                    ),
                      'function_attributes' => array( 'is_owner'           => 'isOwner',
                                                      'owner'              => 'owner',
                                                      'item_count'         => 'fetchItemListCount',
                                                      'itemlist'           => 'fetchItemList',
                                                      'object_id_list'     => 'fetchObjectIDList',
                                                      'can_edit'           => 'canEdit',
                                                      'can_view'           => 'canView',
                                                      'can_send'           => 'canSend',
                                                      'can_grant'          => 'canGrant',
                                                      'can_add_class_list' => 'canAddClassList',
                                                      'access_list'        => 'fetchAccessList',
                                                      'access_keys'        => 'fetchAccessKeys'
                                                    ),
                      'keys'                => array( 'id' ),
                      'increment_key'       => 'id',
                      'sort'                => array ( 'name' => 'desc' ),
                      'class_name'          => 'eZLightbox',
                      'name'                => 'ezlightbox' );
    }

    public static function createSessionKey( $userID )
    {
        $user        = eZUser::fetch( $userID );
        $hashArray   = array( $userID );
        $hashArray[] = eZPreferences::value( eZLightbox::PREFERENCE_CURRENT_LIGHTBOX );

        // AB: Problem, what happens if there are some limitations?
        $accessArray = $user->hasAccessTo( 'lightbox', 'create' );
        if ( isset( $accessArray['accessWord'] ) )
        {
            $hashArray[] = $accessArray['accessWord'];
        }
        $lightboxes   = eZLightbox::fetchListByUser( $userID );
        foreach ( $lightboxes as $lightbox )
        {
            $hashArray[] = $lightbox->attribute( 'name' ) . '_' . $lightbox->fetchItemListCount();
        }
        $hashString = implode( ',', $hashArray );
        $hashString = hash( 'md5', $hashString );
        $http = eZHTTPTool::instance();
        $http->setSessionVariable( eZLightbox::PREFERENCE_SESSION_HASHKEY, $hashString );
        return $hashString;
    }

    public static function canCreate()
    {
        $allowed    = false;
        $userObject = eZUser::currentUser();
        if ( is_object( $userObject ) )
        {
            $accessResults = $userObject->hasAccessTo( 'lightbox' , 'create' );
            if ( !$accessResults || !array_key_exists( 'accessWord', $accessResults ) )
            {
                eZDebug::writeWarning( 'Failed to get permissions.',
                                       'eZLightbox::canCreate' );
            }
            else if ( $accessResults['accessWord'] == 'yes' )
            {
                $allowed = true;
            }
        }
        else
        {
            eZDebug::writeWarning( 'Failed to get current user.',
                                   'eZLightbox::canCreate' );
        }
        return $allowed;
    }

    public static function fetch( $id, $asObject = true )
    {
        $cacheKey = 'object';
        if ( !$asObject )
        {
            $cacheKey = 'array';
        }
        eZLightbox::$__lightbox_cache =& $GLOBALS['eZLighboxIDCache'];
        if ( isset( eZLightbox::$__lightbox_cache[ $cacheKey ][ $id ] ) )
        {
            return eZLightbox::$__lightbox_cache[ $cacheKey ][ $id ];
        }
        $conditions       = array( 'id' => array( '=', $id  ) );
        $persistentObject =  eZPersistentObject::fetchObject( eZLightbox::definition(),
                                                              null,
                                                              $conditions,
                                                              $asObject
                                                            );
        if ( (  $asObject && is_object( $persistentObject ) ) ||
             ( !$asObject && is_array( $persistentObject ) )
           )
        {
            eZLightbox::$__lightbox_cache[ $cacheKey ][ $id ] = $persistentObject;
            return $persistentObject;
        }
        eZDebug::writeError( 'Cannot fetch lightbox with ID ' . $id . '.', 'eZLightbox::fetch' );
        return false;
    }

    public static function fetchListByUser( $userID, $asObject = true, $countOnly = false, $sortBy = false,
                                            $offset = false, $limit = false
                                          )
    {
        $user_id = $userID;
        /*if ( $userID == false )
        {
            $user    = eZUser::currentUser();
            $user_id = $user->attribute( 'contentobject_id' );
        }*/
        $conditions  = array( 'owner_id' => array( '=', $user_id  ) );
        if ( $countOnly )
        {
            $rows = eZPersistentObject::fetchObjectList( eZLightbox::definition(),
                                                         array(),
                                                         $conditions,
                                                         null,
                                                         null,
                                                         false,
                                                         false,
                                                         array( array( 'operation' => 'count( id )',
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
            $rows = eZPersistentObject::fetchObjectList( eZLightbox::definition(),
                                                         null,
                                                         $conditions,
                                                         $sortBy,
                                                         $limitations,
                                                         $asObject
                                                       );
            return $rows;
        }
    }

    public function fetchItemList( $asObject = true, $offset = false, $limit = false )
    {
        return eZLightboxObject::fetchListByLightbox( $this->attribute( 'id' ), $asObject );
    }

    public function fetchItemListCount()
    {
        return eZLightboxObject::fetchListByLightbox( $this->attribute( 'id' ), false, true );
    }

    public function fetchAccessList( $asObject = true, $offset = false, $limit = false )
    {
        return eZLightboxAccess::fetchListByLightbox( $this->attribute( 'id' ), $asObject );
    }

    public function fetchAccessKeys()
    {
        return eZLightboxAccess::accessKeys();
    }

    public function fetchObjectIDList()
    {
        return eZLightboxObject::fetchLightboxObjectIDs( $this->attribute( 'id' ) );
    }

    public static function create( $name, $userID = false )
    {
        $user_id = $userID;
        if ( $userID == false )
        {
            $user    = eZUser::currentUser();
            $user_id = $user->attribute( 'contentobject_id' );
        }

        $lightboxObject = new eZLightbox( array( 'name'     => $name,
                                                 'owner_id' => $user_id,
                                                 'created'  => time()
                                               )
                                        );
        if ( is_object( $lightboxObject ) )
        {
            $http = eZHTTPTool::instance();
            $http->removeSessionVariable( eZLightbox::PREFERENCE_SESSION_HASHKEY );
            $db = eZDB::instance();
            $db->begin();
            $lightboxObject->store();
            $db->commit();
        }
        else
        {
            $lightboxObject = null;
        }
        return $lightboxObject;
    }

    public function lightboxContains( $objectID )
    {
        $object = eZLightboxObject::fetch( $this->attribute( 'id' ), $objectID );
        if ( is_object( $object ) )
        {
            return $object;
        }
        return null;
    }

    public function addToLightbox( $objectID )
    {
        $newObject = $this->lightboxContains( $objectID  );
        if ( !is_object( $newObject ) )
        {
            $db = eZDB::instance();
            $db->begin();
            $newObject = eZLightboxObject::create( $this->attribute( 'id' ), $objectID );
            if ( is_object( $newObject ) )
            {
                $http = eZHTTPTool::instance();
                $http->removeSessionVariable( eZLightbox::PREFERENCE_SESSION_HASHKEY );
                $newObject->store();
                $db->commit();
            }
            else
            {
                $db->commit();
                $newObject = null;
            }
        }
        return $newObject;
    }

    public function removeFromLightbox( $objectID )
    {
        $object = $this->lightboxContains( $objectID  );
        if ( is_object( $object ) )
        {
            $http = eZHTTPTool::instance();
            $http->removeSessionVariable( eZLightbox::PREFERENCE_SESSION_HASHKEY );
            $db = eZDB::instance();
            $db->begin();
            $object->purge();
            $db->commit();
            return true;
        }
        else
        {
            eZDebug::writeWarning( 'This lightbox does not contain an object with ID ' . $objectID,
                                   'eZLightbox::removeFromLightbox'
                                 );
        }
        return false;
    }

    public function purge()
    {
        $conditions = array( 'id'          => $this->attribute( 'id'          ),
                             'owner_id'    => $this->attribute( 'owner_id'    ),
                             'created'     => $this->attribute( 'created'     ),
                             'name'        => $this->attribute( 'name'        ),
                             'external_id' => $this->attribute( 'external_id' )
                           );
        $lightboxObjectList = eZLightboxObject::fetchListByLightbox( $this->attribute( 'id' ) );
        $lightboxAccessList = eZLightboxAccess::fetchListByLightbox( $this->attribute( 'id' ) );
        if ( is_array( $lightboxObjectList ) )
        {
            foreach ( $lightboxObjectList as $key => $object )
            {
                if ( is_object( $object ) )
                {
                    $object->purge();
                }
            }
        }
        if ( is_array( $lightboxAccessList ) )
        {
            foreach ( $lightboxAccessList as $key => $object )
            {
                if ( is_object( $object ) )
                {
                    $object->purge();
                }
            }
        }
        $this->remove( $conditions );
        $http = eZHTTPTool::instance();
        $http->removeSessionVariable( eZLightbox::PREFERENCE_SESSION_HASHKEY );
    }

    public function owner()
    {
        $userObject = eZUser::fetch( $this->attribute( 'owner_id' ) );
        return $userObject;
    }

    public function isOwner()
    {
        $userObject = eZUser::currentUser();
        if ( !is_object( $userObject ) )
        {
            eZDebug::writeWarning( 'Failed to fetch current user.',
                                   'eZLightbox::isOwner'
                                 );
            return false;
        }
        $isOwner = ( $this->attribute( 'owner_id' ) == $userObject->attribute( 'contentobject_id' ) );
        $this->__permissions['is_owner'] = $isOwner;
        return $isOwner;
    }

    public function canEdit()
    {
        // eZ Publish permission system:
        $result = false;
        if ( isset( $this->__permissions['can_edit'] ) )
        {
            $result = $this->__permissions['can_edit'];
        }
        else if ( eZLightbox::checkLimitations( 'edit', $this ) )
        {
            $result = ( $this->__permissions['can_edit'] == true );
        }
        if ( $result )
        {
            // Lightbox permission system:
            if ( ( isset( $this->__permissions['is_owner'] ) && $this->__permissions['is_owner'] ) ||
                 $this->isOwner()
               )
            {
                return true;
            }
            return $this->currentUserAccess( eZLightboxAccess::EDIT );
        }
        return false;
    }

    public function canView()
    {
        // eZ Publish permission system:
        $result = false;
        if ( isset( $this->__permissions['can_view'] ) )
        {
            $result = $this->__permissions['can_view'];
        }
        else if ( eZLightbox::checkLimitations( 'view', $this ) )
        {
            $result = ( $this->__permissions['can_view'] == true );
        }
        if ( $result )
        {
            // Lightbox permission system:
            if ( ( isset( $this->__permissions['is_owner'] ) && $this->__permissions['is_owner'] ) ||
                 $this->isOwner()
               )
            {
                return true;
            }
            return $this->currentUserAccess( eZLightboxAccess::VIEW );
        }
        return false;
    }

    public function canSend()
    {
        // eZ Publish permission system:
        $result = false;
        if ( isset( $this->__permissions['can_send'] ) )
        {
            $result = $this->__permissions['can_send'];
        }
        else if ( eZLightbox::checkLimitations( 'send', $this ) )
        {
            $result = ( $this->__permissions['can_send'] == true );
        }
        return $result;
    }

    public function canGrant()
    {
        // eZ Publish permission system:
        $result = false;
        if ( isset( $this->__permissions['can_grant'] ) )
        {
            $result = $this->__permissions['can_grant'];
        }
        else if ( eZLightbox::checkLimitations( 'grant', $this ) )
        {
            $result = ( $this->__permissions['can_grant'] == true );
        }
        return $result;
        //return $this->currentUserAccess( eZLightboxAccess::GRANT );
    }

    public function canAddClassList()
    {
        $result = false;
        if ( isset( $this->__permissions['can_add'] ) )
        {
            $result = $this->__permissions['can_add'];
        }
        else if ( eZLightbox::checkLimitations( 'add', $this ) )
        {
            $result = $this->__permissions['can_add'];
        }
        return $result;
    }

    private function getLimitations( $viewFunction, $policies, $user )
    {
        $lightboxAccess = eZLightboxAccess::fetch( $this->attribute( 'id' ),
                                                   $user->attribute( 'contentobject_id' ),
                                                   false
                                                 );
        foreach ( array_keys( $policies ) as $policy_key  )
        {
            $has_class_limit = false;
            $is_class_ok     = false;
            $has_owner_limit = false;
            $is_owner_ok     = false;
            $limitationArray = $policies[ $policy_key ];
            foreach ( array_keys( $limitationArray ) as $limitation )
            {
                switch ( $limitation )
                {
                    case 'Class':
                        $has_class_limit = true;
                        $is_class_ok     = $limitationArray[ $limitation ];
                    case 'Owner':
                        $has_owner_limit = true;
                        if ( in_array( 0, $limitationArray[ $limitation ] ) )
                        {
                            $is_owner_ok = true;
                        }
                        else if ( in_array( 1, $limitationArray[ $limitation ] ) )
                        {
                            if ( $this->attribute( 'owner_id' ) == $user->attribute( 'contentobject_id' ) )
                            {
                                $is_owner_ok = true;
                            }
                        }
                        else if ( in_array( 2, $limitationArray[ $limitation ] ) )
                        {
                            $is_owner_ok = 'granted';
                        }
                        break;
                }
            }
            switch ( $viewFunction )
            {
                case 'add':
                    if ( $has_class_limit )
                    {
                        $this->setPermission( $viewFunction, $is_class_ok );
                    }
                    break;
                case 'edit':
                case 'view':
                case 'send':
                case 'grant':
                    if ( $has_owner_limit && $is_owner_ok )
                    {
                        $this->setPermission( $viewFunction, $is_owner_ok );
                    }
                    else if ( isset( $lightboxAccess['access_mask'] ) &&
                              isset( eZLightbox::$__function_access_map[ $viewFunction ] )
                            )
                    {
                        $permission = ( $lightboxAccess['access_mask'] & eZLightbox::$__function_access_map[ $viewFunction ] ) == true;
                        $this->setPermission( $viewFunction, $permission );
                    }
                    break;
                default:
                    eZDebug::writeWarning( 'Unknown view function: ' . $viewFunction,
                                           'eZLightbox::getLimitations' );
                    break;
            }
        }
    }

    private function setPermission( $viewFunction, $limitation )
    {
        switch ( $viewFunction )
        {
            case 'add':
                    if ( $limitation === true )
                    {
                        $result = array();
                        $classIDList = eZContentClass::fetchList(
                            0, false, false, array( 'name' => 'asc' ), array( 'id' )
                        );
                        foreach ( $classIDList as $key => $row )
                        {
                            $result[] = $row['id'];
                        }
                        $this->__permissions['can_add'] = $result;
                    }
                    else
                    {
                        $this->__permissions['can_add']   = $limitation;
                    }
                break;
            case 'edit':
                    $this->__permissions['can_edit']  = $limitation;
                break;
            case 'view':
                    $this->__permissions['can_view']  = $limitation;
                break;
            case 'send':
                    $this->__permissions['can_send']  = $limitation;
                break;
            case 'grant':
                    $this->__permissions['can_grant'] = $limitation;
                break;
            default:
                eZDebug::writeWarning( 'Unknown view function: ' . $viewFunction,
                                       'eZLightbox::setPermission' );
                break;
        }
    }

    private function currentUserAccess( $accessType )
    {
        $userObject = eZUser::currentUser();
        if ( !is_object( $userObject ) )
        {
            eZDebug::writeWarning( 'Failed to fetch current user.',
                                   'eZLightbox::currentUserAccess'
                                 );
            return false;
        }
        $lightboxAccessObject = eZLightboxAccess::fetch( $this->attribute( 'id' ),
                                                         $userObject->attribute( 'contentobject_id' )
                                                       );
        if ( !is_object( $lightboxAccessObject ) )
        {
            eZDebug::writeWarning( 'Failed to fetch access object for lightbox ID ' . $this->attribute( 'id' ) . ' and user ID ' . $userObject->attribute( 'contentobject_id' ),
                                   'eZLightbox::currentUserAccess'
                                 );
            return false;
        }
        return $lightboxAccessObject->checkAccess( $accessType );
    }
}

?>
