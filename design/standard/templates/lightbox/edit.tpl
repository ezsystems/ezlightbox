{if and( is_set( $messages ), $messages|count()|gt( 0 ) )}

<div class="message-{cond( $actionSuccess, 'feedback', true(), 'error' )}">

    {foreach $messages as $message}

 <p>{$message|wash()}</p>

    {/foreach}

</div>

{/if}

{if is_object( $lightbox )}

<h1>{'Edit lightbox "%1"'|i18n( 'lightbox/edit', , hash( '%1', $lightbox.name ) )}</h1>
<form action={$url|ezurl()} method="post">
 <p>
   {'Name'|i18n( 'lightbox/edit' )}: <input type="text" name="lightbox_name" value="{$lightbox.name|wash()}" size="50" />
 </p>

 <fieldset>
  <legend>{'Items'|i18n( 'lightbox/edit' )}</legend>

{if $lightbox.item_id_list|count()|gt( 0 )}

    {def $item_object = false()}

   <ul>

    {foreach $lightbox.item_id_list as $item_id => $type_id sequence array( 'bglight', 'bgdark' ) as $bg}

        {if $type_id|eq( 1 )}

            {set $item_object = fetch( 'content', 'object', hash( 'object_id', $item_id ) )}

        {elseif $type_id|eq( 2 )}

            {set $item_object = fetch( 'content', 'node', hash( 'node_id', $item_id ) )}

        {/if}

        {if is_object( $item_object )}

    <li>{$item_object.name|wash()}</li>

        {/if}

    {/foreach}

   </ul>

{else}

    {'No items in this lightbox'|i18n( 'lightbox/edit' )}

{/if}

 </fieldset>
 <fieldset>
  <legend>{'Lightbox users'|i18n( 'lightbox/edit' )}</legend>

    {def $hasUsers = $lightbox.access_list|count()|gt( 0 )}

    {if $hasUsers}

        {def $canGrant = $lightbox.can_grant}

  <table class="list">
   <tr>
    <th class="tight">&nbsp;</th>
    <th class="tight">{'Name'|i18n( 'lightbox/edit' )}</th>
    <th class="tight">{'Granted'|i18n( 'lightbox/edit' )}</th>
    <th>{'Rights'|i18n( 'lightbox/edit' )}</th>
   </tr>

        {foreach $lightbox.access_list as $access sequence array( 'bglight', 'bgdark' ) as $bg}

   <tr class="{$bg}">
    <td><input type="checkbox" name="selectedUserList[]" value="{$access.user.contentobject_id}"{cond( $canGrant|not(), ' disabled="disabled"' )} />
    <td class="nowrap">{$access.user.contentobject.name|wash()}</td>
    <td class="nowrap">{$access.created|l10n( 'datetime' )}</td>
    <td>
     <input type="hidden" name="userFlags[{$access.user.contentobject_id}][]" value="0" /> {* Prevent error if no flag is checked *}

            {foreach $access.access_keys as $key => $text}

     <input type="checkbox" name="userFlags[{$access.user.contentobject_id}][]" value="{$key}"{cond( is_set( $access.flags[$key] ), ' checked="checked"' )}{cond( $canGrant|not(), ' disabled="disabled"' )} />
     <span style="margin-right: 1.0em;">{$text|wash()}</span>

            {/foreach}

    </td>
   </tr>

        {/foreach}

  </table>

    {else}

        {'Currently no users are allowed to access this lightbox.'|i18n( 'lightbox/edit' )}

    {/if}

 </fieldset>
 <div class="lightbox-buttons">
  <input type="submit" name="StoreLightboxButton" value="{'Store changes'|i18n( 'lightbox/edit' )}" title="{'Use this button to save the changes in the lightbox name and user rights'|i18n( 'lightbox/edit' )}" class="button" />
  <input type="submit" name="DeleteLightboxButton" value="{'Delete lightbox'|i18n( 'lightbox/edit' )}" title="{'Use this button to delete this lightbox'|i18n( 'lightbox/edit' )}" class="button" />

    {if $hasUsers}

  <input type="submit" name="DeleteUsersButton" value="{'Delete users'|i18n( 'lightbox/edit' )}" title="{'Use this button to delete the selected users from the list of user that are allowed to access your lightbox'|i18n( 'lightbox/edit' )}" class="button" />

    {/if}

    {if and( is_set( $redirectURI ), $redirectURI|ne( '' ) )}

  <input type="hidden" name="redirectURI" value="{$redirectURI}" />
  <input type="submit" name="GoBackButton" value="{'Go back'|i18n( 'lightbox/edit' )}" title="{'Use this button to go back.'|i18n( 'lightbox/edit' )}" class="button" />

    {/if}

 </div>

{else}

      {'No lightbox selected'|i18n( 'lightbox/edit' )}

{/if}

</form>
