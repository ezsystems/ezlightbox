<h1>{'Create new lightbox'|i18n( 'lightbox/create' )}</h1>

{if and( is_set( $messages ), $messages|count()|gt( 0 ) )}

<div class="message-{cond( $actionSuccess, 'feedback', true(), 'error' )}">

    {foreach $messages as $message}

 <p>{$message|wash()}</p>

    {/foreach}

</div>

{/if}

<form action={$url|ezurl()} method="post">
 <table>
  <tr>
   <td><label>{'Name'|i18n( 'lightbox/create' )}:</label></td>
   <td><input type="text" name="lightbox_name" value="{$lightbox_name|wash()}" size="50" /></td>
  </tr>

{if ezpreference( 'currentLightboxID' )}

  <tr>
   <td colspan="2">
    <input type="checkbox" name="changeToCurrentLightbox" value="true" /> {'Use the newly created lightbox as the current lightbox.'|i18n( 'lightbox/create' )}
   </td>
  </tr>

{/if}

 </table>
 <div class="lightbox-buttons">

{if is_set( $addItemIDAfterCreate )}

    {'Once the lightbox has been created successfully, a new item will be added to it.'|i18n( 'lightbox/create' )}

  <input type="hidden" name="addItemIDAfterCreate" value="{$addItemIDAfterCreate}" />

{/if}

{if is_set( $addTypeIDAfterCreate )}

  <input type="hidden" name="addTypeIDAfterCreate" value="{$addTypeIDAfterCreate}" />

{/if}

  <input type="submit" name="CreateLightboxButton" value="{'Create new'|i18n( 'lightbox/create' )}" class="button" />

{if and( is_set( $redirectURI ), $redirectURI|ne( '' ) )}

  <input type="hidden" name="redirectURI" value="{$redirectURI}" />
  <input type="submit" name="GoBackButton" value="{'Go back'|i18n( 'lightbox/create' )}" title="{'Use this button to go back.'|i18n( 'lightbox/create' )}" class="button" />

{/if}

 </div>
</form>
