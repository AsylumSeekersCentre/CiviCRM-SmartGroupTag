{literal}
<script>
 function showAlert($) {
   alert("Button pressed");
 };
 function pressButton($) {
   var tag = CRM.$('#tagSelect').find(":selected").val();
   var group = CRM.$('#groupSelect').find(":selected").val();
   CRM.api3('Smarttag', 'bindpair', {
     "sequential": 1,
     "tagId":tag,
     "groupId":group
     }).done(function(result) {
       alert("Tag applied:\nTag = " + tag + " Group = " + group);
   });
 };
 function onReady($) {
   CRM.$('#applyButton').click(pressButton);
 };
 CRM.$(document).ready(onReady);
</script>
{/literal}

{literal}
<script>
 function pressRemoveButton(id, $) {
 /*
   CRM.api3('Smarttag', 'removepair', {
     "sequential": 1,
     "mapId":{/literal}$row.id{literal}
     }).done(function(result) {
       alert("Group-Tag pair removed.");
   });
 */
       alert("Group-Tag pair " + id + " removed.");
 };
 function onRemoveReady(id) {
   CRM.$('#removeButton[id="' + id + '"]').click(pressRemoveButton.bind(id));
 };
{/literal}
 {foreach from=$tagMap item=row}
  {literal}
   CRM.$(document).ready(onRemoveReady.bind({/literal}{$row.id}{literal}));
  {/literal}
 {/foreach}
</script>


<h3>This is the configuration page for the SmartGroupTag extension.</h3>

{* Example: Display a variable directly *}
<p>The current time is {$currentTime}</p>

{* Example: Display a translated string -- which happens to include a variable *}
<p>{ts 1=$currentTime}(In your native language) The current time is %1.{/ts}</p>
<p>Here is the current mapping:</p>
<table width=800px>
{foreach from=$tagMap item=row}
  <tr>
    <td>{$row.id}</td>
    <td>{$row.tag_id}</td>
    <td>{$row.group_id}</td>
    <td> 
       <a title="Remove" id='removeButton[id="{$row.id}"]' class="button" href="javascript: void(0);">
       <span>
       <div class="icon icon_name-icon"></div>
       Remove
       </span>
       </a>
    </td>
  </tr>
{/foreach}
</table>


<div class="crm-accordion-wrapper collapsed">
  <div class="crm-accordion-header">
    Add
  </div>
  <div class="crm-accordion-body">
     <div class="crm-block crm-form-block crm-form-title-here-form-block">
       <p>Add mapping from tag:</p>
       <p>
       <select id="tagSelect">
         <option value=-1>None</option>
       {foreach from=$tags item=tagData}
         <option value={$tagData.id}>{$tagData.name}</option>
       {/foreach}
       </select>
       </p>

       <p>To group:</p>
       <p>
       <select id="groupSelect">
         <option value=-1>None</option>
       {foreach from=$groups item=groupData}
         <option value={$groupData.id}>{$groupData.name}</option>
       {/foreach}
       </select>
       </p>

       <a title="Add" id="applyButton" class="button" href="javascript: void(0);">
       <span>
       <div class="icon icon_name-icon"></div>
       Apply
       </span>
       </a>

     </div>
   </div>
</div>

