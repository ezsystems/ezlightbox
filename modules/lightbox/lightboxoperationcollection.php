<?php

require_once( 'autoload.php' );

class lightboxOperationCollection
{

    private static $__messages = array();

    function lightboxOperationCollection()
    {
    }

    public static function createLightbox( $lightbox_name )
    {
        $status      = eZModuleOperationInfo::STATUS_CANCELLED;
        $messages    = array();
        $lightbox_id = false;
        if ( eZLightbox::canCreate() )
        {
            $lightbox_name = trim( $lightbox_name );
            if ( $lightbox_name != "" )
            {
                $lightboxObject = eZLightbox::create( $lightbox_name );
                if ( is_object( $lightboxObject ) )
                {
                    if ( isset( $_SESSION ) )
                    {
                        $http = eZHTTPTool::instance();
                        $http->removeSessionVariable( eZLightbox::PREFERENCE_SESSION_HASHKEY );
                    }
                    $lightboxObject->store();
                    $lightboxListCount = eZLightbox::fetchListByUser( eZUser::currentUserID(), false, true );
                    if ( $lightboxListCount == 1 )
                    {
                        eZPreferences::setValue( eZLightbox::PREFERENCE_CURRENT_LIGHTBOX,
                                                 $lightboxObject->attribute( 'id' )
                                               );
                    }
                    $lightbox_id = $lightboxObject->attribute( 'id' );
                    $messages[]   = ezi18n( 'eZLightboxOperationCollection::createLightbox',
                                            'Successfully created new lightbox.'
                                          );
                    $status = eZModuleOperationInfo::STATUS_CONTINUE;
                }
                else
                {
                    $messages[]   = ezi18n( 'eZLightboxOperationCollection::createLightbox',
                                            'An error occured while trying to create the lightbox.'
                                          );
                }
            }
            else
            {
                $messages[]   = ezi18n( 'eZLightboxOperationCollection::createLightbox',
                                        'Lightbox name %1 is invalid.', null, array( $lightbox_name )
                                      );
            }
        }
        else
        {
            $messages[] = ezi18n( 'eZLightboxOperationCollection::createLightbox',
                                  'You do not have the permission to create a lightbox.'
                                );
        }
        return array( 'status'      => $status,
                      'messages'    => $messages,
                      'lightbox_id' => $lightbox_id
                    );
    }

    public static function deleteLightbox( $lightbox_id )
    {
        $status   = eZModuleOperationInfo::STATUS_CANCELLED;
        $messages = array();
        $lightboxObject = eZLightbox::fetch( $lightbox_id );
        if ( is_object( $lightboxObject ) )
        {
            if ( $lightboxObject->attribute( 'can_edit' ) )
            {
                if ( isset( $_SESSION ) )
                {
                    $http = eZHTTPTool::instance();
                    $http->removeSessionVariable( eZLightbox::PREFERENCE_SESSION_HASHKEY );
                }
                $currentLightboxID = eZPreferences::value( eZLightbox::PREFERENCE_CURRENT_LIGHTBOX );
                $db = eZDB::instance();
                $db->begin();
                $lightboxObject->purge();
                $db->commit();
                if ( $currentLightboxID == $lightbox_id )
                {
                    $lightboxList = eZLightbox::fetchListByUser( eZUser::currentUserID() );
                    if ( is_array( $lightboxList ) && count( $lightboxList ) > 0 )
                    {
                        eZPreferences::setValue( eZLightbox::PREFERENCE_CURRENT_LIGHTBOX,
                                                 $lightboxList[0]->attribute( 'id' )
                                               );
                        $messages[] = ezi18n( 'eZLightboxOperationCollection::deleteLightbox',
                                              'Lightbox "%1" is now the current lightbox.',
                                              null, array( $lightboxList[0]->attribute( 'name' ) )
                                            );
                    }
                    else
                    {
                        eZPreferences::setValue( eZLightbox::PREFERENCE_CURRENT_LIGHTBOX,
                                                 null
                                               );
                        $messages[] = ezi18n( 'eZLightboxOperationCollection::deleteLightbox',
                                              'No more lightboxes available. No current lightbox set.'
                                            );
                    }
                }
                $messages[] = ezi18n( 'eZLightboxOperationCollection::deleteLightbox',
                                      'Successfully deleted lightbox with ID %1.',
                                       null, array( $lightbox_id )
                                    );
                $status = eZModuleOperationInfo::STATUS_CONTINUE;
            }
            else
            {
                $messages[] = ezi18n( 'eZLightboxOperationCollection::deleteLightbox',
                                      'The lightbox with ID %1 can not be deleted by the current user.',
                                       null, array( $lightbox_id )
                                    );
            }
        }
        else
        {
            $messages[] = ezi18n( 'eZLightboxOperationCollection::deleteLightbox',
                                  'A lightbox with ID %1 does not exist.', null, array( $lightbox_id )
                                );
        }
        return array( 'status'   => $status,
                      'messages' => $messages
                    );
    }

