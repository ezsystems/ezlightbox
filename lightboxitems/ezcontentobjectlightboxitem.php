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

class eZContentObjectLightboxObjectItem extends eZLightboxObjectItem implements ieZLightboxObjectItem
{

    const ITEM_ID = 1;

    private $validItemIDList = array();

    public function getID()
    {
        return eZContentObjectLightboxObjectItem::ITEM_ID;
    }

    public function fetchItemObjectById( $itemID )
    {
        return eZContentObject::fetch( $itemID );
    }

    public function itemIDisValid( $itemID )
    {
        if ( isset( $this->validItemIDList[$itemID] ) )
        {
            return $this->validItemIDList[$itemID];
        }
        $this->validItemIDList[$itemID] = false;
        if ( is_numeric( $itemID ) )
        {
            $contentObject = eZContentObject::fetch( (int)$itemID, false );
            if ( is_array( $contentObject ) && isset( $contentObject['id'] ) )
            {
                $this->validItemIDList[$itemID] = true;
            }
        }
        return $this->validItemIDList[$itemID];
    }

    public function itemObjectIsValid( $itemObject )
    {
        if ( $itemObject instanceof eZContentObject )
        {
            return true;
        }
        return false;
    }

    public function getPermissionFunctionListByName( $functionName )
    {
        $ClassID = array(
            'name'      => 'Class',
            'values'    => array(),
            'path'      => 'classes/',
            'file'      => 'ezcontentclass.php',
            'class'     => 'eZContentClass',
            'function'  => 'fetchList',
            'parameter' => array( 0, false, false, array( 'name' => 'asc' ) )
        );

        $FunctionList = array();

        switch ( $functionName )
        {
            case 'add':
            {
                $FunctionList[] = array( 'Class' => $ClassID );
                return $FunctionList;
            }
            break;
        }
        return false;
    }

    public function policyMatchItemObject( $policy, $itemObject )
    {
        $hasTypeLimit  = false;
        $isTypeOk      = false;
        $hasClassLimit = false;
        $isClassOk     = false;
        $type_limit    = null;
        foreach ( array_keys( $policy ) as $limitation )
        {
            switch ( $limitation )
            {
                case 'Type':
                {
                    $hasTypeLimit = true;
                    if ( in_array( $this->getID(), $policy[ $limitation ] ) )
                    {
                        $isTypeOk = true;
                    }
                }
                case $this->getItemType() . '_Class':
                {
                    $hasClassLimit = true;
                    if ( in_array( $itemObject->attribute( 'contentclass_id'), $policy[ $limitation ] ) )
                    {
                        $isClassOk = true;
                    }
                }
                break;
            }
        }
        if ( !$hasClassLimit )
        {
            $isClassOk = true;
        }
        if ( !$hasTypeLimit )
        {
            $isTypeOk = true;
        }
        if ( $isClassOk && $isTypeOk )
        {
            return true;
        }
        return false;
    }

    public function cleanUp()
    {
        $query             = 'SELECT item_id, lightbox_id FROM ezlightbox_object WHERE type_id=' . eZContentObjectLightboxObjectItem::ITEM_ID . ' AND
                                     item_id NOT IN ( SELECT id FROM ezcontentobject )';
        $db                = eZDB::instance();
        $invalidItemIDList = $db->arrayQuery( $query );
        $result            = array( 'removed' => array(), 'skipped' => array() );
        if ( is_array( $invalidItemIDList ) && count( $invalidItemIDList ) > 0 )
        {
            foreach ( $invalidItemIDList as $invalidItemIDItem )
            {
                $lightboxObjectObject = eZLightboxObject::fetch( $invalidItemIDItem['lightbox_id'], $invalidItemIDItem['item_id'], eZContentObjectLightboxObjectItem::ITEM_ID );
                if ( is_object( $lightboxObjectObject ) )
                {
                    $db->begin();
                    $lightboxObjectObject->purge();
                    $db->commit();
                    $result['removed'][] = array( 'lightbox_id' => $invalidItemIDItem['lightbox_id'],
                                                  'item_id' => $invalidItemIDItem['item_id'],
                                                  'type_id' => eZContentObjectLightboxObjectItem::ITEM_ID
                                                );
                }
                else
                {
                    $result['skipped'][] = array( 'lightbox_id' => $invalidItemIDItem['lightbox_id'], 'item_id' => $invalidItemIDItem['item_id'] );
                }
            }
        }
        return $result;
    }

}

?>
