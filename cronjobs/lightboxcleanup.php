<?php
//
// Created on: <2009-12-07 10:47:28 ab>
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

$replaceInvalidOwnerWithID = false;
$removeInvalidItems        = true;
$lightboxINI               = eZINI::instance( 'lightbox.ini' );

if ( $lightboxINI->hasVariable( 'CleanUpSettings', 'ReplaceInvalidOwnerWithID' ) )
{
    $replaceID = $lightboxINI->variable( 'CleanUpSettings', 'ReplaceInvalidOwnerWithID' );
    if ( $replaceID != 'disabled' )
    {
        $replaceInvalidOwnerWithID = (int)$replaceID;
    }
}

if ( $lightboxINI->hasVariable( 'CleanUpSettings', 'RemoveInvalidItems' ) )
{
    if ( $lightboxINI->variable( 'CleanUpSettings', 'RemoveInvalidItems' ) != 'enabled' )
    {
        $removeInvalidItems = false;
    }
}

$lightboxCleanUpResult     = array();
$lightboxItemCleanUpResult = array();

if ( $replaceInvalidOwnerWithID !== false )
{
    $lightboxCleanUpResult = eZLightbox::cleanup( eZLightbox::CLEANUP_OWNER_ACTION_REPLACE, $replaceInvalidOwnerWithID );
}
else
{
    $lightboxCleanUpResult = eZLightbox::cleanup( eZLightbox::CLEANUP_OWNER_ACTION_REMOVE, $replaceInvalidOwnerWithID );
}

print_r( $lightboxCleanUpResult );

if ( $removeInvalidItems )
{
    $lightboxItemCleanUpResult = eZLightboxObjectItem::cleanUpAllItems();
    print_r( $lightboxItemCleanUpResult );
}

if ( !$isQuiet )
{
    if ( isset( $lightboxCleanUpResult['lightbox']['removed'] ) && count( $lightboxCleanUpResult['lightbox']['removed'] ) > 0 )
    {
        $cli->notice( '', implode( ', ', $lightboxCleanUpResult['lightbox']['removed'] ) );
    }
    if ( isset( $lightboxCleanUpResult['lightbox']['changed'] ) && count( $lightboxCleanUpResult['lightbox']['changed'] ) > 0 )
    {
        $cli->notice( '', implode( ', ', $lightboxCleanUpResult['lightbox']['changed'] ) );
    }
    if ( )
}

?>