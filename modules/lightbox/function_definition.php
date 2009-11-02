<?php

$FunctionList = array();

$FunctionList['list'] = array(
    'name'            => 'list',
    'call_method'     => array( 'include_file' => 'extension/ezlightbox/modules/lightbox/lightboxfunctioncollection.php',
                                'class'        => 'lightboxFunctionCollection',
                                'method'       => 'fetchList'
                              ),
    'parameter_type' => 'standard',
    'parameters'     => array( array( 'name'     => 'userID',
                                      'type'     => 'integer',
                                      'required' => false
                                    ),
                               array( 'name'     => 'asObject',
                                      'type'     => 'boolean',
                                      'required' => false
                                    ),
                               array( 'name'     => 'sortBy',
                                      'type'     => 'array',
                                      'required' => false
                                    ),
                               array( 'name'     => 'offset',
                                      'type'     => 'integer',
                                      'required' => false
                                    ),
                               array( 'name'     => 'limit',
                                      'type'     => 'integer',
                                      'required' => false
                                    ),
                               array( 'name'     => 'otherMasks',
                                      'type'     => 'array',
                                      'required' => false
                                    )
                             )
);

$FunctionList['listOwn'] = array(
    'name'            => 'listOwn',
    'call_method'     => array( 'include_file' => 'extension/ezlightbox/modules/lightbox/lightboxfunctioncollection.php',
                                'class'        => 'lightboxFunctionCollection',
                                'method'       => 'fetchListOwn'
                              ),
    'parameter_type' => 'standard',
    'parameters'     => array( array( 'name'     => 'userID',
                                      'type'     => 'integer',
                                      'required' => false
                                    ),
                               array( 'name'     => 'asObject',
                                      'type'     => 'boolean',
                                      'required' => false
                                    ),
                               array( 'name'     => 'sortBy',
                                      'type'     => 'array',
                                      'required' => false
                                    ),
                               array( 'name'     => 'offset',
                                      'type'     => 'integer',
                                      'required' => false
                                    ),
                               array( 'name'     => 'limit',
                                      'type'     => 'integer',
                                      'required' => false
                                    )
                             )
);

$FunctionList['listOther'] = array(
    'name'            => 'listOther',
    'call_method'     => array( 'include_file' => 'extension/ezlightbox/modules/lightbox/lightboxfunctioncollection.php',
                                'class'        => 'lightboxFunctionCollection',
                                'method'       => 'fetchListOther'
                              ),
    'parameter_type' => 'standard',
    'parameters'     => array( array( 'name'     => 'userID',
                                      'type'     => 'integer',
                                      'required' => false
                                    ),
                               array( 'name'     => 'asObject',
                                      'type'     => 'boolean',
                                      'required' => false
                                    ),
                               array( 'name'     => 'sortBy',
                                      'type'     => 'array',
                                      'required' => false
                                    ),
                               array( 'name'     => 'offset',
                                      'type'     => 'integer',
                                      'required' => false
                                    ),
                               array( 'name'     => 'limit',
                                      'type'     => 'integer',
                                      'required' => false
                                    ),
                               array( 'name'     => 'accessKeys',
                                      'type'     => 'array',
                                      'required' => false
                                    )
                             )
);

$FunctionList['count'] = array(
    'name'            => 'count',
    'call_method'     => array( 'include_file' => 'extension/ezlightbox/modules/lightbox/lightboxfunctioncollection.php',
                                'class'        => 'lightboxFunctionCollection',
                                'method'       => 'fetchListCount'
                              ),
    'parameter_type' => 'standard',
    'parameters'     => array( array( 'name'     => 'userID',
                                      'type'     => 'integer',
                                      'required' => false
                                    ),
                               array( 'name'     => 'otherMasks',
                                      'type'     => 'array',
                                      'required' => false
                                    )
                             )
);

$FunctionList['countOwn'] = array(
    'name'            => 'countOwn',
    'call_method'     => array( 'include_file' => 'extension/ezlightbox/modules/lightbox/lightboxfunctioncollection.php',
                                'class'        => 'lightboxFunctionCollection',
                                'method'       => 'fetchListCountOwn'
                              ),
    'parameter_type' => 'standard',
    'parameters'     => array( array( 'name'     => 'userID',
                                      'type'     => 'integer',
                                      'required' => false
                                    )
                             )
);

$FunctionList['countOther'] = array(
    'name'            => 'countOther',
    'call_method'     => array( 'include_file' => 'extension/ezlightbox/modules/lightbox/lightboxfunctioncollection.php',
                                'class'        => 'lightboxFunctionCollection',
                                'method'       => 'fetchListCountOther'
                              ),
    'parameter_type' => 'standard',
    'parameters'     => array( array( 'name'     => 'userID',
                                      'type'     => 'integer',
                                      'required' => false
                                    ),
                               array( 'name'     => 'accessKeys',
                                      'type'     => 'array',
                                      'required' => false
                                    )
                             )
);

$FunctionList['sessionKey'] = array(
    'name'            => 'sessionKey',
    'call_method'     => array( 'include_file' => 'extension/ezlightbox/modules/lightbox/lightboxfunctioncollection.php',
                                'class'        => 'lightboxFunctionCollection',
                                'method'       => 'fetchSessionKey'
                              ),
    'parameter_type' => 'standard',
    'parameters'     => array( array( 'name'     => 'userID',
                                      'type'     => 'integer',
                                      'required' => false
                                    )
                             )
);

