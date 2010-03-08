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

$Module = array( 'name' => 'lightbox' );

$ViewList = array();

$ViewList['add'] = array(
    'script'                  => 'add.php',
    'params'                  => array( 'LightboxID', 'ItemID', 'TypeID' ),
    'functions'               => array( 'add' ),
    'default_navigation_part' => 'ezlightboxnavigationpart',
    'ui_context'              => 'browse'
);

$ViewList['create'] = array(
    'script'                  => 'create.php',
    'functions'               => array( 'create' ),
    'default_navigation_part' => 'ezlightboxnavigationpart',
    'ui_context'              => 'edit'
);

$ViewList['edit'] = array(
    'script'                  => 'edit.php',
    'params'                  => array( 'LightboxID' ),
    'functions'               => array( 'edit' ),
    'default_navigation_part' => 'ezlightboxnavigationpart',
    'ui_context'              => 'edit'
);

$ViewList['view'] = array(
    'script'                  => 'view.php',
    'params'                  => array( 'ViewMode', 'LightboxID' ),
    'functions'               => array( 'view' ),
    'default_navigation_part' => 'ezlightboxnavigationpart',
    'ui_context'              => 'view'
);

$ViewList['send'] = array(
    'script'                  => 'send.php',
    'params'                  => array( 'LightboxID' ),
    'functions'               => array( 'view' ),
    'default_navigation_part' => 'ezlightboxnavigationpart',
    'ui_context'              => 'browse'
);

$ClassID = array(
    'name'      => 'Class',
    'values'    => array(),
    'path'      => 'classes/',
    'file'      => 'ezcontentclass.php',
    'class'     => 'eZContentClass',
    'function'  => 'fetchList',
    'parameter' => array( 0, false, false, array( 'name' => 'asc' ) )
);

$Owner = array(
    'name'   => 'Owner',
    'values' => array( array( 'Name'  => 'Self',
                              'value' => '1'
                            )
                     )
);

$GrantOwner = array(
    'name'   => 'Owner',
    'values' => array( array( 'Name'  => 'Self',
                              'value' => '1'
                            ),
                       array( 'Name'  => 'Granted',
                              'value' => '2'
                            )
                     )
);

$FunctionList = array();

$FunctionList['add']    = array( 'Owner' => $Owner, 'Class' => $ClassID );

$FunctionList['create'] = array();

$FunctionList['edit']   = array( 'Owner' => $Owner );

$FunctionList['view']   = array( 'Owner' => $GrantOwner );

$FunctionList['send']   = array( 'Owner' => $Owner );

$FunctionList['grant']  = array( 'Owner' => $Owner );

?>
