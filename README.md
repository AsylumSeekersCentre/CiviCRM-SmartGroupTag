CiviCRM-SmartGroupTag
=====================

CiviCRM Extension providing automatic tagging based on Smart Groups.

This extension reads from the map.txt file to associate Tags with Smart Groups.

To specify the mapping, paste CSV data into the configuration page at /civicrm/smartgrouptag-config (requires admin permissions).

The "Updatetags" function can be run from the API explorer, or scheduled as a cron job.

At present, it expects one tag to apply to only one group. This limitation should be fixed in future versions.

Multiple tags can be applied to the same group.

