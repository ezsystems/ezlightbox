<?php

$OperationList = array();

$OperationList['create'] = array(
    'name'                => 'create',
    'default_call_method' => array( 'include_file'   => 'extension/ezlightbox/modules/lightbox/lightboxoperationcollection.php',
                                    'class'          => 'lightboxOperationCollection'
                                  ),
    'parameter_type' => 'standard',
    'parameters'     => array( array( 'name'     => 'name',
                                      'type'     => 'string',
                                      'required' => true
                                    )
                             ),
    'keys'           => array(),
    'body'           => array( array( 'type' => 'trigger',
                                      'name' => 'pre_create',
                                      'keys' => array()
                                    ),
                               array( 'type'      => 'method',
                                      'name'      => 'create-lightbox',
                                      'frequency' => 'once',
                                      'method'    => 'createLightbox'
                                    ),
                               array( 'type' => 'trigger',
                                      'name' => 'post_create',
                                      'keys' => array()
                                    )
                             )
);

$OperationList['delete'] = array(
    'name'                => 'delete',
    'default_call_method' => array( 'include_file'   => 'extension/ezlightbox/modules/lightbox/lightboxoperationcollection.php',
                                    'class'          => 'lightboxOperationCollection'
                                  ),
    'parameter_type' => 'standard',
    'parameters'     => array( array( 'name'     => 'id',
                                      'type'     => 'integer',
                                      'required' => true
                                    )
                             ),
    'keys'           => array( 'id' ),
    'body'           => array( array( 'type' => 'trigger',
                                      'name' => 'pre_delete',
                                      'keys' => array( 'id' )
                                    ),
                               array( 'type'      => 'method',
                                      'name'      => 'delete-lightbox',
                                      'frequency' => 'once',
                                      'method'    => 'deleteLightbox'
                                    ),
                               array( 'type' => 'trigger',
                                      'name' => 'post_delete',
                                      'keys' => array( 'id' )
                                    )
                             )
);

$OperationList['empty'] = array(
    'name'                => 'empty',
    'default_call_method' => array( 'include_file'   => 'extension/ezlightbox/modules/lightbox/lightboxoperationcollection.php',
                                    'class'          => 'lightboxOperationCollection'
                                  ),
    'parameter_type' => 'standard',
    'parameters'     => array( array( 'name'     => 'id',
                                      'type'     => 'integer',
                                      'required' => true
                                    )
                             ),
    'keys'           => array( 'id' ),
    'body'           => array( array( 'type' => 'trigger',
                                      'name' => 'pre_delete',
                                      'keys' => array( 'id' )
                                    ),
                               array( 'type'      => 'method',
                                      'name'      => 'empty-lightbox',
                                      'frequency' => 'once',
                                      'method'    => 'emptyLightbox'
                                    ),
                               array( 'type' => 'trigger',
                                      'name' => 'post_delete',
                                      'keys' => array( 'id' )
                                    )
                             )
);

$OperationList['add'] = array(
    'name'                => 'add',
    'default_call_method' => array( 'include_file'   => 'extension/ezlightbox/modules/lightbox/lightboxoperationcollection.php',
                                    'class'          => 'lightboxOperationCollection'
                                  ),
    'parameter_type' => 'standard',
    'parameters'     => array( array( 'name'     => 'id',
                                      'type'     => 'integer',
                                      'required' => true
                                    ),
                               array( 'name'     => 'item_id',
                                      'type'     => 'integer',
                                      'required' => true
                                    ),
                               array( 'name'     => 'type_id',
                                      'type'     => 'integer',
                                      'required' => true
                                    )
                             ),
    'keys'           => array( 'id', 'item_id', 'type_id' ),
    'body'           => array( array( 'type' => 'trigger',
                                      'name' => 'pre_add',
                                      'keys' => array( 'id', 'item_id', 'type_id' )
                                    ),
                               array( 'type'      => 'method',
                                      'name'      => 'add-item',
                                      'frequency' => 'once',
                                      'method'    => 'addItem'
                                    ),
                               array( 'type' => 'trigger',
                                      'name' => 'post_add',
                                      'keys' => array( 'id', 'item_id', 'type_id' )
                                    )
                             )
);

