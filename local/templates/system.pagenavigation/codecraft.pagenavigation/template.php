<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
//var_dump($arResult);
use \Bitrix\Main\Localization\Loc;

//условие для отключения постраничной навигации, если только одна страница или нет элементов
if (!$arResult['NavShowAlways']) {
    if ($arResult['NavRecordCount'] == 0 || ($arResult['NavPageCount'] == 1 && $arResult['NavShowAll'] == false)) {
        return;
    }
}
?>

<div class="navigation-pages">
    <? if ($arResult['NavPageNomer'] > 1) { ?>
        <a href="<?= $arResult['START_PAGE_LINK'] ?>"><?= Loc::GetMessage("nav_begin") ?></a>&nbsp;|&nbsp;
        <a href="<?= $arResult['PREV_PAGE_LINK'] ?>">&laquo;</a>
        &nbsp;|&nbsp;
    <? } ?>
    <?
    $bFirst  = true;
    $bPoints = false;
    do {
        if ($arResult['nStartPage'] < 2 || $arResult['nEndPage'] - $arResult['nStartPage'] < 1
            || abs($arResult['nStartPage'] - $arResult['NavPageNomer']) < 2
        ) {

            if ($arResult['nStartPage'] == $arResult['NavPageNomer']) {
                ?>
                <span class="nav-current-page"><?= $arResult['nStartPage'] ?></span>
                <?
            } else {
                ?>
                <a href="<?= $arResult['PATH_TEMPLATE'] . $arResult["nStartPage"] ?>"><?= $arResult["nStartPage"] ?></a>
                <?
            };
            $bFirst  = false;
            $bPoints = true;
        } else {
            if ($bPoints) {
                ?>...<?
                $bPoints = false;
            }
        }
        $arResult['nStartPage']++;
    } while ($arResult['nStartPage'] <= $arResult['nEndPage']);

    if ($arResult['NavPageNomer'] < $arResult['NavPageCount']) { ?>

        |&nbsp;<a href="<?= $arResult['NEXT_PAGE_LINK'] ?>">&raquo;</a>&nbsp;|&nbsp;
        <a href="<?= $arResult['END_PAGE_LINK'] ?>"><?= Loc::GetMessage("nav_end") ?></a>&nbsp;
    <? }

    if ($arResult['bShowAll']) { ?>
        <a class="nav-page-all" href="<?= $arResult['SHOW_ALL_LINK'] ?>"><?= Loc::GetMessage("nav_all") ?></a>
    <? };
    ?>
</div>
