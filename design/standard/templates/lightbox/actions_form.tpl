 <div class="actions">
  <div class="iconseparator">
   <a href={'/'|ezurl()} onclick="eZLightboxLibrary.setLightboxItem( 'hiddenLightboxListActionFormItemID', '{$item_id}', 'hiddenLightboxListActionFormItemType', '{$item_type}' ); eZLightboxLibrary.sendLightboxActionForm( 'hiddenLightboxListActionForm', 'ActionMoveLightboxItemUp', false ); return false;">
    <span class="lightboxMove lightboxMoveItemUp" title="{'Use this button to move the item one position up'|i18n( 'lightbox/icons' )|wash()}"></span>
   </a>
   <a href={'/'|ezurl()} onclick="eZLightboxLibrary.setLightboxItem( 'hiddenLightboxListActionFormItemID', '{$item_id}', 'hiddenLightboxListActionFormItemType', '{$item_type}' ); eZLightboxLibrary.sendLightboxActionForm( 'hiddenLightboxListActionForm', 'ActionMoveLightboxItemDown', false ); return false;">
    <span class="lightboxMove lightboxMoveItemDown" title="{'Use this button to move the item one position down'|i18n( 'lightbox/icons' )|wash()}"></span>
   </a>
  </div>

{* Add to basket *}

{if $can_use_basket}

  <div class="iconseparator">
   <a href={'/'|ezurl()} onclick="eZLightboxLibrary.setLightboxItem( 'hiddenLightboxListActionFormItemID', '{$item_id}', 'hiddenLightboxListActionFormItemType', '{$item_type}' ); eZLightboxLibrary.sendLightboxActionForm( 'hiddenLightboxListActionForm', 'ActionAddToBasket', false ); return false;">
    <span class="lightboxActionMedium lightboxCartMedium" title="{'Use this button to add the item to the shopping cart'|i18n( 'lightbox/icons' )|wash()}"></span>
   </a>
  </div>

{/if}

{if $lightbox.can_edit}

    {* Remove from lightbox *}

  <div class="iconseparator">
   <a href={'/'|ezurl()} onclick="eZLightboxLibrary.setLightboxItem( 'hiddenLightboxListActionFormItemID', '{$item_id}', 'hiddenLightboxListActionFormItemType', '{$item_type}' ); eZLightboxLibrary.sendLightboxActionForm( 'hiddenLightboxListActionForm', 'RemoveFromLightboxAction', '{'Are you sure that you want to delete this item ?'|i18n( 'lightbox/icons' )}' ); return false;">
    <span class="lightboxActionMedium lightboxDeleteMedium" title="{'Use this button to delete the item from this lightbox'|i18n( 'lightbox/icons' )|wash()}"></span>
   </a>
  </div>

    {* Move from one lightbox to another *}

    {if and( is_array( $lightboxList ), $lightboxList|count()|gt( 1 ) )}

  <div class="iconseparator">
   <form action={'/content/action'|ezurl()} method="post" id="moveLightboxActionForm_{$item_id}_{$item_type}_{$lightbox.id}">
    <input type="hidden" name="ContentObjectID" value="0" />
    <input type="hidden" name="ItemID"          value="{$item_id}" />
    <input type="hidden" name="ItemType"        value="{$item_type}" />
    <input type="hidden" name="LightboxID"      value="{$lightbox.id}" />
    <input type="hidden" name=""                value="1" id="moveLightboxActionForm_{$item_id}_{$item_type}_{$lightbox.id}SubmitField" />
    <select name="MoveToLightboxID" title="{'Target lightbox to move the item in'|i18n( 'lightbox/view' )}">

        {foreach $lightboxList as $moveLightbox}

            {if $moveLightbox.id|ne( $lightbox.id )}

     <option value="{$moveLightbox.id}">{$moveLightbox.name|shorten( 15 )|wash()}</option>

            {/if}

        {/foreach}

    </select>
   </form>
   <a href={'/'|ezurl()} onclick="eZLightboxLibrary.sendLightboxActionForm( 'moveLightboxActionForm_{$item_id}_{$item_type}_{$lightbox.id}', 'MoveToLightboxAction', false ); return false;">
    <span class="lightboxActionMedium lightboxMoveMedium" title="{'Use this button to move the item from this lightbox to the selected one'|i18n( 'lightbox/icons' )|wash()}"></span>
   </a>
  </div>

    {/if}

{/if}

 </div>
 <div class="float-break"></div>