$FunctionList['basketItemCount'] = array(
    'name'            => 'basketItemCount',
    'call_method'     => array( 'include_file' => 'extension/ezlightbox/modules/lightbox/lightboxfunctioncollection.php',
                                'class'        => 'lightboxFunctionCollection',
                                'method'       => 'fetchBasketItemCount'
                              ),
    'parameter_type' => 'standard',
    'parameters'     => array()
);

$FunctionList['access_list'] = array(
    'name'            => 'access_list',
    'call_method'     => array( 'include_file' => 'extension/ezlightbox/modules/lightbox/lightboxfunctioncollection.php',
                                'class'        => 'lightboxFunctionCollection',
                                'method'       => 'fetchAccessList'
                              ),
    'parameter_type' => 'standard',
    'parameters'     => array( array( 'name'     => 'userID',
                                      'type'     => 'integer',
                                      'required' => false
                                    ),
                               array( 'name'     => 'asObject',
                                      'type'     => 'boolean',
                                      'required' => false
                                    ),
                               array( 'name'     => 'sortBy',
                                      'type'     => 'array',
                                      'required' => false
                                    ),
                               array( 'name'     => 'offset',
                                      'type'     => 'integer',
                                      'required' => false
                                    ),
                               array( 'name'     => 'limit',
                                      'type'     => 'integer',
                                      'required' => false
                                    )
                             )
);

$FunctionList['object'] = array(
    'name'            => 'object',
    'call_method'     => array( 'include_file' => 'extension/ezlightbox/modules/lightbox/lightboxfunctioncollection.php',
                                'class'        => 'lightboxFunctionCollection',
                                'method'       => 'fetchLightbox'
                              ),
    'parameter_type' => 'standard',
    'parameters'     => array( array( 'name'     => 'id',
                                      'type'     => 'integer',
                                      'required' => true
                                    ),
                               array( 'name'     => 'asObject',
                                      'type'     => 'boolean',
                                      'required' => false
                                    )
                             )
);

$FunctionList['objectItems'] = array(
    'name'           => 'objectItems',
    'call_method'    => array( 'include_file' => 'extension/ezlightbox/modules/lightbox/lightboxfunctioncollection.php',
                               'class'        => 'lightboxFunctionCollection',
                               'method'       => 'fetchLightboxObjectItems'
                             ),
    'parameter_type' => 'standard',
    'parameters'     => array()
);

$FunctionList['accessKeys'] = array(
    'name'           => 'accessKeys',
    'call_method'    => array( 'include_file' => 'extension/ezlightbox/modules/lightbox/lightboxfunctioncollection.php',
                               'class'        => 'lightboxFunctionCollection',
                               'method'       => 'fetchLightboxAccessKeys'
                             ),
    'parameter_type' => 'standard',
    'parameters'     => array()
);

$FunctionList['accessKeyByName'] = array(
    'name'           => 'accessKeyByName',
    'call_method'    => array( 'include_file' => 'extension/ezlightbox/modules/lightbox/lightboxfunctioncollection.php',
                               'class'        => 'lightboxFunctionCollection',
                               'method'       => 'fetchLightboxAccessKeyByName'
                             ),
    'parameter_type' => 'standard',
    'parameters'     => array( array( 'name'     => 'name',
                                      'type'     => 'string',
                                      'required' => true
                                    )
                             )
);

$FunctionList['accessKeyFlags'] = array(
    'name'           => 'accessKeyFlags',
    'call_method'    => array( 'include_file' => 'extension/ezlightbox/modules/lightbox/lightboxfunctioncollection.php',
                               'class'        => 'lightboxFunctionCollection',
                               'method'       => 'fetchLightboxAccessKeyFlags'
                             ),
    'parameter_type' => 'standard',
    'parameters'     => array()
);

$FunctionList['accessKeyByFlag'] = array(
    'name'           => 'accessKeyByFlag',
    'call_method'    => array( 'include_file' => 'extension/ezlightbox/modules/lightbox/lightboxfunctioncollection.php',
                               'class'        => 'lightboxFunctionCollection',
                               'method'       => 'fetchLightboxAccessKeyByFlag'
                             ),
    'parameter_type' => 'standard',
    'parameters'     => array( array( 'name'     => 'flag',
                                      'type'     => 'string',
                                      'required' => true
                                    )
                             )
);

$FunctionList['itemMoveDirections'] = array(
    'name'           => 'itemMoveDirections',
    'call_method'    => array( 'include_file' => 'extension/ezlightbox/modules/lightbox/lightboxfunctioncollection.php',
                               'class'        => 'lightboxFunctionCollection',
                               'method'       => 'fetchLightboxItemMoveDirections'
                             ),
    'parameter_type' => 'standard',
    'parameters'     => array()
);

$FunctionList['itemMoveDirectionsName'] = array(
    'name'           => 'itemMoveDirectionsByName',
    'call_method'    => array( 'include_file' => 'extension/ezlightbox/modules/lightbox/lightboxfunctioncollection.php',
                               'class'        => 'lightboxFunctionCollection',
                               'method'       => 'fetchLightboxItemMoveDirectionsByName'
                             ),
    'parameter_type' => 'standard',
    'parameters'     => array( array( 'name'     => 'name',
                                      'type'     => 'string',
                                      'required' => true
                                    )
                             )
);

?>
