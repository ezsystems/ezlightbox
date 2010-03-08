<?php /*

[CommonSettings]
UserSendOwnLightbox=enabled
UseShop=disabled

[LightboxItemSettings]
AvailableItemList[]
AvailableItemList[]=eZContentObject
AvailableItemList[]=eZContentNode

# Settings used by the lightboxcleanup cronjob
# script to clean up the lightbox database tables
# and remove "invalid" data.
[CleanUpSettings]
# Used if there are lightboxes with owners that
# do not exist in the system anymore.
# Can be either "disabled" or a user ID of a user
# that exists in the system
ReplaceInvalidOwnerWithID=14
# What to do with lightbox items (for example
# Content Objects or Content nodes) that do not
# exist anymore.
# "enabled" will remove them while "disabled"
# will leave them untouched
RemoveInvalidItems=enabled

*/ ?>
