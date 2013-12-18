{combine_script id='jquery.timepicker' require='jquery.ui.datepicker,jquery.ui.slider' load='footer' path=$BIRTHDATE_PATH|@cat:"admin/template/jquery-ui-timepicker-addon.js"}

{assign var="datepicker_language" value="themes/default/js/ui/i18n/jquery.ui.datepicker-`$lang_info.code`.js"}

{if "PHPWG_ROOT_PATH"|@constant|@cat:$datepicker_language|@file_exists}
{combine_script id="jquery.ui.datepicker-$lang_info.code" load='footer' path=$datepicker_language}
{/if}

{combine_script id='jquery.chosen' load='footer' path='themes/default/js/plugins/chosen.jquery.min.js'}
{combine_css path="themes/default/js/plugins/chosen.css"}

{combine_css path="themes/default/js/ui/theme/jquery.ui.datepicker.css"}
{combine_css path="themes/default/js/ui/theme/jquery.ui.slider.css"}

{footer_script require='jquery.timepicker'}
jQuery(document).ready(function() {
  jQuery("#who").chosen({ "width":"300px" });

  jQuery('#birthdateSelect').datetimepicker({
    numberOfMonths: 1,
    changeMonth: true,
    changeYear: true,
    yearRange: "1900:+1",
    dateFormat: "yy-mm-dd",
    timeText: '{'selection'|@translate|escape:javascript}',
    hourText: '{'Hour'|@translate|escape:javascript}',
    minuteText: '{'Minute'|@translate|escape:javascript}',
    currentText: '{'Now'|@translate|escape:javascript}',
    closeText: '{'Validate'|@translate|escape:javascript}'
  });

  jQuery("#displayForm").click(function() {
    jQuery("#editLegend").hide();
    jQuery("#addLegend").show();

    jQuery("[name=add_birthdate]").show();
    jQuery(this).hide();
    return false;
  });

  jQuery("#cancelBirthdate").click(function() {
    jQuery("[name=add_birthdate]").hide();
    jQuery("#displayForm").show();
    return false;
  });

  jQuery(".editBirthdate").click(function() {
    var tag_id = jQuery(this).data("tag_id");
    var birthdate = jQuery(this).data("birthdate");

    console.log("tag_id = "+tag_id+"; birthdate = "+birthdate);

    jQuery("#displayForm").hide();

    jQuery("#editLegend").show();
    jQuery("#addLegend").hide();

    jQuery("[name=add_birthdate]").show();
    jQuery("#who").val("~~"+tag_id+"~~").trigger("chosen:updated");
    jQuery("#birthdateSelect").val(birthdate);

    jQuery("#birthdateList tr").removeClass("rowSelected");
    jQuery(this).parentsUntil("tr").parent().addClass("rowSelected");
    window.scroll(0,140); // horizontal and vertical scroll targets

    return false;
  });

  jQuery("form[name=add_birthdate]").submit(function(){
    jQuery("form[name=add_birthdate] .error").hide();
    var nb_errors = 0;

    if ("" == jQuery("#who").val()) {
      jQuery("#whoError").show();
      nb_errors++;
    }

    if ("" == jQuery("#birthdateSelect").val()) {
      jQuery("#birthdateError").show();
      nb_errors++;
    }

    if (nb_errors > 0) {
      return false;
    }
  });

});
{/footer_script}

{html_style}{literal}
form fieldset p {text-align:left;margin:0 0 1.5em 0;line-height:20px;}
.rowSelected {background-color:#C2F5C2 !important}
form .error {display:none; color:red;}
.birthdateActions {text-align:center;}
.birthdateActions a:hover {border:none}
.ui-datepicker-current {display:none}

#editLegend {display:none}
.birthdateActions a {display:inline-block;}
.birthdateActions a:hover {text-decoration:none;}
{/literal}{/html_style}

<div class="titrePage">
  <h2>{'Manage birthdates'|@translate}</h2>
</div>

<p style="text-align:left;margin:0 1em;">
  <a id="displayForm" href="#">{'Add a birthdate'|@translate}</a>
</p>

<form method="post" name="add_birthdate" action="{$F_ADD_ACTION}" style="display:none">
  <fieldset>
    <legend>
      <span id="editLegend">{'Edit a birthdate'|@translate}</span>
      <span id="addLegend">{'Add a birthdate'|@translate}</span>
    </legend>

    <p>
      <strong>{'Who?'|@translate}</strong>
      <br>
      <select id="who" name="who" data-placeholder="{'Select a tag'|@translate}">
        <option value=""></option>
{foreach from=$tags item=tag}
        <option value="{$tag.id}">{$tag.name}</option>
{/foreach}
      </select>
      <span class="error" id="whoError">&#x2718; {'Select a tag'|@translate}</span>
    </p>

    <p>
      <strong>{'Birthdate'|@translate}</strong>
      <br>
      <input type="text" id="birthdateSelect" name="birthdate" style="width:120px;" value="{$BIRTHDATE}">
      <span class="error" id="birthdateError">&#x2718; {'Select a birthdate'|@translate}</span>
    </p>

    <p style="margin:0;">
      <input class="submit" type="submit" name="submit_add" value="{'Submit'|@translate}"/>
      <a href="#" id="cancelBirthdate">{'Cancel'|@translate}</a>
    </p>
  </fieldset>
</form>

<table id="birthdateList" class="table2" style="margin:1em;">
  <tr class="throw">
    <th>{'Who?'|@translate} ({'Tag'|@translate})</th>
    <th>{'Birthdate'|@translate}</th>
    <th>{'Photos'|@translate}</th>
    <th>{'Actions'|@translate}</th>
  </tr>
{if not empty($birthdates)}
  {foreach from=$birthdates item=birthdate name=birthdate_loop}
  <tr class="{if $smarty.foreach.birthdate_loop.index is odd}row1{else}row2{/if}{if $birthdate.HIGHLIGHT} rowSelected{/if}">
    <td>{$birthdate.NAME}</td>
    <td>{$birthdate.BIRTHDATE}</td>
    <td>{$birthdate.PHOTOS}</td>
    <td class="birthdateActions">
      <a href="#" class="editBirthdate icon-pencil" data-tag_id="{$birthdate.TAG_ID}" data-birthdate="{$birthdate.BIRTHDATE_RAW}" title="{'edit'|@translate}"></a>
      <a href="{$birthdate.U_DELETE}" onclick="return confirm( document.getElementById('btn_delete{$birthdate.TAG_ID}').title + '\n\n' + '{'Are you sure?'|@translate|@escape:'javascript'}');" class="icon-trash" title="{'Delete birthdate for %s'|@translate|@sprintf:$birthdate.NAME}"></a>
    </td>
  </tr>
  {/foreach}
{/if}
</table>
