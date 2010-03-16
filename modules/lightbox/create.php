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
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

require_once( 'autoload.php' );
require_once( 'kernel/common/template.php' );

$http                   = eZHTTPTool::instance();
$tpl                    = templateInit();
$db                     = eZDB::instance();
$error                  = false;
$Module                 = $Params['Module'];
$url                    = '/lightbox/create';
$lightbox_name          = '';
$messages               = array();
$actionSuccess          = false;
$redirectURI            = '';
$redirectAfterCreate    = false;
$addObjectIDAfterCreate = false;

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

if( $http->hasPostVariable( 'redirectAfterLightboxHasBeenCreated' ) )
{
    $redirectAfterCreate = true;
}

if ( !$error )
{

    if ( isset( $Params['UserParameters']['ContentObjectID'] ) )
    {
        $addObjectIDAfterCreate = $Params['UserParameters']['ContentObjectID'];
    }
    else if ( $http->hasPostVariable( 'addObjectIDAfterCreate' ) )
    {
        $addObjectIDAfterCreate = $http->postVariable( 'addObjectIDAfterCreate' );
    }

    if ( $addObjectIDAfterCreate !== false )
    {
        $tpl->setVariable( 'addObjectIDAfterCreate', $addObjectIDAfterCreate );
    }

    if ( $http->hasPostVariable( 'GoBackButton' ) && $redirectURI != '' )
    {
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
            if ( $addObjectIDAfterCreate !== false && $operationResult['lightbox_id'] !== false )
            {
                $operationResult = eZOperationHandler::execute( 'lightbox', 'add',
                                                                 array( 'id'        => $operationResult['lightbox_id'],
                                                                        'object_id' => $addObjectIDAfterCreate
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