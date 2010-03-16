<?php

require_once( 'autoload.php' );

class lightboxFunctionCollection
{

    function fetchList( $userID = false, $asObject = true, $sortBy = false,
                        $offset = false, $limit = false, $otherMasks = array()
                      )
    {
        if ( !$userID )
        {
            $userID = eZUser::currentUserID();
        }
        return array( 'result' => eZLightbox::fetchListByUser( $userID, $asObject, false,
                                                               $sortBy, $offset, $limit, $otherMasks
                                                             )
                    );
    }

    function fetchListCount( $userID = false, $otherMasks = array() )
    {
        if ( !$userID )
        {
            $userID = eZUser::currentUserID();
        }
        return array( 'result' => (int)eZLightbox::fetchListByUser( $userID, false, true, false, false, false, $otherMasks ) );
    }

    function fetchSessionKey( $userID = false )
    {
        if ( !$userID )
        {
            $userID = eZUser::currentUserID();
        }
        return array( 'result' => eZLightbox::createSessionKey( $userID ) );
    }

    function fetchBasketItemCount()
    {
        $http       = eZHTTPTool::instance();
        $sessionID  = $http->sessionID();
        $db    = eZDB::instance();
        $query = 'SELECT count( a.id ) count
                  FROM   ezproductcollection_item a, ezbasket b
                  WHERE  a.productcollection_id=b.productcollection_id AND
                         b.session_id="' . $sessionID . '"';
        $rows = $db->arrayQuery( $query );
        if ( isset( $rows[0]['count'] ) )
        {
            return array( 'result' => $rows[0]['count'] );
        }
        return array( 'result' => 0 );
    }

    function fetchAccessList( $userID = false, $asObject = true, $sortBy = false,
                              $offset = false, $limit = false
                            )
    {
        if ( !$userID )
        {
            $userID = eZUser::currentUserID();
        }
        return array( 'result' => eZLightboxAccess::fetchListByUser( $userID, $asObject, false,
                                                                     $sortBy, $offset, $limit
                                                                   )
                    );
    }

    function fetchLightbox( $id, $asObject = true )
    {
        return array( 'result' => eZLightbox::fetch( $id, $asObject ) );
    }

}

?>
