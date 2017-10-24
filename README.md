CiviCRM-SmartGroupTag
=====================

CiviCRM Extension providing automatic tagging based on Smart Groups.

This extension reads from the map.txt file to associate Tags with Smart Groups.

For each association between tag and group, it performs these actions:
 - Get the list of all contacts with the specified Tag, called tagList.
 - Get the list of all contacts in the specified Group, called groupList.
 - Subtract groupList from tagList to get the Contacts who have the tag and shouldn't. Remove the tag from these contacts.
 - Subtract the tagList from the groupList to get the Contacts who don't have the tag and should. Apply the tag to these contacts.
 - Record the number of tags deleted, added, and confirmed.

At present, it expects one tag to apply to only one group. This limitation should be fixed in future versions.

Multiple tags can be applied to the same group.

