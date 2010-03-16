{if and( is_set( $messages ), $messages|count()|gt( 0 ) )}

<div class="message-{cond( $actionSuccess, 'feedback', true(), 'error' )}">

    {foreach $messages as $message}

 <p>{$message|wash()}</p>

    {/foreach}

</div>

{/if}

{if is_object( $selectedLightbox )}

    {def $isForeignLightbox = $selectedLightbox.owner_id|ne( $currentUserID )}

<h1>{'Lightbox "%1"'|i18n( 'design/standard/lightbox/views', , hash( '%1', $selectedLightbox.name ) )}</h1>

    {if $isForeignLightbox}

<div class="lightboxlist">
 <div class="lightboxowner">
  <strong>{'Owner'|i18n( 'design/standard/lightbox/views' )}:</strong> {$selectedLightbox.owner.contentobject.name|wash()}
 </div>
 <div class="itemlist">

    {/if}

    {if $selectedLightbox.item_id_list|count()|gt( 0 )}

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

        {foreach $selectedLightbox.item_id_list as $item_id => $type_id sequence array( 'bglight', 'bgdark' ) as $bg}

            {if $type_id|eq( 1 )} {* Content object *}

                {content_view_gui content_object = fetch( 'content', 'object', hash( 'object_id', $item_id ) )
                                       object_id = $item_id
                                            view = lightbox_line
                                              bg = $bg
                                  can_use_basket = $can_use_basket
                                    lightboxList = $userLightboxList
                                        lightbox = $selectedLightbox}

            {elseif $type_id|eq( 2 )} {* Content node *}

                {* def $content_node = fetch( 'content', 'node', hash( 'node_id', $item_id ) )
                     $content_object = $item_object.object
                          $object_id = $item_object.object.id *}

                {node_view_gui   content_node = fetch( 'content', 'node', hash( 'node_id', $item_id ) )
                                      node_id = $item_id
                                         view = lightbox_line
                                           bg = $bg
                               can_use_basket = $can_use_basket
                                 lightboxList = $userLightboxList
                                     lightbox = $selectedLightbox}

            {/if}

        {/foreach}

    {else}

<p>{'This lightbox is empty.'|i18n( 'design/standard/lightbox/views' )}</p>

    {/if}

 </div>
 <div class="grantedusers">
  <h3>{'Granted Users'|i18n( 'design/standard/lightbox/menu' )}</h3>
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

   <li class="nobox">{'Currently no users are allowed to access this lightbox.'|i18n( 'design/standard/lightbox/menu' )}</li>

{/if}

  </ul>
 </div>
</div>
<div class="lightbox-buttons">
 <form action={'/content/action'|ezurl()} method="post">
  <input type="hidden" name="ContentObjectID" value="" />
  <input type="hidden" name="LightboxID"      value="{$selectedLightbox.id}" />

    {if $selectedLightbox.can_edit}

  <input type="submit" name="EditLightboxAction"   value="{'Edit'|i18n( 'design/standard/lightbox/views' )}" class="button" title="{'Use this button to edit this lightbox.'|i18n( 'design/standard/lightbox/views' )}" />
  <input type="submit" name="DeleteLightboxAction" value="{'Delete'|i18n( 'design/standard/lightbox/views' )}" class="button" title="{'Use this button to delete this lightbox'|i18n( 'design/standard/lightbox/views' )}" />
  <input type="submit" name="EmptyLightboxAction" value="{'Empty'|i18n( 'design/standard/lightbox/views' )}" class="button" title="{'Use this button to remove all items from this lightbox'|i18n( 'design/standard/lightbox/views' )}" />

    {/if}

    {if $selectedLightbox.can_send}

  <input type="submit" name="SendLightboxAction"   value="{'Send'|i18n( 'design/standard/lightbox/views' )}" class="button" title="{'Use this button to permit another user to access this lightbox by sending him an email'|i18n( 'design/standard/lightbox/views' )}" />

    {/if}

    {if and( is_set( $redirectURI ), $redirectURI|ne( '' ) )}

  <input type="hidden" name="redirectURI" value="{$redirectURI}" />
  <input type="submit" name="GoBackButton" value="{'Go back'|i18n( 'design/ezdam/lightbox/create' )}" title="{'Use this button to go back to the last page.'|i18n( 'design/ezdam/lightbox/create' )}" class="button" />

    {/if}

 </form>
</div>

{else}

<div class="lightboxlist">
 <h1>{'Choose a lightbox to view'|i18n( 'design/standard/lightbox/views' )}</h1>
 <ul class="shortlist">

    {foreach $userLightboxList as $lightbox}

  <li><a href={concat( '/lightbox/view/list/', $lightbox.id )|ezurl()}>{$lightbox.name|wash()}</a></li>

    {/foreach}

 </ul>
</div>

{/if}
