<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

//условие для отключения постраничной навигации, если только одна страница или нет элементов
if (!$arResult['NavShowAlways']) {
    if ($arResult['NavRecordCount'] == 0 || ($arResult['NavPageCount'] == 1 && $arResult['NavShowAll'] == false)) {
        return;
    }
}
?>

<div class='navigation-pages'>
    <? if ($arResult['NavPageNomer'] > 1) { ?>
        <a href='<?= $arResult['sUrlPath'] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult['NavNum'] ?>=1'><?= GetMessage('nav_begin') ?></a>&nbsp;|&nbsp;
        <a href='<?= $arResult['sUrlPath'] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult['NavNum'] ?>=<?= ($arResult['NavPageNomer']
                                                                                                             - 1) ?>'>&laquo;</a>
        &nbsp;|&nbsp;
    <? } ?>
    <?
    $bFirst  = true;
    $bPoints = false;
    do {
        if ($arResult['nStartPage'] < 2 || $arResult['nEndPage'] - $arResult['nStartPage'] < 1
            || abs($arResult['nStartPage'] - $arResult['NavPageNomer']) < 2
        ) {

            if ($arResult['nStartPage'] == $arResult['NavPageNomer']):
                ?>
                <span class='nav-current-page'><?= $arResult['nStartPage'] ?></span>
                <?
            elseif ($arResult['nStartPage'] == 1 && $arResult['bSavePage'] == false):
                ?>
                <a href='<?= $arResult['sUrlPath'] ?><?= $strNavQueryStringFull ?>'><?= $arResult['nStartPage'] ?></a>
                <?
            else:
                ?>
                <a href='<?= $arResult['sUrlPath'] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult['NavNum'] ?>=<?= $arResult['nStartPage'] ?>'><?= $arResult['nStartPage'] ?></a>
                <?
            endif;
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

        |&nbsp;<a
            href='<?= $arResult['sUrlPath'] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult['NavNum'] ?>=<?= ($arResult['NavPageNomer']
                                                                                                              + 1) ?>'>&raquo;</a>&nbsp;|&nbsp;
        <a href='<?= $arResult['sUrlPath'] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult['NavNum'] ?>=<?= $arResult['NavPageCount'] ?>'><?= GetMessage('nav_end') ?></a>&nbsp;
    <? }

    if ($arResult['bShowAll']):
        if ($arResult['NavShowAll']):
            ?>
            <a class='nav-page-pagen'
               href='<?= $arResult['sUrlPath'] ?>?<?= $strNavQueryString ?>SHOWALL_<?= $arResult['NavNum'] ?>=0'><?= GetMessage('nav_paged') ?></a>
            <?
        else:
            ?>
            <a class='nav-page-all'
               href='<?= $arResult['sUrlPath'] ?>?<?= $strNavQueryString ?>SHOWALL_<?= $arResult['NavNum'] ?>=1'><?= GetMessage('nav_all') ?></a>
            <?
        endif;
    endif;
    ?>
</div>
