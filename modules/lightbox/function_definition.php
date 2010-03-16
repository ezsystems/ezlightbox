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

?>