    public static function emptyLightbox( $lightbox_id )
    {
        $status   = eZModuleOperationInfo::STATUS_CANCELLED;
        $messages = array();
        $lightboxObject = eZLightbox::fetch( $lightbox_id );
        if ( is_object( $lightboxObject ) )
        {
            if ( $lightboxObject->attribute( 'can_edit' ) )
            {
                $lightboxObjects = eZLightboxObject::fetchListByLightbox( $lightbox_id );
                if ( is_array( $lightboxObjects ) )
                {
                    if ( isset( $_SESSION ) )
                    {
                        $http = eZHTTPTool::instance();
                        $http->removeSessionVariable( eZLightbox::PREFERENCE_SESSION_HASHKEY );
                    }
                    $db = eZDB::instance();
                    $db->begin();
                    foreach ( $lightboxObjects as $lightboxObject )
                    {
                        $lightboxObject->purge();
                    }
                    $db->commit();
                    $status = eZModuleOperationInfo::STATUS_CONTINUE;
                }
                else
                {
                    $messages[] = ezi18n( 'eZLightboxOperationCollection::emptyLightbox',
                                          'Failed to fetch content of lightbox with ID %1.',
                                           null, array( $lightbox_id )
                                        );
                }
            }
            else
            {
                $messages[] = ezi18n( 'eZLightboxOperationCollection::emptyLightbox',
                                      'The lightbox with ID %1 can not be emptied by the current user.',
                                       null, array( $lightbox_id )
                                    );
            }
        }
        else
        {
            $messages[] = ezi18n( 'eZLightboxOperationCollection::emptyLightbox',
                                  'A lightbox with ID %1 does not exist.', null, array( $lightbox_id )
                                );
        }
        return array( 'status'   => $status,
                      'messages' => $messages
                    );
    }

    public static function addItem( $lightbox_id, $item_id, $type_id )
    {
        $status         = eZModuleOperationInfo::STATUS_CANCELLED;
        $messages       = array();
        $lightboxObject = eZLightbox::fetch( $lightbox_id );
        if ( is_object( $lightboxObject ) )
        {
            $canAdd = false;
            switch ( $type_id )
            {
                case eZLightboxObject::TYPE_OBJECT_ID:
                    $contentObject = eZContentObject::fetch( $item_id );
                    if ( is_object( $contentObject ) )
                    {
                        $classList = $lightboxObject->attribute( 'can_add_class_list' );
                        if ( is_array( $classList ) && in_array( $contentObject->attribute( 'contentclass_id' ), $classList ) )
                        {
                            $canAdd = true;
                        }
                        else
                        {
                            $messages[] = ezi18n( 'eZLightboxOperationCollection::addItem',
                                                  'The current user is not allowed to add content object items of this class to a lightbox.',
                                                  null, array( $lightbox_id )
                                                );
                        }
                    }
                    else
                    {
                        $messages[] = ezi18n( 'eZLightboxOperationCollection::addItem',
                                              'No content object item with ID %1 found.',
                                              null, array( $item_id )
                                            );
                    }
                    break;
                case eZLightboxObject::TYPE_NODE_ID:
                    $nodeObject = eZContentObjectTreeNode::fetch( $item_id );
                    if ( is_object( $nodeObject ) )
                    {
                        $canAdd = true;
                    }
                    else
                    {
                        $messages[] = ezi18n( 'eZLightboxOperationCollection::addItem',
                                              'No content node item with ID %1 found.',
                                              null, array( $item_id )
                                            );
                    }
                    break;
                default:
                    $canAdd = true;
                    break;
            }
            if ( $canAdd )
            {
                $newObject = eZLightboxObject::create( $lightbox_id, $item_id, $type_id );
                if ( is_object( $newObject ) )
                {
                    if ( isset( $_SESSION ) )
                    {
                        $http = eZHTTPTool::instance();
                        $http->removeSessionVariable( eZLightbox::PREFERENCE_SESSION_HASHKEY );
                    }
                    $db = eZDB::instance();
                    $db->begin();
                    $newObject->store();
                    $db->commit();
                    $messages[] = ezi18n( 'eZLightboxOperationCollection::addItem',
                                          'Successfully added item of type %1 with ID %2 to lightbox with ID %3.',
                                          null, array( $type_id, $item_id, $lightbox_id )
                                        );
                    $status = eZModuleOperationInfo::STATUS_CONTINUE;
                }
                else
                {
                    $messages[] = ezi18n( 'eZLightboxOperationCollection::addItem',
                                          'Failed to add item of type %1 with ID %2 to lightbox with ID %3.',
                                          null, array( $type_id, $item_id, $lightbox_id )
                                        );
                }
            }
        }
        else
        {
            $messages[] = ezi18n( 'eZLightboxOperationCollection::addItem',
                                  'A lightbox with ID %1 does not exist.', null, array( $lightbox_id )
                                );
        }
        return array( 'status'   => $status,
                      'messages' => $messages
                    );
    }

