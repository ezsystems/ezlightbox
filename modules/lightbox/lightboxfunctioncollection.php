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

    function fetchListOwn( $userID = false, $asObject = true, $sortBy = false,
                           $offset = false, $limit = false
                         )
    {
        if ( !$userID )
        {
            $userID = eZUser::currentUserID();
        }
        return array( 'result' => eZLightbox::fetchOwnListByUser( $userID, $asObject, false,
                                                                  $sortBy, $limit, $offset
                                                                )
                    );
    }

    function fetchListOther( $userID = false, $asObject = true, $sortBy = false,
                             $offset = false, $limit = false, $accessKeys = array()
                           )
    {
        if ( !$userID )
        {
            $userID = eZUser::currentUserID();
        }
        $idList          = array();
        $otherLightboxes = eZLightboxAccess::fetchListByUser( $userID, $asObject, false,
                                                              $sortBy, $limit, $offset, $accessKeys
                                                            );
        foreach ( $otherLightboxes as $index => $lightboxArray )
        {
            $idList[] = $lightboxArray['lightbox_id'];
        }
        $otherLightboxes = array();
        foreach ( array_unique( $idList ) as $lightboxID )
        {
            $otherLightboxes[] = eZLightbox::fetch( $lightboxID );
        }
        return array( 'result' => $otherLightboxes );
    }

    function fetchListCount( $userID = false, $otherMasks = array() )
    {
        if ( !$userID )
        {
            $userID = eZUser::currentUserID();
        }
        return array( 'result' => (int)eZLightbox::fetchListByUser( $userID, false, true, false, false, false, $otherMasks ) );
    }

    function fetchListCountOwn( $userID = false )
    {
        if ( !$userID )
        {
            $userID = eZUser::currentUserID();
        }
        return array( 'result' => (int)eZLightbox::fetchOwnListByUser( $userID, false, true, false, false, false ) );
    }

    function fetchListCountOther( $userID = false, $asObject = true, $sortBy = false,
                                  $offset = false, $limit = false, $accessKeys = array()
                                )
    {
        if ( !$userID )
        {
            $userID = eZUser::currentUserID();
        }
        return array( 'result' => (int)eZLightboxAccess::fetchListByUser( $userID, $asObject, true,
                                                                          $sortBy, $limit, $offset, $accessKeys
                                                                        )
                    );
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

    function fetchLightboxObjectItems()
    {
        return array( 'result' => eZLightboxObject::availableItems() );
    }

    function fetchLightboxAccessKeys()
    {
        return array( 'result' => eZLightboxAccess::accessKeys() );
    }

    function fetchLightboxAccessKeyByName( $accessKeyName )
    {
        return array( 'result' => eZLightboxAccess::accessKeyByName( $accessKeyName ) );
    }

    function fetchLightboxAccessKeyFlags()
    {
        return array( 'result' => eZLightboxAccess::accessKeyFlags() );
    }

    function fetchLightboxAccessKeyByFlag( $accessKeyFlag )
    {
        return array( 'result' => eZLightboxAccess::accessKeyByFlag( $accessKeyFlag ) );
    }

    function fetchLightboxItemMoveDirections()
    {
        return array( 'result' => eZLightbox::itemMoveDirections() );
    }

    function fetchLightboxItemMoveDirectionsByName( $directionName )
    {
        return array( 'result' => eZLightbox::itemMoveDirectionsByName( $directionName ) );
    }

}

?>
