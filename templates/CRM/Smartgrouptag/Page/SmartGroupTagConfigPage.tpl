{literal}
<script>
 function pressButton($) {
//     CRM.alert("Apply pressed, but nothing will be done.\n map : \n" + CRM.$('textarea').val());
   CRM.api3('Smarttag', 'updatemap', {"sequential": 1,
       "tag_map":CRM.$('textarea').val()
     }); // .done(function(result) {
//     CRM.alert("Apply pressed, map = \n" + CRM.$('textarea').val());
//   });

//   var tag = CRM.$('#tagSelect').find(":selected").val();
//   var group = CRM.$('#groupSelect').find(":selected").val();
//   CRM.api3('Smarttag', 'bindpair', {
//     "sequential": 1,
//     "tagId":tag,
//     "groupId":group
//     }).done(function(result) {
//       alert("Tag applied:\nTag = " + tag + " Group = " + group);
//   });
 };
 function onReady($) {
   CRM.$('#applyButton').click(pressButton);
 };
 CRM.$(document).ready(onReady);
</script>
{/literal}


<h3>This is the configuration page for the SmartGroupTag extension.</h3>

{* Example: Display a variable directly *}
<p>The current time is {$currentTime}</p>

{* Example: Display a translated string -- which happens to include a variable *}
<p>{ts 1=$currentTime}(In your native language) The current time is %1.{/ts}</p>
<p>Here is the current mapping:</p>
<table width=100% border=1px>
{foreach from=$tagMap item=row}
  <tr>
    <td>{$row.id}</td>
    <td>{$row.tag_id}</td>
    <td>{$row.group_id}</td>
  </tr>
{/foreach}
</table>

<div class="crm-accordion-wrapper collapsed">
  <div class="crm-accordion-header">
    Replace
  </div>
  <div class="crm-accordion-body">
     <div class="crm-block crm-form-block crm-form-title-here-form-block">
       <p> Paste the new mapping CSV data here (tag first, then group):</p>
<p>
<textarea rows="26" cols="80">
{foreach from=$tagMap item=row}
{$row.tag_id},{$row.group_id}
{/foreach}</textarea>
</p>
       <a title="Apply" id="applyButton" class="button" href="javascript: void(0);">
       <span>
       <div class="icon icon_name-icon"></div>
       Apply
       </span>
       </a>
     </div>
   </div>
</div>