    public static function removeItem( $lightbox_id, $item_id, $type_id )
    {
        $status         = eZModuleOperationInfo::STATUS_CANCELLED;
        $messages       = array();
        $lightboxObject = eZLightbox::fetch( $lightbox_id );
        if ( is_object( $lightboxObject ) )
        {
            $lightboxItemObject = eZLightboxObject::fetch( $lightbox_id, $item_id, $type_id );
            if ( is_object( $lightboxItemObject ) )
            {
                if ( isset( $_SESSION ) )
                {
                    $http = eZHTTPTool::instance();
                    $http->removeSessionVariable( eZLightbox::PREFERENCE_SESSION_HASHKEY );
                }
                $db = eZDB::instance();
                $db->begin();
                $lightboxItemObject->purge();
                $db->commit();
                $messages[] = ezi18n( 'eZLightboxOperationCollection::removeItem',
                                      'Successfully deleted item of type %1 with ID %2 from lightbox with ID %3.',
                                      null, array( $type_id, $item_id, $lightbox_id )
                                    );
                $status = eZModuleOperationInfo::STATUS_CONTINUE;
            }
            else
            {
                $messages[] = ezi18n( 'eZLightboxOperationCollection::removeItem',
                                      'No item of type %1 with ID %2 found in lightbox ID %3.',
                                       null, array( $type_id, $item_id, $lightbox_id )
                                    );
            }
        }
        else
        {
            $messages[] = ezi18n( 'eZLightboxOperationCollection::removeItem',
                                  'A lightbox with ID %1 does not exist.', null, array( $lightbox_id )
                                );
        }
        return array( 'status'   => $status,
                      'messages' => $messages
                    );
    }

    public static function moveItem( $lightbox_id, $target_lightbox_id, $item_id, $type_id )
    {
        $status         = eZModuleOperationInfo::STATUS_CANCELLED;
        $messages       = array();
        $lightboxObject = eZLightbox::fetch( $lightbox_id );
        if ( is_object( $lightboxObject ) )
        {
            $targetLightboxObject = eZLightbox::fetch( $target_lightbox_id );
            if ( is_object( $targetLightboxObject ) )
            {
                $lightboxItemObject = eZLightboxObject::fetch( $lightbox_id, $item_id, $type_id );
                if ( is_object( $lightboxItemObject ) )
                {
                    $targetLightboxItemObject = eZLightboxObject::fetch( $target_lightbox_id, $item_id, $type_id );
                    if ( !is_object( $targetLightboxItemObject ) )
                    {
                        $targetLightboxItemObject = eZLightboxObject::create( $target_lightbox_id, $item_id, $type_id );
                        if ( is_object( $targetLightboxItemObject ) )
                        {
                            if ( isset( $_SESSION ) )
                            {
                                $http = eZHTTPTool::instance();
                                $http->removeSessionVariable( eZLightbox::PREFERENCE_SESSION_HASHKEY );
                            }
                            $db = eZDB::instance();
                            $db->begin();
                            $lightboxItemObject->purge();
                            $targetLightboxItemObject->store();
                            $db->commit();
                            $messages[] = ezi18n( 'eZLightboxOperationCollection::moveItem',
                                                  'Successfully moved item of type %1 with ID %2 from lightbox ID %3 to lightbox ID %4.',
                                                  null, array( $type_id, $item_id, $lightbox_id, $target_lightbox_id )
                                                );
                            $status = eZModuleOperationInfo::STATUS_CONTINUE;
                        }
                        else
                        {
                        }
                    }
                    else
                    {
                    }
                }
                else
                {
                    $messages[] = ezi18n( 'eZLightboxOperationCollection::moveItem',
                                          'No item of type %1 with ID %2 found in lightbox ID %3.',
                                           null, array( $type_id, $item_id, $lightbox_id )
                                        );
                }
            }
            else
            {
                $messages[] = ezi18n( 'eZLightboxOperationCollection::moveItem',
                                      'A target lightbox with ID %1 does not exist.',
                                      null, array( $target_lightbox_id )
                                    );
            }
        }
        else
        {
            $messages[] = ezi18n( 'eZLightboxOperationCollection::moveItem',
                                  'A lightbox with ID %1 does not exist.', null, array( $lightbox_id )
                                );
        }
        return array( 'status'   => $status,
                      'messages' => $messages
                    );
    }

