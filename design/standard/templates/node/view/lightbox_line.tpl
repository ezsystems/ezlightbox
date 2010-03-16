<div class="lightboxline">
 <div class="lightboxitem {$bg}">
  <div class="nodename">

{if $content_node}

   <a href={$content_node.url_alias|ezurl()}>{$content_node.name|wash()}</a> ({$content_node.class_name|wash()})

{else}

    {'Node ID %1 not available'|i18n( 'design/standard/node/view/lightbox_line', , array( $node_id ) )}

{/if}

  </div>
  <div class="published">{cond( $content_node, $content_node.object.published|l10n( 'date' ) )}</div>
 </div>
 <div class="actions">
  <form action={'/content/action'|ezurl()} method="post">
   <input type="hidden" name="ContentObjectID"   value="{$content_node.object.id}" />
   <input type="hidden" name="ItemID"            value="{$node_id}" />
   <input type="hidden" name="ItemType"          value="2" />

{* Add to basket *}

{if and( $content_node, $can_use_basket )}

   <input type="image" src={'action_add_to_cart.png'|ezimage()} title="{'Use this button to add the item to the shopping cart'|i18n( 'design/standard/view/lightbox_line' )}" name="ActionAddToBasket" alt="{'Cart'|i18n( 'design/standard/view/lightbox_line' )}" style="padding-right: 0.5em;" />

{/if}

{if $lightbox.can_edit}

    {* Remove from lightbox *}

   <input type="image" src={'action_lightbox_delete_medium.png'|ezimage()} title="{'Use this button to delete the item from this lightbox'|i18n( 'design/standard/view/lightbox_line' )}" name="RemoveFromLightboxAction" alt="{'Remove'|i18n( 'design/standard/view/lightbox_line' )}" />

    {* Move from one lightbox to another *}

    {if and( $content_node, is_array( $lightboxList ), $lightboxList|count()|gt( 1 ) )}

   <input type="hidden" name="LightboxID" value="{$lightbox.id}" />
   <input type="image" src={'action_lightbox_move_medium.png'|ezimage()} alt="{'Move'|i18n( 'design/standard/view/lightbox_line' )}" title="{'Use this button to move the item from this lightbox to the selected one'|i18n( 'design/standard/view/lightbox_line' )}" name="MoveToLightboxAction" style="padding-right: 0.5em;" />
   <br />
   <select name="MoveToLightboxID" title="{'Target lightbox to move the item in'|i18n( 'design/standard/view/lightbox_line' )}">

        {foreach $lightboxList as $moveLightbox}

            {if $moveLightbox.id|ne( $lightbox.id )}

    <option value="{$moveLightbox.id}">{$moveLightbox.name|shorten( 15 )|wash()}</option>

            {/if}

        {/foreach}

   </select>

    {/if}

{/if}

  </form>
 </div>
 <div class="float-break"></div>
</div>