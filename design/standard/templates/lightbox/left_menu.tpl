{def     $show_links  = and( $module_result.ui_component|eq( 'lightbox' ), $module_result.ui_context|ne( 'edit' ) )
         $currentText = ''
       $viewAccessKey = fetch( 'lightbox', 'accessKeyByName', hash( 'name', 'view' ) )
        $lightboxList = fetch( 'lightbox', 'list', hash( 'otherMasks', array( $viewAccessKey ) ) )
       $ownLightboxes = fetch( 'lightbox', 'listOwn' )
     $otherLightboxes = fetch( 'lightbox', 'listOther', hash( 'accessKeys', array( $viewAccessKey ) ) )}

<div class="lightboxmenu">
 <div class="mylightboxes">
  <h1>{'My lightboxes'|i18n( 'lightbox/menu' )}</h1>
  <ul>

{if and( is_array( $ownLightboxes ), $ownLightboxes|count()|gt( 0 ) )}

    {foreach $ownLightboxes as $lightbox sequence array( 'bglight', 'bgdark' ) as $bg}

   <li>

        {if ezpreference( 'currentLightboxID' )|eq( $lightbox.id )}

            {set $currentText = concat( '(', 'current'|i18n( 'lightbox/menu' ), ')' )|wash()}

        {else}

            {set $currentText = ''}

        {/if}

        {if and( $show_links, $shownLightboxID|ne( $lightbox.id ) )}

    &gt; <a href={concat( '/lightbox/view/', $viewMode , '/', $lightbox.id)|ezurl()} title="{'View this lightbox'|i18n( 'lightbox/menu' )}">{$lightbox.name|shorten( 25 )|wash()}</a> {$currentText}

        {else}

    &gt; {$lightbox.name|shorten( 25 )|wash()} {$currentText}

        {/if}

   </li>

    {/foreach}

{else}

   <li class="nobox">{'You do not have any lightbox yet.'|i18n( 'lightbox/menu' )}</li>

{/if}

  </ul>
 </div>
 <div class="otherlightboxes">
  <h1>{'Other lightboxes'|i18n( 'lightbox/menu' )}</h1>
  <ul>

{if and( is_array( $otherLightboxes ), $otherLightboxes|count()|gt( 0 ) )}

    {foreach $otherLightboxes as $lightbox sequence array( 'bglight', 'bgdark' ) as $bg}

   <li>

        {if ezpreference( 'currentLightboxID' )|eq( $lightbox.id )}

            {set $currentText = 'current'|i18n( 'lightbox/menu' )|wash()}

        {else}

            {set $currentText = ''}

        {/if}

        {if and( $show_links, $shownLightboxID|ne( $lightbox.id ) )}

    &gt; <a href={concat( '/lightbox/view/', $viewMode , '/', $lightbox.id)|ezurl()} title="{'View this lightbox owned by %1'|i18n( 'lightbox/menu', , hash( '%1', $lightbox.owner.contentobject.name|wash() ) )}">{$lightbox.name|shorten( 25 )|wash()}</a>
    ({cond( $currentText, concat( $currentText, ', ' ) )}{$lightbox.owner.contentobject.name|wash()}, {$lightbox.access_object.flags|implode( ',' )})

        {else}

    &gt; {$lightbox.name|shorten( 25 )|wash()}{if $currentText|ne( '' )}( {$currentText} ){/if}

        {/if}

   </li>

    {/foreach}

{else}

   <li class="nobox">{'You do not have access to any other lightbox yet.'|i18n( 'lightbox/menu' )}</li>

{/if}

  </ul>
 </div>
</div>