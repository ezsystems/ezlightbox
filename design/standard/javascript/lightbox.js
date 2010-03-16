function changeCurrentLightbox()
{
    hiddenForm       = document.getElementById( 'hiddenlightboxselectionform' );
    lightboxSelect   = document.getElementById( 'lightboxselection' );
    inputField       = document.getElementById( 'newLightboxIDInput' );
    inputField.value = lightboxSelect.options[lightboxSelect.selectedIndex].value;
    hiddenForm.submit();
}
