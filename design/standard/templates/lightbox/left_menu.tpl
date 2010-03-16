{def $show_links  = and( $module_result.ui_component|eq( 'lightbox' ), $module_result.ui_context|ne( 'edit' ) )
     $currentText = ''}

<div class="lightboxmenu">
 <div class="mylightboxes">
  <h1>{'My lightboxes'|i18n( 'design/standard/lightbox/menu' )}</h1>
  <ul>

{if is_array( $userLightboxList )}

    {foreach $userLightboxList as $lightbox sequence array( 'bglight', 'bgdark' ) as $bg}

   <li>

        {if ezpreference( 'currentLightboxID' )|eq( $lightbox.id )}

            {set $currentText = concat( '(', 'current'|i18n( 'design/standard/lightbox/menu' ), ')' )|wash()}

        {else}

            {set $currentText = ''}

        {/if}

        {if and( $show_links, $lightboxID|ne( $lightbox.id ) )}

    &gt; <a href={concat( '/lightbox/view/', $viewMode , '/', $lightbox.id)|ezurl()} title="{'View this lightbox'|i18n( 'design/standard/lightbox/menu' )}">{$lightbox.name|shorten( 25 )|wash()}</a> {$currentText}

        {else}

    &gt; {$lightbox.name|shorten( 25 )|wash()} {$currentText}

        {/if}

   </li>

    {/foreach}

{else}

   <li class="nobox">{'You do not have any lightbox yet.'|i18n( 'design/standard/lightbox/menu' )}</li>

{/if}

  </ul>
 </div>
 <div class="otherlightboxes">
  <h1>{'Other lightboxes'|i18n( 'design/standard/lightbox/menu' )}</h1>
  <ul>

{if and( is_array( $otherLightboxList ), $otherLightboxList|count()|gt( 0 ) )}

    {foreach $otherLightboxList as $lightbox sequence array( 'bglight', 'bgdark' ) as $bg}

   <li>

        {if and( $show_links, $lightboxID|ne( $lightbox.lightbox_id ) )}

    &gt; <a href={concat( '/lightbox/view/', $viewMode , '/', $lightbox.lightbox_id)|ezurl()} title="{'View this lightbox owned by %1'|i18n( 'design/standard/lightbox/menu', , hash( '%1', $lightbox.owner.contentobject.name|wash() ) )}">{$lightbox.lightbox.name|shorten( 25 )|wash()}</a>
    ({$lightbox.owner.contentobject.name|wash()}, {$lightbox.flags|implode( ',' )})

        {else}

    &gt; {$lightbox.lightbox.name|shorten( 25 )|wash()}

        {/if}

   </li>

    {/foreach}

{else}

   <li class="nobox">{'You do not have access to any other lightbox yet.'|i18n( 'design/standard/lightbox/menu' )}</li>

{/if}

  </ul>
 </div>
</div>