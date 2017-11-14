<h3>This is the configuration page for the SmartGroupTag extension.</h3>

{* Example: Display a variable directly *}
<p>The current time is {$currentTime}</p>

{* Example: Display a translated string -- which happens to include a variable *}
<p>{ts 1=$currentTime}(In your native language) The current time is %1.{/ts}</p>
<p>Here is the current mapping:</p>
<table width=800px>
{foreach from=$tagMap key=tagId item=groupId}
  <tr>
    <td>{$tagId}</td>
    <td>{$groupId}</td>
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
       <select>
         <option value=-1>None</option>
       {foreach from=$tags item=tagData}
         <option value={$tagData.id}>{$tagData.name}</option>
       {/foreach}
       </select>
       </p>

       <p>To group:</p>
       <p>
       <select>
         <option value=-1>None</option>
       {foreach from=$groups item=groupData}
         <option value={$groupData.id}>{$groupData.name}</option>
       {/foreach}
       </select>
       </p>
       <a title="Add" class="button_name button" href="#">
       <span>
       <div class="icon icon_name-icon"></div>
       Apply
       </span>
       </a>
     </div>
   </div>
</div>

