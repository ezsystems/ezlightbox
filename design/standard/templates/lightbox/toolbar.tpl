{def $user_hash                   = ''
     $lightboxSessionKey          = false()
     $lightbox_count              = 0
     $current_lightbox_id         = false()
     $current_lightbox            = false()
     $current_lightbox_item_count = 0
     $current_node_id             = first_set( $module_result.node_id, 0 )
     $can_create_lightbox         = false()
     $can_add_item                = false()}

{if is_array( $current_user.role_id_list )}

    {if $current_user.role_id_list|count()|gt( 0 )}

        {set $user_hash = $current_user.role_id_list|implode( ',' )}

    {/if}

{/if}

{if is_array( $current_user.limited_assignment_value_list )}

    {if $current_user.limited_assignment_value_list|count()|gt( 0 )}

        {if $user_hash|eq( '' )}

            {set $user_hash = $current_user.limited_assignment_value_list|implode( ',' )}

        {else}

            {set $user_hash = concat( $user_hash, ',', $current_user.limited_assignment_value_list|implode( ',' ) )}

        {/if}

    {/if}

{/if}

{if ezhttp_hasvariable( 'lightboxSessionKey', 'session' )|not()}

    {set $lightboxSessionKey  = fetch( 'lightbox', 'sessionKey' )}

{else}

    {set $lightboxSessionKey = ezhttp( 'lightboxSessionKey', 'session' )}

{/if}

{if $current_node_id|gt( 0 )}

    {* itemTypeID of 2 means eZ Content Node while itemTypeID 1 means eZ Content Object *}
    {* It is also possible to use a lightbox object as a parameter for 'lightbox' *}
    {set $can_add_item = fetch( 'lightbox', 'canAddItemToLightbox', hash( 'lightbox',   $current_lightbox_id,
                                                                          'item',       $current_node_id,
                                                                          'itemTypeID', 2
                                                                         )
                              )}

{/if}

{cache-block                  keys = array( $uri_string, $lightboxSessionKey, $user_hash, $can_add_item )
                            expiry = 86400
             ignore_content_expiry}

{set $lightbox_count      = fetch( 'lightbox', 'count' )
     $current_lightbox_id = ezpreference( 'currentLightboxID' )
     $can_create_lightbox = fetch( 'user', 'has_access_to',
                                   hash( 'module', 'lightbox', 'function', 'create' )
                                 )}

{def $lightboxList     = fetch( 'lightbox', 'list', hash( 'sortBy', hash( 'name', 'ASC' ), 'asObject', true(), 'otherMasks', array( '3' ) ) )
     $hasLightboxes    = $lightbox_count|gt( 0 )
     $is_own_lightbox  = false()
     $can_use_lightbox = and( fetch( 'user', 'has_access_to',
                                     hash( 'module',   'lightbox', 'function', 'add' )
                                   ),
                              $current_lightbox_id|gt( 0 )
                            )}

{if $current_lightbox_id|gt( 0 )}

    {set            $current_lightbox = fetch( 'lightbox', 'object', hash( 'id', $current_lightbox_id, 'asObject', true() ) )
         $current_lightbox_item_count = $current_lightbox.item_count}

{/if}

<div class="border-box">
 <div class="border-tl">
  <div class="border-tr">
   <div class="border-tc"></div>
  </div>
 </div>
 <div class="border-ml">
  <div class="border-mr">
   <div class="border-mc" style="padding: 0;">
    <form action={'/content/action'|ezurl()} method="post" id="hiddenLightboxToolbarActionForm">
     <input type="hidden" name="ContentObjectID" value="0" />
     <input type="hidden" name=""                value="1" id="hiddenLightboxToolbarActionFormSubmitField" />
     <input type="hidden" name="ItemID"          value=""  id="hiddenLightboxToolbarActionFormItemID" />
     <input type="hidden" name="ItemType"        value=""  id="hiddenLightboxToolbarActionFormItemType" />
    </form>
    <table style="background-color: white;">
     <tr>
      <td>

{'Current Lightbox'|i18n( 'lightbox/toolbar' )|wash()}:

      </td>
      <td style="padding-left: 0.3em; padding-right: 0.3em;">

{if $hasLightboxes}

       <form action={'/content/action'|ezurl()} method="post" id="hiddenlightboxselectionform">
        <input type="hidden" name="ContentObjectID" value="0" />
        <input type="hidden" name="newLightboxID"   value="0" id="newLightboxIDInput" />
        <input type="hidden" name="ChangeUserCurrentLightbox" value="1" />
        <select name="selectedLightboxID" onchange="eZLightboxLibrary.changeCurrentLightbox();" id="lightboxselection">

    {if $current_lightbox_id|le( 0 )}

         <option selected="selected" value="-1">{'Choose current lightbox'|i18n( 'lightbox/toolbar' )|wash()}</option>

    {/if}

    {foreach $lightboxList as $lightbox}

        {set $is_own_lightbox = $lightbox.owner_id|eq( $current_user.contentobject_id )}

         <option value="{$lightbox.id}"{cond( $lightbox.id|eq( $current_lightbox_id ), ' selected="selected"' )}{cond( $is_own_lightbox, ' style="font-weight: bold;"' )}>{$lightbox.name|shorten( 20 )|wash()} ({$lightbox.item_count})</option>

    {/foreach}

        </select>
       </form>

{else}

    {'None'|i18n( 'lightbox/toolbar' )|wash()}

{/if}

      </td>

