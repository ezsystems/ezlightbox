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
require_once( 'kernel/common/template.php' );

$http           = eZHTTPTool::instance();
$tpl            = templateInit();
$db             = eZDB::instance();
$error          = false;
$Module         = $Params['Module'];
$lightbox_id    = $Params['LightboxID'];
$lightboxObject = null;
$url            = '/lightbox/edit';
$lightbox_name  = '';
$messages       = array();
$actionSuccess  = false;
$redirectURI    = '';
$redirectName   = 'LastLightboxURI_edit';
$path           = array( array( 'text' => ezi18n( 'lightbox/edit/path', 'Lightbox' ),
                                'url'  => null
                              ),
                         array( 'text' => ezi18n( 'lightbox/edit/path', 'Edit' ),
                                'url'  => $url
                              )
                       );

if ( !is_object( $http ) )
{
    eZDebug::writeError( 'Failed to get eZHTTPTool instance.' );
    $error = true;
}

if ( !is_object( $tpl ) )
{
    eZDebug::writeError( 'Failed to get eZTemplate instance.' );
    $error = true;
}

if ( !is_object( $db ) )
{
    eZDebug::writeError( 'Failed to get eZDB instance.' );
    $error = true;
}

if ( !$lightbox_id || !is_numeric( $lightbox_id ) || $lightbox_id <= 0 )
{
    eZDebug::writeError( 'Invalid lightbox ID: ' . $lightbox_id );
    $error = true;
}
else
{
    $lightboxObject = eZLightbox::fetch( $lightbox_id );
    if ( !is_object( $lightboxObject ) )
    {
        eZDebug::writeError( 'Failed to fetch lightbox with ID: ' . $lightbox_id );
        $error = true;
    }
    else if ( !$lightboxObject->attribute( 'can_edit' ) )
    {
        eZDebug::writeError( 'You are not allowed to edit lightbox with ID: ' . $lightbox_id );
        $lightboxObject = null;
        $error = true;
    }
    else
    {
        $url .= '/' . $lightbox_id;
        $path[] = array( 'text' => $lightboxObject->attribute( 'name' ),
                         'url'  => $url
                       );
    }
}

if ( $http->hasPostVariable( 'redirectURI' ) )
{
    $redirectURI = $http->postVariable( 'redirectURI' );
}
else if ( $http->hasSessionVariable( $redirectName ) )
{
    $redirectURI = $http->sessionVariable( $redirectName );
}
else if ( $http->hasSessionVariable( 'LastAccessesURI' ) &&
          !preg_match( '/\b\(type\)\b/', $http->sessionVariable( 'LastAccessesURI' ) )
        )
{
    $redirectURI = $http->sessionVariable( 'LastAccessesURI' );
    if ( $redirectURI == '' )
    {
        $redirectURI = '/';
    }
    $http->setSessionVariable( $redirectName, $redirectURI );
}
else if ( $http->hasSessionVariable( 'LastSearchURI' ) )
{
    $redirectURI = $http->sessionVariable( 'LastSearchURI' );
    $http->setSessionVariable( $redirectName, $redirectURI );
}

if ( !$error )
{
    $url .= '/' . $lightbox_id;

    if ( $http->hasPostVariable( 'GoBackButton' ) && $redirectURI != '' )
    {
        $http->removeSessionVariable( $redirectName );
        $Module->redirectTo( $redirectURI );
    }

    if ( $http->hasPostVariable( 'DeleteLightboxButton' ) )
    {
        $operationResult = eZOperationHandler::execute( 'lightbox', 'delete',
                                                        array( 'id' => $lightbox_id )
                                                      );
        $messages = array_merge( $messages, $operationResult['messages'] );
        if ( $operationResult['status'] == eZModuleOperationInfo::STATUS_CONTINUE )
        {
            return $Module->redirectTo( $redirectURI );
        }
    }

    if ( $http->hasPostVariable( 'DeleteUsersButton' ) )
    {
        if ( $http->hasPostVariable( 'selectedUserList' ) )
        {
            $userIDList = $http->postVariable( 'selectedUserList' );
            $operationResult = eZOperationHandler::execute( 'lightbox', 'removeUsers',
                                                            array( 'id'       => $lightbox_id,
                                                                   'user_ids' => $userIDList
                                                                 )
                                                          );
            if ( $operationResult['status'] == eZModuleOperationInfo::STATUS_CONTINUE )
            {
                $actionSuccess = true;
            }
            $messages = array_merge( $messages, $operationResult['messages'] );
        }
        else
        {
            $messages[] = ezi18n( 'lightbox/edit', 'No users have been selected for deletion.' );
        }
    }

    if ( $http->hasPostVariable( 'StoreLightboxButton' ) )
    {
        if ( $http->hasPostVariable( 'lightbox_name' ) )
        {
            $newName = trim( $http->postVariable( 'lightbox_name' ) );
            if ( $newName != '' )
            {
                $http = eZHTTPTool::instance();
                $http->removeSessionVariable( eZLightbox::PREFERENCE_SESSION_HASHKEY );
                $lightboxObject->setAttribute( 'name', $newName );
                $lightboxObject->store();
                if ( $http->hasPostVariable( 'userFlags' ) )
                {
                    $error     = false;
                    $userFlags = $http->postVariable( 'userFlags' );
                    foreach ( $userFlags as $userID => $flags )
                    {
                        $lightboxAccessObject = eZLightboxAccess::fetch( $lightbox_id, $userID );
                        if ( is_object( $lightboxAccessObject ) )
                        {
                            $newMask = 0;
                            foreach ( $flags as $key => $value )
                            {
                                $newMask += $value;
                            }
                            $lightboxAccessObject->setAttribute( 'access_mask', $newMask );
                            $db->begin();
                            $lightboxAccessObject->store();
                            $db->commit();
                        }
                        else
                        {
                            $error = true;
                            $messages[] = ezi18n( 'lightbox/edit', 'Failed to fetch access object for user ID "%1"',
                                                  null, array( $userID )
                                                );
                        }
                    }
                }
                else
                {
                    $actionSuccess = true;
                    eZDebug::writeDebug( 'No flags for user access have been submitted.', 'lightbox/edit' );
                }
                if ( !$error )
                {
                    $actionSuccess = true;
                    $messages[] = ezi18n( 'lightbox/edit', 'Successfully stored lightbox.' );
                }
            }
            else
            {
                $messages[] = ezi18n( 'lightbox/edit', 'Invalid lightbox name "%1"',
                                      null, array( $newName )
                                    );
            }
        }
        else
        {
            $messages[] = ezi18n( 'lightbox/edit', 'No name for lightbox available.' );
        }
    }

}

$lightboxList = eZLightbox::fetchListByUser( eZUser::currentUserID() );

$tpl->setVariable( 'url',              $url );
$tpl->setVariable( 'messages',         $messages );
$tpl->setVariable( 'actionSuccess',    $actionSuccess );
$tpl->setVariable( 'lightbox',         $lightboxObject );
$tpl->setVariable( 'userLightboxList', $lightboxList );
$tpl->setVariable( 'redirectURI',      $redirectURI );
$tpl->setVariable( 'currentUserID',    eZUser::currentUserID() );

$res = eZTemplateDesignResource::instance();
$res->setKeys( array( array( 'navigation_part_identifier', 'ezlightboxnavigationpart' ),
                      array( 'url_alias',                  $url )
                    )
             );

$Result = array();
$Result['content']    = $tpl->fetch( 'design:lightbox/edit.tpl' );
$Result['pagelayout'] = true;
$Result['path']       = $path;

?>
