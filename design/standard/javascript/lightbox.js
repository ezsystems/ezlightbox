var eZLightboxLibrary = {};

eZLightboxLibrary.changeCurrentLightbox = function()
{
    hiddenForm       = document.getElementById( 'hiddenlightboxselectionform' );
    lightboxSelect   = document.getElementById( 'lightboxselection' );
    inputField       = document.getElementById( 'newLightboxIDInput' );
    inputField.value = lightboxSelect.options[lightboxSelect.selectedIndex].value;
    hiddenForm.submit();
}

eZLightboxLibrary.setLightboxItem = function( itemIDTarget, itemID, itemTypeTarget, itemType )
{
    if ( document.getElementById )
    {
        var itemIDTargetDOMObject   = document.getElementById( itemIDTarget ),
            itemTypeTargetDOMObject = document.getElementById( itemTypeTarget );
        if ( itemIDTargetDOMObject !== undefined && itemIDTargetDOMObject !== null )
        {
            itemIDTargetDOMObject.value = itemID;
        }
        if ( itemTypeTargetDOMObject !== undefined && itemTypeTargetDOMObject !== null )
        {
            itemTypeTargetDOMObject.value = itemType;
        }
    }
}

eZLightboxLibrary.sendLightboxActionForm = function( targetID, actionName, confirmDelete )
{
    if ( confirmDelete !== false )
    {
        if ( !confirm( confirmDelete ) )
        {
            return false;
        }
    }
    if ( document.getElementById )
    {
        var hiddenFormDOMObject = document.getElementById( targetID );
        if ( hiddenFormDOMObject !== undefined && hiddenFormDOMObject !== null )
        {
            var submitFieldDOMObject = document.getElementById( targetID + 'SubmitField' );
            if ( submitFieldDOMObject !== undefined && submitFieldDOMObject !== null  )
            {
                submitFieldDOMObject.name  = actionName;
                submitFieldDOMObject.value = 'true';
            }
            hiddenFormDOMObject.submit();
        }
    }
}