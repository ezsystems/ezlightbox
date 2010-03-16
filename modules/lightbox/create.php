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

$http                 = eZHTTPTool::instance();
$tpl                  = templateInit();
$db                   = eZDB::instance();
$error                = false;
$Module               = $Params['Module'];
$url                  = '/lightbox/create';
$lightbox_name        = '';
$messages             = array();
$actionSuccess        = false;
$redirectURI          = '';
$redirectName         = 'LastLightboxURI_create';
$redirectAfterCreate  = false;
$addItemIDAfterCreate = false;
$addTypeIDAfterCreate = false;

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

if ( $http->hasPostVariable( 'redirectURI' ) )
{
    $redirectURI = $http->postVariable( 'redirectURI' );
}
else if ( $http->hasSessionVariable( $redirectName ) )
{
    $redirectURI = $http->sessionVariable( $redirectName );
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
    $http->setSessionVariable( $redirectName, $redirectURI );
}
else if ( $http->hasSessionVariable( 'LastSearchURI' ) )
{
    $redirectURI = $http->sessionVariable( 'LastSearchURI' );
    $http->setSessionVariable( $redirectName, $redirectURI );
}

if( $http->hasPostVariable( 'redirectAfterLightboxHasBeenCreated' ) )
{
    $redirectAfterCreate = true;
}

if ( !$error )
{

    if ( isset( $Params['UserParameters']['ItemID'] ) &&
         isset( $Params['UserParameters']['TypeID'] )
       )
    {
        $addItemIDAfterCreate = $Params['UserParameters']['ItemID'];
        $addTypeIDAfterCreate = $Params['UserParameters']['TypeID'];
    }
    else if ( $http->hasPostVariable( 'addItemIDAfterCreate' ) &&
              $http->hasPostVariable( 'addTypeIDAfterCreate' )
            )
    {
        $addItemIDAfterCreate = $http->postVariable( 'addItemIDAfterCreate' );
        $addTypeIDAfterCreate = $http->postVariable( 'addTypeIDAfterCreate' );
    }

    if ( $addItemIDAfterCreate !== false && $addTypeIDAfterCreate !== false )
    {
        $tpl->setVariable( 'addItemIDAfterCreate', $addItemIDAfterCreate );
        $tpl->setVariable( 'addTypeIDAfterCreate', $addTypeIDAfterCreate );
    }

    if ( $http->hasPostVariable( 'GoBackButton' ) && $redirectURI != '' )
    {
        $http->removeSessionVariable( $redirectName );
        $Module->redirectTo( $redirectURI );
    }

    if ( $http->hasPostVariable( 'CreateLightboxButton' ) &&
         $http->hasPostVariable( 'lightbox_name' )
       )
    {
        $operationResult = eZOperationHandler::execute( 'lightbox', 'create',
                                                        array( 'name' => $http->postVariable( 'lightbox_name' ) )
                                                      );
        $messages = array_merge( $messages, $operationResult['messages'] );
        if ( $operationResult['status'] == eZModuleOperationInfo::STATUS_CONTINUE )
        {
            $actionSuccess = true;
            if ( $http->hasPostVariable( 'changeToCurrentLightbox' ) && $operationResult['lightbox_id'] !== false )
            {
                eZPreferences::setValue( eZLightbox::PREFERENCE_CURRENT_LIGHTBOX, $operationResult['lightbox_id'] );
            }
            if ( $addItemIDAfterCreate !== false && $addTypeIDAfterCreate !== false && $operationResult['lightbox_id'] !== false )
            {
                $operationResult = eZOperationHandler::execute( 'lightbox', 'add',
                                                                 array( 'id'      => $operationResult['lightbox_id'],
                                                                        'item_id' => $addItemIDAfterCreate,
                                                                        'type_id' => $addTypeIDAfterCreate
                                                                      )
                                                              );
                $messages = array_merge( $messages, $operationResult['messages'] );
            }
        }

        if( $redirectAfterCreate )
        {
            $Module->redirectTo( $redirectURI );
        }
    }
}

$tpl->setVariable( 'url',           $url );
$tpl->setVariable( 'lightbox_name', $lightbox_name );
$tpl->setVariable( 'messages',      $messages );
$tpl->setVariable( 'actionSuccess', $actionSuccess );
$tpl->setVariable( 'redirectURI',   $redirectURI );
$tpl->setVariable( 'redirectAfter', $redirectAfterCreate );

$res = eZTemplateDesignResource::instance();
$res->setKeys( array( array( 'navigation_part_identifier', 'ezlightboxnavigationpart' ),
                      array( 'url_alias',                  $url )
                    )
             );

$Result               = array();
$Result['content']    = $tpl->fetch( 'design:lightbox/create.tpl' );
$Result['pagelayout'] = true;
$Result['path']       = array( array( 'text' => ezi18n( 'lightbox/create/path', 'Lightbox' ),
                                      'url'  => null
                                    ),
                               array( 'text' => ezi18n( 'lightbox/create/path', 'Create' ),
                                      'url'  => $url
                                    )
                             );

?>
