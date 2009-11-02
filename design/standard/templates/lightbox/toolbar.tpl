{def $user_hash                   = concat( $current_user.role_id_list|implode( ',' ), ',', $current_user.limited_assignment_value_list|implode( ',' ) )
     $lightboxSessionKey          = false()
     $lightbox_count              = 0
     $current_lightbox_id         = false()
     $current_lightbox            = false()
     $current_lightbox_item_count = 0
     $current_node_id             = first_set( $module_result.node_id, 0 )
     $can_create_lightbox         = false()}

{if ezhttp_hasvariable( 'lightboxSessionKey', 'session' )|not()}

    {set $lightboxSessionKey  = fetch( 'lightbox', 'sessionKey' )}

{else}

    {set $lightboxSessionKey = ezhttp( 'lightboxSessionKey', 'session' )}

{/if}

{cache-block                  keys = array( $uri_string, $lightboxSessionKey, $user_hash )
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
     $can_use_lightbox = fetch( 'user', 'has_access_to',
                                 hash( 'module',   'lightbox', 'function', 'add' )
                              )}

{if $current_lightbox_id|gt( 0 )}

    {set            $current_lightbox = fetch( 'lightbox', 'object', hash( 'id', $current_lightbox_id, 'asObject', true() ) )
         $current_lightbox_item_count = $current_lightbox.item_count}

{/if}

<div class="border-box" style="width: 500px; margin-bottom: 0px;">
 <div class="border-tl">
  <div class="border-tr">
   <div class="border-tc"></div>
  </div>
 </div>
 <div class="border-ml">
  <div class="border-mr">
   <div class="border-mc" style="height: 33px;">
    <form action={'/content/action'|ezurl()} method="post" id="hiddenlightboxselectionform">
     <input type="hidden" name="ContentObjectID" value="0" />
     <input type="hidden" name="newLightboxID"   value="0" id="newLightboxIDInput" />
     <input type="hidden" name="ChangeUserCurrentLightbox" value="1" />
    </form>
    <form action={'/content/action'|ezurl()} method="post">
     <input type="hidden" name="ContentObjectID" value="0" />
     <table>
      <tr>
       <td>

{'Current Lightbox'|i18n( 'design/ezdam/lightbox-head' )}:

       </td>
       <td style="padding-left: 0.3em; padding-right: 0.3em;">

{if $hasLightboxes}

        <select name="selectedLightboxID" onchange="changeCurrentLightbox();" id="lightboxselection">

    {if $current_lightbox_id|le( 0 )}

         <option selected="selected" value="-1">{'Choose current lightbox'|i18n( 'design/ezdam/lightbox-head' )}</option>

    {/if}

    {foreach $lightboxList as $lightbox}

        {set $is_own_lightbox = $lightbox.owner_id|eq( $current_user.contentobject_id )}

         <option value="{$lightbox.id}"{cond( $lightbox.id|eq( $current_lightbox_id ), ' selected="selected"' )}{cond( $is_own_lightbox, ' style="font-weight: bold;"' )}>{$lightbox.name|shorten( 20 )|wash()} ({$lightbox.item_count})</option>

    {/foreach}

        </select>

{else}

    {'None'|i18n( 'design/ezdam/lightbox-head' )}

{/if}

       </td>

