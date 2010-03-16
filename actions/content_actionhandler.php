<?php

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
            $error_message = ezi18n( 'ezlightbox/error', 'Missing or invalid lightbox ID %1.',
                                     null, array( $lightboxID )
                                   );
        }
    }

    $itemID = $objectID;
    $typeID = eZLightboxObject::TYPE_OBJECT_ID;

    if ( $http->hasPostVariable( 'ItemID' ) && $http->hasPostVariable( 'ItemType' ) )
    {
        $itemID = $http->postVariable( 'ItemID' );
        $typeID = $http->postVariable( 'ItemType' );
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
        $error_message    = ezi18n( 'ezlightbox/error', 'Missing target lightbox ID.' );
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
