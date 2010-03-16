{if and( is_set( $messages ), $messages|count()|gt( 0 ) )}

<div class="message-{cond( $actionSuccess, 'feedback', true(), 'error' )}">

    {foreach $messages as $message}

 <p>{$message|wash()}</p>

    {/foreach}

</div>

{/if}

{if is_object( $selectedLightbox )}

    {def $isForeignLightbox = $selectedLightbox.owner_id|ne( $currentUserID )}

<h1>{'Lightbox "%1"'|i18n( 'lightbox/view', , hash( '%1', $selectedLightbox.name ) )}</h1>
<div class="lightboxlist">

    {if $isForeignLightbox}

 <div class="lightboxowner">
  <strong>{'Owner'|i18n( 'lightbox/view' )}:</strong> {$selectedLightbox.owner.contentobject.name|wash()}
 </div>

    {/if}

 <div class="itemlist">

    {if $selectedLightbox.itemlist|count()|gt( 0 )}

  <ul id="itemlist_{$selectedLightbox.id}">

        {def $content_object = false()
                $item_object = false()
                  $object_id = 0
                   $use_shop = ezini( 'CommonSettings', 'UseShop', 'lightbox.ini' )
             $can_use_basket = false()}

        {if $use_shop|eq( 'enabled' )}

            {set $can_use_basket = fetch( 'user', 'has_access_to',
                                          hash( 'module',   'shop',
                                                'function', 'buy'
                                              )
                                        )}

        {/if}

        {foreach $selectedLightbox.itemlist as $itemObject sequence array( 'bglight', 'bgdark' ) as $bg}

   <li id="item_{$itemObject.item_id}">

            {if $itemObject.type_id|eq( 1 )} {* Content object *}

                {content_view_gui content_object = fetch( 'content', 'object', hash( 'object_id', $itemObject.item_id ) )
                                       object_id = $itemObject.item_id
                                            view = lightbox_line
                                              bg = $bg
                                  can_use_basket = $can_use_basket
                                    lightboxList = $userLightboxList
                                        lightbox = $selectedLightbox}

            {elseif $itemObject.type_id|eq( 2 )} {* Content node *}

                {node_view_gui   content_node = fetch( 'content', 'node', hash( 'node_id', $itemObject.item_id ) )
                                      node_id = $itemObject.item_id
                                         view = lightbox_line
                                           bg = $bg
                               can_use_basket = $can_use_basket
                                 lightboxList = $userLightboxList
                                     lightbox = $selectedLightbox}

            {else}

                {'Unknown item type for item ID %1'|i18n( 'lightbox/view', , array( $itemObject.item_id ) )}

            {/if}

   </li>

        {/foreach}

  </ul>

    {else}

  <p>{'This lightbox is empty.'|i18n( 'lightbox/view' )}</p>

    {/if}

 </div>
 <div class="grantedusers">
  <h3>{'Granted Users'|i18n( 'lightbox/view' )}</h3>
  <ul>

    {def $accessKeys = array()}

    {if $selectedLightbox.access_list|count()|gt( 0 )}

        {foreach $selectedLightbox.access_list as $access}

   <li>

            {$access.user.contentobject.name|wash()}

            {if $access.flags|count()|gt( 0 )}

                {foreach $access.flags as $key => $value}

                    {set $accessKeys = $accessKeys|append( $access.access_keys[$key] )}

                {/foreach}

                ({$accessKeys|implode( ', ' )})

                {set $accessKeys = array()}

            {else}

            ( - )

            {/if}

   </li>

        {/foreach}

    {else}

   <li class="nobox">{'Currently no users are allowed to access this lightbox.'|i18n( 'lightbox/view' )}</li>

    {/if}

  </ul>
 </div>
</div>
<div class="lightbox-buttons">
 <form action={'/content/action'|ezurl()} method="post">
  <input type="hidden" name="ContentObjectID" value="" />
  <input type="hidden" name="LightboxID"      value="{$selectedLightbox.id}" />

    {if $selectedLightbox.can_edit}

  <input type="submit" name="EditLightboxAction"   value="{'Edit'|i18n( 'lightbox/view' )}" class="button" title="{'Use this button to edit this lightbox.'|i18n( 'lightbox/view' )}" />
  <input type="submit" name="DeleteLightboxAction" value="{'Delete'|i18n( 'lightbox/view' )}" class="button" title="{'Use this button to delete this lightbox'|i18n( 'lightbox/view' )}" onclick="return confirm( '{'Are you sure that you want to delete the lightbox ?'|i18n( 'lightbox/view' )}' );" />
  <input type="submit" name="EmptyLightboxAction" value="{'Empty'|i18n( 'lightbox/view' )}" class="button" title="{'Use this button to remove all items from this lightbox'|i18n( 'lightbox/view' )}" />

    {/if}

    {if $selectedLightbox.can_send}

  <input type="submit" name="SendLightboxAction"   value="{'Send'|i18n( 'lightbox/view' )}" class="button" title="{'Use this button to permit another user to access this lightbox by sending him an email'|i18n( 'lightbox/view' )}" />

    {/if}

    {if and( is_set( $redirectURI ), $redirectURI|ne( '' ) )}

  <input type="hidden" name="redirectURI" value="{$redirectURI}" />
  <input type="submit" name="GoBackButton" value="{'Go back'|i18n( 'lightbox/view' )}" title="{'Use this button to go back to the last page.'|i18n( 'lightbox/view' )}" class="button" />

    {/if}

 </form>
</div>

{elseif $userLightboxList|count()|gt( 0 )}

<div class="lightboxlist">
 <h1>{'Choose a lightbox to view'|i18n( 'lightbox/view' )}</h1>
 <ul class="shortlist">

    {foreach $userLightboxList as $lightbox}

  <li><a href={concat( '/lightbox/view/list/', $lightbox.id )|ezurl()}>{$lightbox.name|wash()}</a></li>

    {/foreach}

 </ul>
</div>

{else}

<div class="lightboxlist">
 <h1>{'Create new lightbox'|i18n( 'lightbox/create' )}</h1>
</div>
<div class="lightbox-buttons">
 <p>
  {'You do not have any lightbox yet.'|i18n( 'lightbox/menu' )}
 </p>
 <a href={'/lightbox/create/'|ezurl()}>{'Create new lightbox'|i18n( 'lightbox/create' )}</a>
</div>

{/if}
