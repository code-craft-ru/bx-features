<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * Created by PhpStorm.
 * User: Manriel
 * Date: 23.03.2015
 * Time: 20:47
 */

global $APPLICATION;
$APPLICATION->SetAdditionalCSS("/bitrix/gadgets/codecraft/admin_info/style.css");

$default = $_SERVER['SERVER_NAME'];
if ($_SERVER['SERVER_PORT'] != 80) {
    $default .= ':'.$_SERVER['SERVER_PORT'];
}
$siteName = COption::GetOptionString('main', 'server_name', $default);
unset($default);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/checklist.php");
$checklist = new CCheckList;
$isStarted = $checklist->started;
if ($isStarted == true)
    $arStat = $checklist->GetSectionStat();
else
{
    $arReports = CCheckListResult::GetList(Array(),Array("REPORT"=>"Y"));
    if ($arReports)
    {
        $arReport = $arReports->Fetch();
        $arReportData = new CCheckList($arReport["ID"]);
        $arReportInfo = $arReportData->GetReportInfo();
        $arStat = $arReportInfo["STAT"];
    }
}

?>
<table class="cc_gadget_info_table">
    <tr>
        <td>
            <span class="cc-logo"></span>
        </td>
        <td style="min-width: 178px;">
            <p><span class="caption">Адрес сайта: </span><a href="http://<?=$siteName?>"><?=$siteName?></a></p>
            <p><span class="caption">Создатель сайта: </span>Студия <a href="http://code-craft.ru">Code Craft</a></p>
            <?if($isStarted):?>
                <p><span class="caption">E-mail: </span><a href="mailto:welcome@code-craft.ru">welcome@code-craft.ru</a></p>
            <?elseif(is_array($arReport)):?>
                <p><span class="caption">Ответственное лицо: </span><?=$arReport["TESTER"]?></p>
                <p><span class="caption">E-mail: </span><a href="mailto:<?=$arReport["EMAIL"]?>"><?=$arReport["EMAIL"]?></a></p>
                <p><span class="caption">Сайт сдан: </span><?$arDate = explode(' ', $arReport["DATE_CREATE"]); echo $arDate[0];?></p>
            <?else:?>
                <p><span class="caption">E-mail: </span><a href="mailto:welcome@code-craft.ru">welcome@code-craft.ru</a></p>
            <?endif;?>
        </td>
    </tr>
</table>