$OperationList['remove'] = array(
    'name'                => 'remove',
    'default_call_method' => array( 'include_file'   => 'extension/ezlightbox/modules/lightbox/lightboxoperationcollection.php',
                                    'class'          => 'lightboxOperationCollection'
                                  ),
    'parameter_type' => 'standard',
    'parameters'     => array( array( 'name'     => 'id',
                                      'type'     => 'integer',
                                      'required' => true
                                    ),
                               array( 'name'     => 'item_id',
                                      'type'     => 'integer',
                                      'required' => true
                                    ),
                               array( 'name'     => 'type_id',
                                      'type'     => 'integer',
                                      'required' => true
                                    )
                             ),
    'keys'           => array( 'id', 'item_id', 'type_id' ),
    'body'           => array( array( 'type' => 'trigger',
                                      'name' => 'pre_remove',
                                      'keys' => array( 'id', 'item_id', 'type_id' )
                                    ),
                               array( 'type'      => 'method',
                                      'name'      => 'remove-item',
                                      'frequency' => 'once',
                                      'method'    => 'removeItem'
                                    ),
                               array( 'type' => 'trigger',
                                      'name' => 'post_remove',
                                      'keys' => array( 'id', 'item_id', 'type_id' )
                                    )
                             )
);

$OperationList['move'] = array(
    'name'                => 'move',
    'default_call_method' => array( 'include_file'   => 'extension/ezlightbox/modules/lightbox/lightboxoperationcollection.php',
                                    'class'          => 'lightboxOperationCollection'
                                  ),
    'parameter_type' => 'standard',
    'parameters'     => array( array( 'name'     => 'id',
                                      'type'     => 'integer',
                                      'required' => true
                                    ),
                               array( 'name'     => 'target_id',
                                      'type'     => 'integer',
                                      'required' => true
                                    ),
                               array( 'name'     => 'item_id',
                                      'type'     => 'integer',
                                      'required' => true
                                    ),
                               array( 'name'     => 'type_id',
                                      'type'     => 'integer',
                                      'required' => true
                                    )
                             ),
    'keys'           => array( 'id', 'target_id', 'item_id', 'type_id' ),
    'body'           => array( array( 'type' => 'trigger',
                                      'name' => 'pre_move',
                                      'keys' => array( 'id', 'target_id', 'item_id', 'type_id' )
                                    ),
                               array( 'type'      => 'method',
                                      'name'      => 'move-item',
                                      'frequency' => 'once',
                                      'method'    => 'moveItem'
                                    ),
                               array( 'type' => 'trigger',
                                      'name' => 'post_move',
                                      'keys' => array( 'id', 'target_id', 'item_id', 'type_id' )
                                    )
                             )
);

$OperationList['send'] = array(
    'name'                => 'send',
    'default_call_method' => array( 'include_file'   => 'extension/ezlightbox/modules/lightbox/lightboxoperationcollection.php',
                                    'class'          => 'lightboxOperationCollection'
                                  ),
    'parameter_type' => 'standard',
    'parameters'     => array( array( 'name'     => 'id',
                                      'type'     => 'integer',
                                      'required' => true
                                    ),
                               array( 'name'     => 'user_id',
                                      'type'     => 'object',
                                      'required' => true
                                    ),
                               array( 'name'     => 'access_mask',
                                      'type'     => 'integer',
                                      'required' => true
                                    ),
                               array( 'name'     => 'mail_data',
                                      'type'     => 'array',
                                      'required' => true
                                    )
                             ),
    'keys'           => array( 'id', 'user_id' ),
    'body'           => array( array( 'type' => 'trigger',
                                      'name' => 'pre_send',
                                      'keys' => array( 'id', 'user_id' )
                                    ),
                               array( 'type'      => 'method',
                                      'name'      => 'create-lightbox-access',
                                      'frequency' => 'once',
                                      'method'    => 'createLightboxAccess'
                                    ),
                               array( 'type'      => 'method',
                                      'name'      => 'send-lightbox-email',
                                      'frequency' => 'once',
                                      'method'    => 'sendLightboxEMail'
                                    ),
                               array( 'type' => 'trigger',
                                      'name' => 'post_send',
                                      'keys' => array( 'id', 'user_id' )
                                    )
                             )
);

$OperationList['removeUsers'] = array(
    'name'                => 'removeUsers',
    'default_call_method' => array( 'include_file'   => 'extension/ezlightbox/modules/lightbox/lightboxoperationcollection.php',
                                    'class'          => 'lightboxOperationCollection'
                                  ),
    'parameter_type' => 'standard',
    'parameters'     => array( array( 'name'     => 'id',
                                      'type'     => 'integer',
                                      'required' => true
                                    ),
                               array( 'name'     => 'user_ids',
                                      'type'     => 'array',
                                      'required' => true
                                    )
                             ),
    'keys'           => array( 'id', 'user_ids' ),
    'body'           => array( array( 'type' => 'trigger',
                                      'name' => 'pre_remove_users',
                                      'keys' => array( 'id', 'user_ids' )
                                    ),
                               array( 'type'      => 'method',
                                      'name'      => 'remove-lightbox-access',
                                      'frequency' => 'once',
                                      'method'    => 'removeLightboxAccess'
                                    ),
                               array( 'type' => 'trigger',
                                      'name' => 'post_remove_users',
                                      'keys' => array( 'id', 'user_ids' )
                                    )
                             )
);

?>