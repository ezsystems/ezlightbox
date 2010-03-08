<?php
//
// Created on: <2010-01-21 10:01:28 ab>
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

$eZTemplateFunctionArray   = array();

$eZTemplateFunctionArray[] = array( 'function'       => 'eZLightboxForwardInit',
                                    'function_names' => array( 'lightbox_item_view_gui' )
                                  );

if ( !function_exists( 'eZLightboxForwardInit' ) )
{

    function eZLightboxForwardInit()
    {
        $forward_rules = array(
            'lightbox_item_view_gui' => array( 'template_root'    => 'lightbox/item/view',
                                               'input_name'       => 'itemObject',
                                               'output_name'      => 'itemObject',
                                               'namespace'        => 'LightboxView',
                                               'attribute_keys'   => array( 'lightboxid' => array( 'lightbox_id' ),
                                                                            'itemid'     => array( 'item_id' ),
                                                                            'itemtypeid' => array( 'type_id' )
                                                                          ),
                                               'attribute_access' => array(),
                                               'use_views'        => 'view'
                                             )
        );
        return new eZObjectForwarder( $forward_rules );

    }

}

?>