{if $hasLightboxes}

    {if $current_lightbox.can_edit}

       <td>
        <input type="image" src={'action_lightbox_edit_medium.png'|ezimage()} alt="{'Edit'|i18n( 'design/ezdam/lightbox-head' )}" title="{'Use this button to edit the current lightbox.'|i18n( 'design/ezdam/lightbox-head' )}" class="lightbox-action" name="EditLightboxAction" />
       </td>
       <td>
        <input type="image" src={'action_lightbox_delete_medium.png'|ezimage()} alt="{'Delete'|i18n( 'design/ezdam/lightbox-head' )}" title="{'Use this button to delete the current lightbox.'|i18n( 'design/ezdam/lightbox-head' )}" class="lightbox-action" name="DeleteLightboxAction" onclick="return confirm( '{'Are you sure that you want to delete the lightbox ?'|i18n( 'design/ezdam/lightbox-head' )}' );" />
       </td>

    {else}

       <td>
        <input type="image" src={'action_lightbox_edit_medium.png'|ezimage()} alt="{'Edit'|i18n( 'design/ezdam/lightbox-head' )}" title="{'You are not allowed to edit the current lightbox.'|i18n( 'design/ezdam/lightbox-head' )}" class="lightbox-action" name="EditLightboxAction" disabled="disabled" />
       </td>
       <td>
        <input type="image" src={'action_lightbox_delete_medium.png'|ezimage()} alt="{'Delete'|i18n( 'design/ezdam/lightbox-head' )}" title="{'You are not allowed to delete the current lightbox.'|i18n( 'design/ezdam/lightbox-head' )}" class="lightbox-action" name="DeleteLightboxAction" disabled="disabled" />
       </td>

    {/if}

    {if $current_lightbox.can_send}

       <td>
        <input type="image" src={'action_lightbox_send_medium.png'|ezimage()} alt="{'Send'|i18n( 'design/ezdam/lightbox-head' )}" title="{'Use this button to send the current lightbox by email.'|i18n( 'design/ezdam/lightbox-head' )}" class="lightbox-action" name="SendLightboxAction" />
       </td>

    {else}

       <td>
        <input type="image" src={'action_lightbox_send_medium.png'|ezimage()} alt="{'Send'|i18n( 'design/ezdam/lightbox-head' )}" title="{'You are not allowed to send the current lightbox by email.'|i18n( 'design/ezdam/lightbox-head' )}" class="lightbox-action" name="SendLightboxAction" disabled="disabled"/>
       </td>

    {/if}

    {if $current_lightbox.can_view}

       <td>
        <input type="image" src={'action_lightbox_view_medium.png'|ezimage()} alt="{'Show'|i18n( 'design/ezdam/lightbox-head' )}" title="{'Use this button to show the contents of the current lightbox.'|i18n( 'design/ezdam/lightbox-head' )}" class="lightbox-action" name="ViewLightboxAction" />
       </td>

    {else}

       <td>
        <input type="image" src={'action_lightbox_view_medium.png'|ezimage()} alt="{'Show'|i18n( 'design/ezdam/lightbox-head' )}" title="{'You are not allowed to view the contents of the current lightbox.'|i18n( 'design/ezdam/lightbox-head' )}" class="lightbox-action" name="ViewLightboxAction" disabled="disabled" />
       </td>

    {/if}

{else}

       <td>
        <img src={'action_lightbox_edit_medium.png'|ezimage()} alt="{'Edit'|i18n( 'design/ezdam/lightbox-head' )}" title="{'Currently no lightbox can be edited'|i18n( 'design/ezdam/lightbox-head' )}" />
       </td>
       <td>
        <img src={'action_lightbox_delete_medium.png'|ezimage()} alt="{'Delete'|i18n( 'design/ezdam/lightbox-head' )}" title="{'Currently no lightbox can be deleted.'|i18n( 'design/ezdam/lightbox-head' )}" />
       </td>
       <td>
        <img src={'action_lightbox_send_medium.png'|ezimage()} alt="{'Send'|i18n( 'design/ezdam/lightbox-head' )}" title="{'Currently no lightbox can be send by email.'|i18n( 'design/ezdam/lightbox-head' )}" />
       </td>
       <td>
        <img src={'action_lightbox_view_medium.png'|ezimage()} alt="{'Show'|i18n( 'design/ezdam/lightbox-head' )}" title="{'Currently no lightbox can be shown.'|i18n( 'design/ezdam/lightbox-head' )}" />
       </td>

{/if}

       <td>

{if $can_create_lightbox}

        <input type="image" src={'action_lightbox_new_medium.png'|ezimage()} alt="{'Create'|i18n( 'design/ezdam/lightbox-head' )}" title="{'Use this button to create a new lightbox.'|i18n( 'design/ezdam/lightbox-head' )}" class="lightbox-action" name="CreateLightboxAction" />

{else}

        <img src={'action_lightbox_new_medium.png'|ezimage()} alt="{'Create'|i18n( 'design/ezdam/lightbox-head' )}" title="{'You are not allowed to create a new lightbox.'|i18n( 'design/ezdam/lightbox-head' )}" class="lightbox-action" name="CreateLightboxAction" />

{/if}

       </td>
       <td>

{if $current_node_id|gt( 0 )}

    {if $can_use_lightbox}

        <input type="hidden" name="ItemID" value="{$current_node_id}" /> {* Use the object ID in case of ItemType is eZContentObject *}
        <input type="hidden" name="ItemType" value="eZContentNode" /> {* eZContentObject is also possible *}
        <input type="image" src={'action_add_to_lightbox.png'|ezimage()} alt="{'Add'|i18n( 'design/ezdam/node/full/xmpimage' )}" title="{'Add this page to the current lightbox.'|i18n( 'design/ezdam/node/full/xmpimage' )}" name="AddToLightboxAction" />

    {else}

        <img src={'action_add_to_lightbox.png'|ezimage()} alt="{'Add'|i18n( 'design/ezdam/node/full/xmpimage' )}" title="{'You are not allowed to add this page to the current lightbox.'|i18n( 'design/ezdam/node/full/xmpimage' )}" name="AddToLightboxAction" />

    {/if}

{/if}

       </td>
      </tr>
     </table>
    </form>

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
