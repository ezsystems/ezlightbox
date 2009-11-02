<?php

class eZContentObjectLightboxObjectItem extends eZLightboxObjectItem implements ieZLightboxObjectItem
{

    public function getID()
    {
        return 1;
    }

    public function itemIDisValid( $itemID )
    {
        return is_numeric( $itemID );
    }

}

?>