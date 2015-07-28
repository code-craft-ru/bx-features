<?php
/**
 * Created by PhpStorm.
 * Company: Code Craft
 * User: Manriel
 * Date: 31.03.2015
 * Time: 22:12
 */

/**
 * Bitrix vars
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global CDatabase $DB
 */

$module_id = "codecraft.null";
$mid = $_REQUEST["mid"];
IncludeModuleLangFile(__FILE__);

if(!$USER->CanDoOperation('view_other_settings') && !$USER->CanDoOperation('edit_other_settings'))
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$CAT_RIGHT = $APPLICATION->GetGroupRight($module_id);

if (check_bitrix_sessid() && $CAT_RIGHT=="W") {
    if (isset($_POST['null'])) {
        $_POST['null'] = htmlspecialcharsbx($_POST['null']);
        COption::SetOptionString($module_id, 'null', $_POST['null'], false);
    }
}

if ($CAT_RIGHT>="R") {

    include_once($GLOBALS["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/include.php");

    $arTabs = array(
        array(
            "DIV" => "edit1",
            "TAB" => GetMessage("MAIN_TAB_SET"),
            "ICON" => "codecraft_null_settings",
            "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")
        ),
        array(
            "DIV" => "edit2",
            "TAB" => GetMessage("MAIN_TAB_RIGHTS"),
            "ICON" => "codecraft_null_rights",
            "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")
        )
    );

    $tabControl = new CAdminTabControl("tabControl", $arTabs);

    $tabControl->Begin();
    ?>
    <form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?echo LANG?>">
        <?=bitrix_sessid_post();?>
    <?$tabControl->BeginNextTab(); // SETTINGS?>
        <tr class="heading">
            <td colspan="2"><b><?=GetMessage("CODECRAFT_NULL_OPT")?></b></td>
        </tr>
        <tr>
            <td width="50%" class="adm-detail-content-cell-l">
                <label for="null"><?=GetMessage('CODECRAFT_NULL_NULL')?>:</label>
            </td>
            <td width="50%" class="adm-detail-content-cell-r">
                <input type="text" size="30" maxlength="255" name="null" value="<?=COption::GetOptionString($module_id, 'null', '')?>"></inpit>
            </td>
        </tr>
    <?$tabControl->BeginNextTab(); // RIGHTS?>
        <?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
    <?$tabControl->Buttons();?>
        <input type="submit" <?if ($CAT_RIGHT<"W") echo "disabled" ?> name="Update" value="<?echo GetMessage("MAIN_SAVE")?>">
        <input type="hidden" name="Update" value="Y">
        <input type="reset" name="reset" value="<?echo GetMessage("MAIN_RESET")?>">
    </form>
    <?$tabControl->End();?>
<?
}
?>