    public static function removeLightboxAccess( $lightbox_id, $user_ids )
    {
        $messages       =& lightboxOperationCollection::$__messages;
        $status         =  eZModuleOperationInfo::STATUS_CANCELLED;
        $messages       = array();
        $lightboxObject = eZLightbox::fetch( $lightbox_id );
        if ( is_object( $lightboxObject ) )
        {
            if ( is_array( $user_ids ) )
            {
                foreach ( $user_ids as $user_id )
                {
                    $lightboxAccessObject = eZLightboxAccess::fetch( $lightbox_id, $user_id );
                    if ( is_object( $lightboxAccessObject ) )
                    {
                        $db = eZDB::instance();
                        $db->begin();
                        $lightboxAccessObject->purge();
                        $db->commit();
                        $messages[] = ezi18n( 'eZLightboxOperationCollection::removeLightboxAccess',
                                              'Successfully removed user with ID %1 from access list for lightbox ID %2.',
                                              null, array( $user_id, $lightbox_id )
                                            );
                    }
                    else
                    {
                        $messages[] = ezi18n( 'eZLightboxOperationCollection::removeLightboxAccess',
                                              'User with ID %1 does not have access to lightbox ID %2.',
                                              null, array( $user_id, $lightbox_id )
                                            );
                    }
                }
                $status = eZModuleOperationInfo::STATUS_CONTINUE;
            }
            else
            {
                $messages[] = ezi18n( 'eZLightboxOperationCollection::removeLightboxAccess',
                                      'Unable to identify the users.'
                                    );
            }
        }
        else
        {
            $messages[] = ezi18n( 'eZLightboxOperationCollection::removeLightboxAccess',
                                  'A lightbox with ID %1 does not exist.', null, array( $lightbox_id )
                                );
        }
        return array( 'status'   => $status,
                      'messages' => $messages
                    );
    }

    public static function createLightboxAccess( $lightbox_id, $user_id, $access_mask, $email_data )
    {
        $messages            =& lightboxOperationCollection::$__messages;
        $lightboxIni         =  eZINI::instance( 'lightbox.ini' );
        $UserSendOwnLightbox =  false;
        if ( $lightboxIni->hasVariable( 'CommonSettings', 'UserSendOwnLightbox' ) &&
             $lightboxIni->variable( 'CommonSettings', 'UserSendOwnLightbox' ) == 'enabled'
           )
        {
            $UserSendOwnLightbox = true;
        }
        $selfSend = ( $user_id == eZUser::currentUserID() );
        if ( $UserSendOwnLightbox && $selfSend )
        {
            $messages[] = ezi18n( 'eZLightboxOperationCollection::createLightboxAccess',
                                  'No special access rights must be created when sending a lightbox to yourself.'
                                );
            return array( 'status'   => eZModuleOperationInfo::STATUS_CONTINUE,
                          'messages' => $messages
                        );
        }
        else if ( !$UserSendOwnLightbox && $selfSend )
        {
            $messages[] = ezi18n( 'eZLightboxOperationCollection::createLightboxAccess',
                                  'It is not allowed to send the lightbox to yourself'
                                );
            return array( 'status'   => eZModuleOperationInfo::STATUS_CANCELLED,
                          'messages' => $messages
                        );
        }

        $status         = eZModuleOperationInfo::STATUS_CANCELLED;
        $messages       = array();
        $lightboxObject = eZLightbox::fetch( $lightbox_id );
        $user_object    = eZUser::fetch( $user_id );
        if ( is_object( $user_object ) )
        {
            $user_id = $user_object->attribute( 'contentobject_id' );
            if ( is_object( $lightboxObject ) )
            {
                $lightboxAccessObject = eZLightboxAccess::fetch( $lightbox_id, $user_id );
                if ( !is_object( $lightboxAccessObject ) )
                {
                    $lightboxAccessObject = eZLightboxAccess::create( $lightbox_id, $user_id, $access_mask );
                    if ( is_object( $lightboxAccessObject ) )
                    {
                        $db = eZDB::instance();
                        $db->begin();
                        $lightboxAccessObject->store();
                        $db->commit();
                        $messages[] = ezi18n( 'eZLightboxOperationCollection::createLightboxAccess',
                                              'Successfully granted access for user ID %1 to lightbox ID %2.',
                                              null, array( $user_id, $lightbox_id )
                                            );
                        $status = eZModuleOperationInfo::STATUS_CONTINUE;
                    }
                    else
                    {
                        $messages[] = ezi18n( 'eZLightboxOperationCollection::createLightboxAccess',
                                              'Failed to create access object for user ID %1 and lightbox ID %2.',
                                               null, array( $user_id, $lightbox_id )
                                            );
                    }
                }
                else
                {
                    $messages[] = ezi18n( 'eZLightboxOperationCollection::createLightboxAccess',
                                          'User with ID %1 already has access to lightbox ID %2.',
                                           null, array( $user_id, $lightbox_id )
                                        );
                }
            }
            else
            {
                $messages[] = ezi18n( 'eZLightboxOperationCollection::createLightboxAccess',
                                      'A lightbox with ID %1 does not exist.', null, array( $lightbox_id )
                                    );
            }
        }
        else
        {
            $messages[] = ezi18n( 'eZLightboxOperationCollection::createLightboxAccess',
                                  'Submitted user object is not valid.'
                                );
        }
        return array( 'status'   => $status,
                      'messages' => $messages
                    );
    }

