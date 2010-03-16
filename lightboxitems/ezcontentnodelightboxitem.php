<?php

class eZContentNodeLightboxObjectItem extends eZLightboxObjectItem implements ieZLightboxObjectItem
{

    const ITEM_ID = 2;

    public function getID()
    {
        return eZContentNodeLightboxObjectItem::ITEM_ID;
    }

    public function itemIDisValid( $itemID )
    {
        return is_numeric( $itemID );
    }

    public function cleanUp()
    {
        $query             = 'SELECT item_id, lightbox_id FROM ezlightbox_object WHERE type_id=' . eZContentNodeLightboxObjectItem::ITEM_ID . ' AND
                                     item_id NOT IN ( SELECT node_id FROM ezcontentobject_tree )';
        $db                = eZDB::instance();
        $invalidItemIDList = $db->arrayQuery( $query );
        $result            = array( 'removed' => array(), 'skipped' => array() );
        if ( is_array( $invalidItemIDList ) && count( $invalidItemIDList ) > 0 )
        {
            foreach ( $invalidItemIDList as $invalidItemIDItem )
            {
                $lightboxObjectObject = eZLightboxObject::fetch( $invalidItemIDItem['lightbox_id'], $invalidItemIDItem['item_id'], eZContentNodeLightboxObjectItem::ITEM_ID );
                if ( is_object( $lightboxObjectObject ) )
                {
                    $db->begin();
                    $lightboxObjectObject->purge();
                    $db->commit();
                    $result['removed'][] = array( 'lightbox_id' => $invalidItemIDItem['lightbox_id'],
                                                  'item_id' => $invalidItemIDItem['item_id'],
                                                  'type_id' => eZContentNodeLightboxObjectItem::ITEM_ID
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