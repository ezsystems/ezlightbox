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
require_once( 'kernel/common/i18n.php' );

$http                 = eZHTTPTool::instance();
$tpl                  = templateInit();
$db                   = eZDB::instance();
$error                = false;
$Module               = $Params['Module'];
$lightboxID           = $Params['LightboxID'];
$itemID               = $Params['ItemID'];
$typeID               = $Params['TypeID'];
$lightboxObject       = null;
$viewParameters       = isset( $Params['UserParameters'] ) ? $Params['UserParameters'] : array();
$scriptMode           = false;
$url                  = '/lightbox/add';
$lightbox_name        = '';
$messages             = array();
$actionSuccess        = false;
$redirectURI          = '';
$redirectName         = 'LastLightboxURI_add';
$redirectAfterAdd     = false;

if ( isset( $viewParameters['scriptmode'] ) && $viewParameters['scriptmode'] == 1 )
{
    $scriptMode = true;
}

if ( !is_object( $http ) )
{
    eZDebug::writeError( 'Failed to get eZHTTPTool instance.', __METHOD__ );
    $error = true;
}

if ( !is_object( $tpl ) )
{
    eZDebug::writeError( 'Failed to get eZTemplate instance.', __METHOD__ );
    $error = true;
}

if ( !is_object( $db ) )
{
    eZDebug::writeError( 'Failed to get eZDB instance.', __METHOD__ );
    $error = true;
}

if ( !$lightboxID || !is_numeric( $lightboxID ) || $lightboxID <= 0 )
{
    eZDebug::writeError( 'Invalid lightbox ID: ' . $lightboxID, __METHOD__ );
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

if ( $http->hasPostVariable( 'redirectAfterLightboxItemHasBeenAdded' ) )
{
    $redirectAfterAdd = true;
}

if ( !$error )
{

    if ( $http->hasPostVariable( 'GoBackButton' ) && $redirectURI != '' )
    {
        $http->removeSessionVariable( $redirectName );
        $Module->redirectTo( $redirectURI );
    }

        $operationResult = eZOperationHandler::execute( 'lightbox', 'add',
                                                        array( 'id'      => $lightboxID,
                                                               'item_id' => $itemID,
                                                               'type_id' => $typeID
                                                             )
                                                      );
        $messages = array_merge( $messages, $operationResult['messages'] );
        if ( $operationResult['status'] == eZModuleOperationInfo::STATUS_CONTINUE )
        {
            $actionSuccess = true;
        }

        if ( $redirectAfterAdd )
        {
            $Module->redirectTo( $redirectURI );
        }
}

$tpl->setVariable( 'url',           $url );
$tpl->setVariable( 'lightboxID',    $lightboxID );
$tpl->setVariable( 'itemID',        $itemID );
$tpl->setVariable( 'typeID',        $typeID );
$tpl->setVariable( 'messages',      $messages );
$tpl->setVariable( 'actionSuccess', $actionSuccess );
$tpl->setVariable( 'redirectURI',   $redirectURI );
$tpl->setVariable( 'redirectAfter', $redirectAfterAdd );
$tpl->setVariable( 'scriptMode',    $scriptMode );

$res = eZTemplateDesignResource::instance();

$res->setKeys( array( array( 'navigation_part_identifier', 'ezlightboxnavigationpart' ),
                      array( 'url_alias',                  $url ),
                      array( 'lightboxid',                 $lightboxID ),
                      array( 'itemid',                     $itemID ),
                      array( 'itemtypeid',                 $typeID ),
                      array( 'scriptmode',                 $scriptMode )
                    )
             );

$Result               = array();
$Result['content']    = $tpl->fetch( 'design:lightbox/add.tpl' );

if ( $scriptMode )
{
    echo $Result['content'];
    eZDB::checkTransactionCounter();
    eZExecution::cleanExit();
}

$Result['pagelayout'] = true;
$Result['path']       = array( array( 'text' => ezi18n( 'lightbox/add/path', 'Lightbox' ),
                                      'url'  => null
                                    ),
                               array( 'text' => ezi18n( 'lightbox/add/path', 'Add' ),
                                      'url'  => $url
                                    )
                             );

?>
