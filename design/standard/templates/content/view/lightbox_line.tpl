<div class="lightboxline">
 <div class="lightboxitem {$bg}">
  <div class="objectname">{$object.name|wash()} ({$object.class_name|wash()})</div>
  <div class="locations">
   <div class="locationhead">{'Locations'|i18n( 'lightbox/view' )}:</div>

{def $pathElements = false()}

{if $object.assigned_nodes|count()|gt( 0 )}

    <ul>

    {foreach $object.assigned_nodes as $node}

        {set $pathElements = array()}

        {foreach $node.url_alias|explode( '/' ) as $index => $pathElement}

            {set $pathElements = $pathElements|append( $pathElement|shorten( 20, '...', 'middle' ) )}

        {/foreach}

    <li class="location">
     <a href={$node.url_alias|ezurl()}>{$pathElements|implode( ' > ' )|wash()}</a>
    </li>

    {/foreach}

   </ul>

{/if}

{undef $pathElements}

  </div>
  <div class="published">{$object.published|l10n( 'date' )}</div>
 </div>

{include            uri = 'design:lightbox/actions_form.tpl'
                item_id = $object.id
              item_type = 'eZContentObject'
         can_use_basket = $can_use_basket
               lightbox = $lightbox
           lightboxList = $lightboxList}

</div>