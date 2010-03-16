<?php

class eZContentNodeLightboxObjectItem extends eZLightboxObjectItem implements ieZLightboxObjectItem
{

    public function getID()
    {
        return 2;
    }

    public function itemIDisValid( $itemID )
    {
        return is_numeric( $itemID );
    }

}

?>