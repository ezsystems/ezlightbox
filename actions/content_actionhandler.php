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

function ezlightbox_ContentActionHandler( $module, $http, $objectID )
{
    $error_message = '';
    $lightboxID    = false;
    if ( $http->hasPostVariable( 'LightboxID' ) )
    {
        $lightboxID = $http->postVariable( 'LightboxID' );
    }
    else if ( $http->hasPostVariable( 'selectedLightboxID' ) )
    {
        $lightboxID = $http->postVariable( 'selectedLightboxID' );
    }
    else
    {
        $lightboxID = eZPreferences::value( eZLightbox::PREFERENCE_CURRENT_LIGHTBOX );
        if ( !$lightboxID || !is_numeric( $lightboxID ) || $lightboxID <= 0 )
        {
            $lightboxID    = false;
            $error_message = ezi18n( 'lightbox/error', 'Missing or invalid lightbox ID %1.',
                                     null, array( $lightboxID )
                                   );
        }
    }

    $itemID = $objectID;
    $typeID = eZLightboxObject::TYPE_OBJECT_ID;

    if ( $http->hasPostVariable( 'ItemID' ) && $http->hasPostVariable( 'ItemType' ) )
    {
        $itemID = $http->postVariable( 'ItemID' );
        $itemType = $http->postVariable( 'ItemType' );
        $itemObject = eZLightboxObjectItem::fetchByName( $itemType );
        if ( !is_object( $itemObject ) )
        {
            $itemObject = eZLightboxObjectItem::fetchByID( $itemType );
        }
        if ( is_object( $itemObject ) )
        {
            $typeID = $itemObject->getID();
        }
        else
        {
            eZDebug::writeWarning( 'Failed to fetch lightbox item object for item type ' . $itemType, 'lightbox/action' );
        }
    }

    if ( $http->hasPostVariable( 'ChangeUserCurrentLightbox' ) &&
         $http->hasPostVariable( 'newLightboxID' )
       )
    {
        $http = eZHTTPTool::instance();
        $http->removeSessionVariable( eZLightbox::PREFERENCE_SESSION_HASHKEY );
        eZPreferences::setValue( 'currentLightboxID', $http->postVariable( 'newLightboxID' ) );
        $redirectURI = $http->hasPostVariable( 'redirectAfterSelectionURI' ) ?
                            $http->postVariable( 'redirectAfterSelectionURI' ) :
                            $http->sessionVariable( 'LastAccessesURI' );
        $module->redirectTo( $redirectURI );
        return true;
    }

    if ( $http->hasPostVariable( 'GoBackButton' ) )
    {
        $redirectURI = '/';
        if ( $http->hasPostVariable( 'redirectURI' ) )
        {
            $redirectURI = $http->postVariable( 'redirectURI' );
        }
        else if ( $http->hasSessionVariable( 'LastAccessesURI' ) &&
                  !ereg( '(type)', $http->sessionVariable( 'LastAccessesURI' ) )
                )
        {
            $redirectURI = $http->sessionVariable( 'LastAccessesURI' );
            if ( $redirectURI == '' )
            {
                $redirectURI = '/';
            }
        }
        else if ( $http->hasSessionVariable( 'LastSearchURI' ) )
        {
            $redirectURI = $http->sessionVariable( 'LastSearchURI' );
        }
        $module->redirectTo( $redirectURI );
        return true;
    }

    if ( $http->hasPostVariable( 'ViewLightboxAction' ) )
    {
        if ( !$lightboxID )
        {
            $module->handleError( eZLightbox::ERROR_NOT_FOUND, 'lightbox',
                                  array( 'error_message' => $error_message )
                                );
            return true;
        }
        $module->redirectTo( '/lightbox/view/list/' . $lightboxID );
        return true;
    }

    if ( $http->hasPostVariable( 'CreateLightboxAction' ) )
    {
        $module->redirectTo( '/lightbox/create' );
        return true;
    }

    if ( $http->hasPostVariable( 'EditLightboxAction' ) )
    {
        $module->redirectTo( '/lightbox/edit/' . $lightboxID );
        return true;
    }

    if ( $http->hasPostVariable( 'SendLightboxAction' ) )
    {
        $module->redirectTo( '/lightbox/send/' . $lightboxID );
        return true;
    }

    if ( $http->hasPostVariable( 'DeleteLightboxAction' ) )
    {
        if ( !$lightboxID )
        {
            $module->handleError( eZLightbox::ERROR_NOT_FOUND, 'lightbox',
                                  array( 'error_message' => $error_message )
                                );
            return true;
        }
        $operationResult = eZOperationHandler::execute( 'lightbox', 'delete',
                                                        array( 'id' => $lightboxID )
                                                     );
        if ( $operationResult['status'] == eZModuleOperationInfo::STATUS_CONTINUE )
        {
            $module->redirectTo( $http->sessionVariable( 'LastAccessesURI' ) );
            return true;
        }
        else
        {
            $module->handleError( eZLightbox::ERROR_OPERATION_FAILED, 'lightbox',
                                  array( 'error_messages' => $operationResult['messages'] )
                                );
            return true;
        }
    }

    if ( $http->hasPostVariable( 'EmptyLightboxAction' ) )
    {
        if ( !$lightboxID )
        {
            $module->handleError( eZLightbox::ERROR_NOT_FOUND, 'lightbox',
                                  array( 'error_message' => $error_message )
                                );
            return true;
        }
        $operationResult = eZOperationHandler::execute( 'lightbox', 'empty',
                                                        array( 'id' => $lightboxID )
                                                     );
        if ( $operationResult['status'] == eZModuleOperationInfo::STATUS_CONTINUE )
        {
            $redirectURI = $http->hasPostVariable( 'redirectAfterSelectionURI' ) ?
                                $http->postVariable( 'redirectAfterSelectionURI' ) :
                                $http->sessionVariable( 'LastAccessesURI' );
            $module->redirectTo( $redirectURI );
            return true;
        }
        else
        {
            $module->handleError( eZLightbox::ERROR_OPERATION_FAILED, 'lightbox',
                                  array( 'error_messages' => $operationResult['messages'] )
                                );
            return true;
        }
    }

    if ( $http->hasPostVariable( 'AddToLightboxAction' ) )
    {
        if ( !$lightboxID )
        {
            $lightboxID = eZPreferences::value( eZLightbox::PREFERENCE_CURRENT_LIGHTBOX );
            if ( !$lightboxID )
            {
                $module->redirect( 'lightbox', 'create', array(), null, array( 'ItemID' => $itemID,
                                                                               'TypeID' => $typeID
                                                                             )
                                 );
                return true;
            }
        }
        $operationResult = eZOperationHandler::execute( 'lightbox', 'add',
                                                        array( 'id'      => $lightboxID,
                                                               'item_id' => $itemID,
                                                               'type_id' => $typeID
                                                             )
                                                     );

        if ( $operationResult['status'] == eZModuleOperationInfo::STATUS_CONTINUE )
        {
            if ( $http->hasPostVariable( 'RedirectToSearchResult' ) &&
                 $http->hasSessionVariable( 'LastSearchURI' )
               )
            {
                $module->redirectTo( $http->sessionVariable( 'LastSearchURI' ) );
                return true;
            }
            else
            {
                $module->redirectTo( $http->sessionVariable( 'LastAccessesURI' ) );
                return true;
            }
        }
        else
        {
            $module->handleError( eZLightbox::ERROR_OPERATION_FAILED, 'lightbox',
                                  array( 'error_messages' => $operationResult['messages'] )
                                );
            return true;
        }
    }

    if ( $http->hasPostVariable( 'RemoveFromLightboxAction' ) )
    {
        if ( !$lightboxID )
        {
            $module->handleError( eZLightbox::ERROR_NOT_FOUND, 'lightbox',
                                  array( 'error_message' => $error_message )
                                );
            return true;
        }
        $operationResult = eZOperationHandler::execute( 'lightbox', 'remove',
                                                        array( 'id'      => $lightboxID,
                                                               'item_id' => $itemID,
                                                               'type_id' => $typeID
                                                             )
                                                     );
        if ( $operationResult['status'] == eZModuleOperationInfo::STATUS_CONTINUE )
        {
            $redirectURI = $http->hasPostVariable( 'redirectAfterSelectionURI' ) ?
                                $http->postVariable( 'redirectAfterSelectionURI' ) :
                                $http->sessionVariable( 'LastAccessesURI' );
            $module->redirectTo( $redirectURI );
            return true;
        }
        else
        {
            $module->handleError( eZLightbox::ERROR_OPERATION_FAILED, 'lightbox',
                                  array( 'error_messages' => $operationResult['messages'] )
                                );
            return true;
        }
    }

    if ( $http->hasPostVariable( 'ActionMoveLightboxItemUp' ) ||
         $http->hasPostVariable( 'ActionMoveLightboxItemDown' )
       )
    {
        if ( !$lightboxID )
        {
            $module->handleError( eZLightbox::ERROR_NOT_FOUND, 'lightbox',
                                  array( 'error_message' => $error_message )
                                );
            return true;
        }
        $direction = eZLightbox::MOVE_DIRECTION_UP;
        if ( $http->hasPostVariable( 'ActionMoveLightboxItemDown' ) )
        {
            $direction = eZLightbox::MOVE_DIRECTION_DOWN;
        }
        $lightboxObject = eZLightbox::fetch( $lightboxID );
        if ( is_object( $lightboxObject ) )
        {
            $moveResult = $lightboxObject->moveItem( $direction, $itemID );
            if ( !$moveResult )
            {
                eZDebug::writeWarning( 'Failed to move item with ID ' . $itemID . ' in lightbox with ID ' . $lightboxID . '.',
                                       'ezlightbox_ContentActionHandler'
                                     );
            }
        }
        else
        {
            eZDebug::writeWarning( 'Failed to fetch lightbox object. Can not move item.', 'ezlightbox_ContentActionHandler' );
        }
        $redirectURI = $http->hasPostVariable( 'redirectAfterSelectionURI' ) ?
                            $http->postVariable( 'redirectAfterSelectionURI' ) :
                            $http->sessionVariable( 'LastAccessesURI' );
        $module->redirectTo( $redirectURI );
        return true;
    }

    if ( $http->hasPostVariable( 'MoveToLightboxAction' ) )
    {
        if ( !$lightboxID )
        {
            $module->handleError( eZLightbox::ERROR_NOT_FOUND, 'lightbox',
                                  array( 'error_message' => $error_message )
                                );
            return true;
        }
        $targetLightboxID = false;
        $error_message    = ezi18n( 'lightbox/error', 'Missing target lightbox ID.' );
        if ( $http->hasPostVariable( 'MoveToLightboxID' ) )
        {
            $targetLightboxID = $http->postVariable( 'MoveToLightboxID' );
            if ( !$targetLightboxID || !is_numeric( $targetLightboxID ) || $targetLightboxID <= 0 )
            {
                $targetLightboxID = false;
            }
        }
        if ( !$targetLightboxID )
        {
            $module->handleError( eZLightbox::ERROR_NOT_FOUND, 'lightbox',
                                  array( 'error_message' => $error_message )
                                );
            return true;
        }
        $operationResult = eZOperationHandler::execute( 'lightbox', 'move',
                                                        array( 'id'        => $lightboxID,
                                                               'target_id' => $targetLightboxID,
                                                               'item_id'   => $itemID,
                                                               'type_id'   => $typeID
                                                             )
                                                     );
        if ( $operationResult['status'] == eZModuleOperationInfo::STATUS_CONTINUE )
        {
            $redirectURI = $http->hasPostVariable( 'redirectAfterSelectionURI' ) ?
                                $http->postVariable( 'redirectAfterSelectionURI' ) :
                                $http->sessionVariable( 'LastAccessesURI' );
            $module->redirectTo( $redirectURI );
            return true;
        }
        else
        {
            $module->handleError( eZLightbox::ERROR_OPERATION_FAILED, 'lightbox',
                                  array( 'error_messages' => $operationResult['messages'] )
                                );
        }
    }

}

?>
