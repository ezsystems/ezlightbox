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

class lightboxFunctionCollection
{

    function fetchList( $userID = false, $asObject = true, $sortBy = false,
                        $offset = false, $limit = false, $otherMasks = array()
                      )
    {
        if ( !$userID )
        {
            $userID = eZUser::currentUserID();
        }
        return array( 'result' => eZLightbox::fetchListByUser( $userID, $asObject, false,
                                                               $sortBy, $offset, $limit, $otherMasks
                                                             )
                    );
    }

    function fetchListOwn( $userID = false, $asObject = true, $sortBy = false,
                           $offset = false, $limit = false
                         )
    {
        if ( !$userID )
        {
            $userID = eZUser::currentUserID();
        }
        return array( 'result' => eZLightbox::fetchOwnListByUser( $userID, $asObject, false,
                                                                  $sortBy, $limit, $offset
                                                                )
                    );
    }

    function fetchListOther( $userID = false, $asObject = true, $sortBy = false,
                             $offset = false, $limit = false, $accessKeys = array()
                           )
    {
        if ( !$userID )
        {
            $userID = eZUser::currentUserID();
        }
        $idList          = array();
        $otherLightboxes = eZLightboxAccess::fetchListByUser( $userID, $asObject, false,
                                                              $sortBy, $limit, $offset, $accessKeys
                                                            );
        foreach ( $otherLightboxes as $index => $lightboxArray )
        {
            $idList[] = $lightboxArray['lightbox_id'];
        }
        $otherLightboxes = array();
        foreach ( array_unique( $idList ) as $lightboxID )
        {
            $otherLightboxes[] = eZLightbox::fetch( $lightboxID );
        }
        return array( 'result' => $otherLightboxes );
    }

    function fetchListCount( $userID = false, $otherMasks = array() )
    {
        if ( !$userID )
        {
            $userID = eZUser::currentUserID();
        }
        return array( 'result' => (int)eZLightbox::fetchListByUser( $userID, false, true, false, false, false, $otherMasks ) );
    }

    function fetchListCountOwn( $userID = false )
    {
        if ( !$userID )
        {
            $userID = eZUser::currentUserID();
        }
        return array( 'result' => (int)eZLightbox::fetchOwnListByUser( $userID, false, true, false, false, false ) );
    }

    function fetchListCountOther( $userID = false, $asObject = true, $sortBy = false,
                                  $offset = false, $limit = false, $accessKeys = array()
                                )
    {
        if ( !$userID )
        {
            $userID = eZUser::currentUserID();
        }
        return array( 'result' => (int)eZLightboxAccess::fetchListByUser( $userID, $asObject, true,
                                                                          $sortBy, $limit, $offset, $accessKeys
                                                                        )
                    );
    }

    function fetchSessionKey( $userID = false )
    {
        if ( !$userID )
        {
            $userID = eZUser::currentUserID();
        }
        return array( 'result' => eZLightbox::createSessionKey( $userID ) );
    }

    function fetchBasketItemCount()
    {
        $http       = eZHTTPTool::instance();
        $sessionID  = $http->sessionID();
        $db    = eZDB::instance();
        $query = 'SELECT count( a.id ) count
                  FROM   ezproductcollection_item a, ezbasket b
                  WHERE  a.productcollection_id=b.productcollection_id AND
                         b.session_id="' . $sessionID . '"';
        $rows = $db->arrayQuery( $query );
        if ( isset( $rows[0]['count'] ) )
        {
            return array( 'result' => $rows[0]['count'] );
        }
        return array( 'result' => 0 );
    }

    function fetchAccessList( $userID = false, $asObject = true, $sortBy = false,
                              $offset = false, $limit = false
                            )
    {
        if ( !$userID )
        {
            $userID = eZUser::currentUserID();
        }
        return array( 'result' => eZLightboxAccess::fetchListByUser( $userID, $asObject, false,
                                                                     $sortBy, $offset, $limit
                                                                   )
                    );
    }

    function fetchLightbox( $id, $asObject = true )
    {
        return array( 'result' => eZLightbox::fetch( $id, $asObject ) );
    }

    function fetchLightboxObjectItems()
    {
        return array( 'result' => eZLightboxObject::availableItems() );
    }

    function fetchLightboxAccessKeys()
    {
        return array( 'result' => eZLightboxAccess::accessKeys() );
    }

    function fetchLightboxAccessKeyByName( $accessKeyName )
    {
        return array( 'result' => eZLightboxAccess::accessKeyByName( $accessKeyName ) );
    }

    function fetchLightboxAccessKeyFlags()
    {
        return array( 'result' => eZLightboxAccess::accessKeyFlags() );
    }

    function fetchLightboxAccessKeyByFlag( $accessKeyFlag )
    {
        return array( 'result' => eZLightboxAccess::accessKeyByFlag( $accessKeyFlag ) );
    }

    function fetchLightboxItemMoveDirections()
    {
        return array( 'result' => eZLightbox::itemMoveDirections() );
    }

    function fetchLightboxItemMoveDirectionsByName( $directionName )
    {
        return array( 'result' => eZLightbox::itemMoveDirectionsByName( $directionName ) );
    }

    function fetchLightboxItemPermission( $lightbox, $item, $itemTypeID )
    {
        $lightboxObject = false;
        if ( is_numeric( $lightbox ) )
        {
            $lightboxObject = eZLightbox::fetch( (int)$lightbox );
            if ( !is_object( $lightboxObject ) )
            {
                eZDebug::writeDebug( 'Invalid lightbox ID: ' . $lightbox, __METHOD__ );
                return false;
}
        }
        else if ( is_object( $lightbox ) )
        {
            if ( !( $lightbox instanceof eZLightbox ) )
            {
                eZDebug::writeDebug( 'Invalid lightbox object.', __METHOD__ );
                return false;
            }
            $lightboxObject = $lightbox;
        }
        else
        {
            eZDebug::writeDebug( 'Type of submitted lightbox not supported.', __METHOD__ );
            return false;
        }
        return array( 'result' => $lightboxObject->userCanAddItem( $itemTypeID, $item ) );
    }

}

?>
