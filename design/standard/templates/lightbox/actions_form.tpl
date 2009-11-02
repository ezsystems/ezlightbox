 <div class="actions">
  <form action={'/content/action'|ezurl()} method="post">
   <input type="hidden" name="ContentObjectID" value="0" />
   <input type="hidden" name="ItemID"          value="{$item_id}" />
   <input type="hidden" name="ItemType"        value="{$item_type}" />
   <div class="iconseparator">
    <input type="image" src={'action_move_up.png'|ezimage()} title="{'Use this button to move the item one position up'|i18n( 'lightbox/view' )}" name="ActionMoveLightboxItemUp" alt="{'Up'|i18n( 'lightbox/view' )}" style="padding-right: 0.5em;" />
    <input type="image" src={'action_move_down.png'|ezimage()} title="{'Use this button to move the item one position down'|i18n( 'lightbox/view' )}" name="ActionMoveLightboxItemDown" alt="{'Down'|i18n( 'lightbox/view' )}" style="padding-right: 0.5em;" />
   </div>

{* Add to basket *}

{if $can_use_basket}

   <div class="iconseparator">
    <input type="image" src={'action_add_to_cart.png'|ezimage()} title="{'Use this button to add the item to the shopping cart'|i18n( 'lightbox/view' )}" name="ActionAddToBasket" alt="{'Cart'|i18n( 'lightbox/view' )}" style="padding-right: 0.5em;" />
   </div>

{/if}

{if $lightbox.can_edit}

    {* Remove from lightbox *}

   <div class="iconseparator">
    <input type="image" src={'action_lightbox_delete_medium.png'|ezimage()} title="{'Use this button to delete the item from this lightbox'|i18n( 'lightbox/view' )}" name="RemoveFromLightboxAction" alt="{'Remove'|i18n( 'lightbox/view' )}" />
   </div>

    {* Move from one lightbox to another *}

    {if and( is_array( $lightboxList ), $lightboxList|count()|gt( 1 ) )}

   <div class="iconseparator">
    <input type="hidden" name="LightboxID" value="{$lightbox.id}" />
    <input type="image" src={'action_lightbox_move_medium.png'|ezimage()} alt="{'Move'|i18n( 'lightbox/view' )}" title="{'Use this button to move the item from this lightbox to the selected one'|i18n( 'lightbox/view' )}" name="MoveToLightboxAction" style="padding-right: 0.5em;" />
    <select name="MoveToLightboxID" title="{'Target lightbox to move the item in'|i18n( 'lightbox/view' )}">

        {foreach $lightboxList as $moveLightbox}

            {if $moveLightbox.id|ne( $lightbox.id )}

     <option value="{$moveLightbox.id}">{$moveLightbox.name|shorten( 15 )|wash()}</option>

            {/if}

        {/foreach}

    </select>
   </div>

    {/if}

{/if}

  </form>
 </div>
 <div class="float-break"></div>
