<div class="lightboxline">
 <div class="lightboxitem {$bg}">
  <div class="nodename">
   <a href={$node.url_alias|ezurl()}>{$node.name|wash()}</a> ({$node.class_name|wash()})
  </div>
  <div class="published">{$node.object.published|l10n( 'date' )}</div>
 </div>

{include            uri = 'design:lightbox/actions_form.tpl'
                item_id = $node.node_id
              item_type = 'eZContentNode'
         can_use_basket = $can_use_basket
               lightbox = $lightbox
           lightboxList = $lightboxList}

</div>