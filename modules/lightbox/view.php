<?php
//
// Created on: <11-Sep-2007 09:08:13 ab>
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

$http              = eZHTTPTool::instance();
$tpl               = templateInit();
$db                = eZDB::instance();
$error             = false;
$Module            = $Params['Module'];
$view_mode         = $Params['ViewMode'];
$lightbox_id       = null;
$lightboxObject    = null;
$lightboxList      = null;
$userLightboxList  = array();
$otherLightboxList = array();
$viewParameters    = array();
$url               = '/lightbox/view/' . $view_mode;
$messages          = array();
$actionSuccess     = false;
$template          = 'design:lightbox/views/' . $view_mode . '.tpl';
$redirectURI       = '';
$redirectName      = 'LastLightboxURI_view';
$path              = array( array( 'text' => ezi18n( 'lightbox/view/path', 'Lightbox' ),
                                   'url'  => null
                                 ),
                            array( 'text' => ezi18n( 'lightbox/view/path', 'View' ),
                                   'url'  => $url
                                 )
                          );

if ( $view_mode == '' )
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

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

if ( isset( $Params['UserParameters'] ) )
{
    $UserParameters = $Params['UserParameters'];
}
else
{
    $UserParameters = array();
}

if ( array_key_exists( 'LightboxID', $Params ) )
{
    $lightbox_id = $Params['LightboxID'];
    if ( !$lightbox_id || !is_numeric( $lightbox_id ) || $lightbox_id <= 0 )
    {
        eZDebug::writeNotice( 'Invalid lightbox ID: ' . $lightbox_id );
    }
    else
    {
        $lightboxObject = eZLightbox::fetch( $lightbox_id );
        if ( !is_object( $lightboxObject ) )
        {
            eZDebug::writeWarning( 'Failed to fetch lightbox with ID: ' . $lightbox_id );
        }
        else if ( !$lightboxObject->attribute( 'can_view' ) )
        {
            eZDebug::writeWarning( 'You are not allowed to view lightbox with ID: ' . $lightbox_id );
            $lightboxObject = null;
        }
        else
        {
            $url .= '/' . $lightbox_id;
            $path[] = array( 'text' => $lightboxObject->attribute( 'name' ),
                             'url'  => $url
                           );
        }
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

$currentUserID = eZUser::currentUserID();
$lightboxList  = eZLightbox::fetchListByUser( eZUser::currentUserID() );

foreach ( $lightboxList as $lightboxListObject )
{
    if ( $lightboxListObject->attribute( 'owner_id' ) == $currentUserID )
    {
        $userLightboxList[] = $lightboxListObject;
    }
    else
    {
        $otherLightboxList[] = $lightboxListObject;
    }
}

unset( $lightboxList );

if ( !$error )
{

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
            return $Module->redirectToView( 'view', array( 'list' ) );
        }
    }
}

$parsed_uri = parse_url( $_SERVER['SCRIPT_URI'] );

if ( isset( $parsed_uri['host'] ) )
{
    $parsed_uri['domain'] = ereg_replace( '^[^\.]*\.', '', $parsed_uri['host'] );
}

$viewParameters = array_merge( $viewParameters, $UserParameters );

$tpl->setVariable( 'parsed_uri',        $parsed_uri );
$tpl->setVariable( 'url',               $url );
$tpl->setVariable( 'messages',          $messages );
$tpl->setVariable( 'actionSuccess',     $actionSuccess );
$tpl->setVariable( 'selectedLightbox',  $lightboxObject );
$tpl->setVariable( 'userLightboxList',  $userLightboxList );
$tpl->setVariable( 'otherLightboxList', $otherLightboxList );
$tpl->setVariable( 'lightboxID',        $lightbox_id );
$tpl->setVariable( 'viewMode',          $view_mode );
$tpl->setVariable( 'redirectURI',       $redirectURI );
$tpl->setVariable( 'viewParameters',    $viewParameters );
$tpl->setVariable( 'currentUserID',     $currentUserID );

$res = eZTemplateDesignResource::instance();

$res->setKeys( array( array( 'navigation_part_identifier', 'ezlightboxnavigationpart' ),
                      array( 'url_alias',                  $url ),
                      array( 'viewmode',                   $view_mode ),
                      array( 'module',                     'lightbox' )
                    )
             );

$Result = array();
$Result['content']           = $tpl->fetch( $template );
$Result['pagelayout']        = true;
$Result['path']              = $path;
$Result['shown_lightbox_id'] = $lightbox_id;

?>