{if $hasLightboxes}

    {if $current_lightbox.can_edit}

      <td>
       <a href={'/'|ezurl()} onclick="eZLightboxLibrary.sendLightboxActionForm( 'hiddenLightboxToolbarActionForm', 'EditLightboxAction', false ); return false;">
        <span class="lightboxActionMedium lightboxEditMedium" title="{'Use this button to edit the current lightbox.'|i18n( 'lightbox/icons' )|wash()}"></span>
       </a>
      </td>
      <td>
       <a href={'/'|ezurl()} onclick="eZLightboxLibrary.sendLightboxActionForm( 'hiddenLightboxToolbarActionForm', 'DeleteLightboxAction', '{'Are you sure that you want to delete the lightbox ?'|i18n( 'lightbox/icons' )}' ); return false;">
        <span class="lightboxActionMedium lightboxDeleteMedium" title="{'Use this button to delete the current lightbox.'|i18n( 'lightbox/icons' )|wash()}"></span>
       </a>
      </td>

    {else}

      <td>
       <span class="lightboxActionMedium lightboxEditMedium" title="{'You are not allowed to edit the current lightbox.'|i18n( 'lightbox/icons' )|wash()}"></span>
      </td>
      <td>
       <span class="lightboxActionMedium lightboxDeleteMedium" title="{'You are not allowed to delete the current lightbox.'|i18n( 'lightbox/icons' )|wash()}"></span>
      </td>

    {/if}

    {if $current_lightbox.can_send}

      <td>
       <a href={'/'|ezurl()} onclick="eZLightboxLibrary.sendLightboxActionForm( 'hiddenLightboxToolbarActionForm', 'SendLightboxAction', false ); return false;">
        <span class="lightboxActionMedium lightboxSendMedium" title="{'Use this button to send the current lightbox by email.'|i18n( 'lightbox/icons' )|wash()}"></span>
       </a>
      </td>

    {else}

      <td>
       <span class="lightboxActionMedium lightboxSendMedium" title="{'You are not allowed to send the current lightbox by email.'|i18n( 'lightbox/icons' )|wash()}"></span>
      </td>

    {/if}

    {if $current_lightbox.can_view}

      <td>
       <a href={'/'|ezurl()} onclick="eZLightboxLibrary.sendLightboxActionForm( 'hiddenLightboxToolbarActionForm', 'ViewLightboxAction', false ); return false;">
        <span class="lightboxActionMedium lightboxViewMedium" title="{'Use this button to show the contents of the current lightbox.'|i18n( 'lightbox/icons' )|wash()}"></span>
       </a>
      </td>

    {else}

      <td>
       <span class="lightboxActionMedium lightboxViewMedium" title="{'You are not allowed to view the contents of the current lightbox.'|i18n( 'lightbox/icons' )|wash()}"></span>
      </td>

    {/if}

{else}

      <td>
       <span class="lightboxActionMedium lightboxEditMedium" title="{'Currently no lightbox can be edited'|i18n( 'lightbox/icons' )|wash()}"></span>
      </td>
      <td>
       <span class="lightboxActionMedium lightboxDeleteMedium" title="{'Currently no lightbox can be deleted.'|i18n( 'lightbox/icons' )|wash()}"></span>
      </td>
      <td>
       <span class="lightboxActionMedium lightboxSendMedium" title="{'Currently no lightbox can be send by email.'|i18n( 'lightbox/icons' )|wash()}"></span>
      </td>
      <td>
       <span class="lightboxActionMedium lightboxViewMedium" title="{'Currently no lightbox can be shown.'|i18n( 'lightbox/icons' )|wash()}"></span>
      </td>

{/if}

      <td>

{if $can_create_lightbox}

       <a href={'/'|ezurl()} onclick="eZLightboxLibrary.sendLightboxActionForm( 'hiddenLightboxToolbarActionForm', 'CreateLightboxAction', false ); return false;">
        <span class="lightboxActionMedium lightboxCreateMedium" title="{'Use this button to create a new lightbox.'|i18n( 'lightbox/icons' )|wash()}"></span>
       </a>

{else}

       <span class="lightboxActionMedium lightboxCreateMedium" title="{'You are not allowed to create a new lightbox.'|i18n( 'lightbox/icons' )|wash()}"></span>

{/if}

      </td>
      <td>

{if $current_node_id|gt( 0 )}

    {* itemTypeID of 2 means eZ Content Node while itemTypeID 1 means eZ Content Object *}
    {if fetch( 'lightbox', 'canAddItemToLightbox', hash( 'lightbox',   $current_lightbox,
                                                         'item',       $current_node_id,
                                                         'itemTypeID', 2
                                                       )
             )}

       {* Use the content object ID and 'eZContentObject' instead of 'eZContentNode' to add a content object to the lightbox instead of a content node *}
       <a href={'/'|ezurl()} onclick="eZLightboxLibrary.setLightboxItem( 'hiddenLightboxToolbarActionFormItemID', {$current_node_id}, 'hiddenLightboxToolbarActionFormItemType', 'eZContentNode' ); eZLightboxLibrary.sendLightboxActionForm( 'hiddenLightboxToolbarActionForm', 'AddToLightboxAction', false ); return false;">
        <span class="lightboxActionMedium lightboxAddMedium" title="{'Add this page to the current lightbox.'|i18n( 'lightbox/icons' )|wash()}"></span>
       </a>

    {else}

       <span class="lightboxActionMedium lightboxAddMedium" title="{'You are not allowed to add this page to the current lightbox.'|i18n( 'lightbox/icons' )|wash()}"></span>

    {/if}

{/if}

      </td>
     </tr>
    </table>

   </div>
  </div>
 </div>
 <div class="border-bl">
  <div class="border-br">
   <div class="border-bc"></div>
  </div>
 </div>
</div>

{/cache-block}