    public static function sendLightboxEMail( $lightbox_id, $user_id, $access_mask, $email_data )
    {
        $messages    =& lightboxOperationCollection::$__messages;
        $status      =  eZModuleOperationInfo::STATUS_CANCELLED;
        $user_object =  eZUser::fetch( $user_id );
        if ( isset( $email_data['subject'] ) && trim( $email_data['subject'] ) != '' )
        {
            $uri      = '/lightbox/view/list/' . $lightbox_id;
            eZURI::transformURI( $uri, false, 'full' );
            $body     = ezi18n( 'eZLightboxOperationCollection::sendLightboxEMail',
                                'Use the following link to view the lightbox'
                              );
            $body .= "\n$uri";
            $subject  = trim( $email_data['subject'] );
            $receiver = $user_object->attribute( 'email' );
            if ( isset( $email_data['body'] ) && trim( $email_data['body'] ) != '' )
            {
                $body = trim( $email_data['body'] ) . "\n\n" . $body;
            }
            $ini = eZINI::instance();
            if ( is_object( $ini ) )
            {
                $currentUser = eZUser::currentUser();
                if ( !is_object( $currentUser ) )
                {
                    $sender = $ini->variable( 'MailSettings', 'EmailSender' );
                    if ( !$sender )
                    {
                        $sender = $ini->variable( 'MailSettings', 'AdminEmail' );
                    }
                }
                else
                {
                    $sender = $currentUser->attribute( 'email' );
                }
                $mailObject = new eZMail();
                if ( is_object( $mailObject ) )
                {
                    $mailObject->setSender( $sender );
                    $mailObject->setReceiver( $receiver );
                    $mailObject->setSubject( $subject );
                    $mailObject->setBody( $body );
                    $mailResult = eZMailTransport::send( $mailObject );
                    if ( $mailResult )
                    {
                        $status = eZModuleOperationInfo::STATUS_CONTINUE;
                    }
                    else
                    {
                        $messages[] = ezi18n( 'eZLightboxOperationCollection::sendLightboxEMail',
                                              'An error occured while sending the email.'
                                            );
                    }
                }
                else
                {
                    $messages[] = ezi18n( 'eZLightboxOperationCollection::sendLightboxEMail',
                                          'Failed to create email object.'
                                        );
                }
            }
            else
            {
                $messages[] = ezi18n( 'eZLightboxOperationCollection::sendLightboxEMail',
                                      'Failed to create object for site.ini.'
                                    );
            }
        }
        else
        {
            $messages[] = ezi18n( 'eZLightboxOperationCollection::sendLightboxEMail',
                                  'No subject in email data found.'
                                );
        }
        return array( 'status'   => $status,
                      'messages' => $messages
                    );
    }

}

?>
