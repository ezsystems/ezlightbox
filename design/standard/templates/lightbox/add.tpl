{if $scriptmode}{if $actionSuccess}OK{else}FAILED{/if}{else}{if $actionSuccess}{'Successfully added item to lightbox'|i18n( 'lightbox/add' )|wash()}{else}{'Failed to add item to lightbox'|i18n( 'lightbox/add' )|wash()}{foreach $messages as $messages}{$message|wash()}{/foreach}{/if}{/if}

