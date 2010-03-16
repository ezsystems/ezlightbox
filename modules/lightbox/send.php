<?php
//
// Created on: <25-Aug-2007 11:11:11 ab>
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

$http           = eZHTTPTool::instance();
$tpl            = templateInit();
$db             = eZDB::instance();
$error          = false;
$Module         = $Params['Module'];
$url            = '/lightbox/send';
$lightbox_id    = $Params['LightboxID'];
$lightboxObject = null;
$lightbox_name  = '';
$messages       = array();
$actionSuccess  = false;
$receiver       = false;
$subject        = false;
$body           = false;
$accessKeyArray = array();
$accessMask     = 0;
$userObject     = null;
$redirectURI    = '';
$sendout        = false;
$lightboxIni    = eZINI::instance( 'lightbox.ini' );
$path           = array( array( 'text' => ezi18n( 'lightbox/send/path', 'Lightbox' ),
                                'url'  => null
                              ),
                         array( 'text' => ezi18n( 'lightbox/send/path', 'Send' ),
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

if ( !is_object( $lightboxIni ) )
{
    eZDebug::writeError( 'Failed to get eZINI instance.' );
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
    else if ( !$lightboxObject->attribute( 'can_send' ) )
    {
        eZDebug::writeError( 'You are not allowed to send lightbox with ID: ' . $lightbox_id );
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

if ( !$error )
{
    if ( $http->hasPostVariable( 'GoBackButton' ) && $redirectURI != '' )
    {
        $Module->redirectTo( $redirectURI );
    }

    if ( $http->hasPostVariable( 'receiver' ) )
    {
        $receiver = trim( $http->postVariable( 'receiver' ) );
    }

    if ( $http->hasPostVariable( 'subject' ) )
    {
        $subject = trim( $http->postVariable( 'subject' ) );
    }

    if ( $http->hasPostVariable( 'body' ) )
    {
        $body = trim( $http->postVariable( 'body' ) );
    }

    if ( $http->hasPostVariable( 'accessKeyArray' ) )
    {
        $accessKeyArray = $http->postVariable( 'accessKeyArray' );
        foreach ( $accessKeyArray as $key => $value )
        {
            $accessMask += $value;
        }
    }

    if ( $http->hasPostVariable( 'FindUserButton' ) )
    {
        $userObject = eZUser::fetchByEmail( $receiver );
        if ( is_object( $userObject ) )
        {
            $UserSendOwnLightbox = false;
            if ( $lightboxIni->hasVariable( 'CommonSettings', 'UserSendOwnLightbox' ) &&
                 $lightboxIni->variable( 'CommonSettings', 'UserSendOwnLightbox' ) == 'enabled'
               )
            {
                $UserSendOwnLightbox = true;
            }
            if ( ( !$UserSendOwnLightbox && $userObject->attribute( 'contentobject_id' ) != eZUser::currentUserID() ) || $UserSendOwnLightbox )
            {
                $userAccess = eZLightboxAccess::fetch( $lightbox_id,
                                                       $userObject->attribute( 'contentobject_id' ),
                                                       true
                                                     );
                if ( !is_object( $userAccess ) )
                {
                    if ( $subject != '' )
                    {
                        $messages[] = ezi18n( 'lightbox/send',
                                              'A user with the EMail address "%1" has been found. Please verify if this is the person you want to grant access to your lightbox.',
                                               null, array( $receiver )
                                            );
                        $actionSuccess = true;
                    }
                    else
                    {
                        $userObject = null;
                        $messages[] = ezi18n( 'lightbox/send', 'Please enter a subject.' );
                    }
                }
                else
                {
                    $userContentObject = $userObject->attribute( 'contentobject' );
                    $messages[] = ezi18n( 'lightbox/send', 'The user "%1" already has access to this lightbox.',
                                           null, array( $userContentObject->attribute( 'name' ) )
                                        );
                    $userObject = null;
                }
            }
            else
            {
                $userObject = null;
                $messages[] = ezi18n( 'lightbox/send', 'It is not allowed to send the lightbox to yourself.' );
            }
        }
        else
        {
            $messages[] = ezi18n( 'lightbox/send', 'No user with an EMail address "%1" found.',
                                   null, array( $receiver )
                                );
        }
    }

    if ( $http->hasPostVariable( 'SendLightboxButton' ) )
    {
        if ( $http->hasPostVariable( 'subject' )                  &&
             $http->hasPostVariable( 'body' )                     &&
             $http->hasPostVariable( 'userID' )                   &&
             $http->hasPostVariable( 'receiver' )                 &&
             $http->hasPostVariable( 'accessMask' )
           )
        {
            $mail_data['subject'] = $http->postVariable( 'subject' );
            $mail_data['body']    = $http->postVariable( 'body' );
            $userID               = $http->postVariable( 'userID' );
            $userObject           = eZUser::fetch( $userID );
            if ( is_object( $userObject ) )
            {
                $operationResult = eZOperationHandler::execute( 'lightbox', 'send',
                                                                array( 'id'          => $lightbox_id,
                                                                       'user_id'     => $userID,
                                                                       'access_mask' => $http->postVariable( 'accessMask' ),
                                                                       'mail_data'   => $mail_data
                                                                     )
                                                              );
                $messages = array_merge( $messages, $operationResult['messages'] );
                $sendout  = true;
                if ( $operationResult['status'] == eZModuleOperationInfo::STATUS_CONTINUE )
                {
                    $actionSuccess = true;
                }
            }
            else
            {
                $messages[] = ezi18n( 'lightbox/send', 'User with ID %1 does not exist.',
                                       null, array( $userID )
                                    );
            }
        }
        else
        {
            $messages[] = ezi18n( 'lightbox/send', 'Not all parameters have been submitted.' );
        }
    }
}

$tpl->setVariable( 'url',            $url );
$tpl->setVariable( 'lightbox_name',  $lightbox_name );
$tpl->setVariable( 'messages',       $messages );
$tpl->setVariable( 'actionSuccess',  $actionSuccess );
$tpl->setVariable( 'lightbox',       $lightboxObject );
$tpl->setVariable( 'receiver',       $receiver );
$tpl->setVariable( 'subject',        $subject );
$tpl->setVariable( 'body',           $body );
$tpl->setVariable( 'accessKeyArray', $accessKeyArray );
$tpl->setVariable( 'user',           $userObject );
$tpl->setVariable( 'accessMask',     $accessMask );
$tpl->setVariable( 'redirectURI',    $redirectURI );
$tpl->setVariable( 'sendout',        $sendout );

$res = eZTemplateDesignResource::instance();
$res->setKeys( array( array( 'navigation_part_identifier', 'ezlightboxnavigationpart' ),
                      array( 'url_alias',                  $url )
                    )
             );

$Result = array();
$Result['content']    = $tpl->fetch( 'design:lightbox/send.tpl' );
$Result['pagelayout'] = true;
$Result['path']       = $path;

?>
