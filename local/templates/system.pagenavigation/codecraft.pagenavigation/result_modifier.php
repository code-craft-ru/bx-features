<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
//var_dump($arResult);
//$strNavQueryString         = ($arResult['NavQueryString'] != '' ? $arResult['NavQueryString'] . '&amp;' : '');
//$arResult['PATH_TEMPLATE'] = $arResult['sUrlPath'] . '?' . $strNavQueryString;
$arResult['PAGEN']         = 'PAGEN_' . $arResult['NavNum'] . '=';
$arResult['PATH_TEMPLATE'] = $arResult['sUrlPathParams'] . $arResult['PAGEN'];
$arResult['nStartPage']    = 1;
$arResult['nEndPage']      = $arResult['NavPageCount'];

$arResult['START_PAGE_LINK'] = $arResult['PATH_TEMPLATE'] . $arResult['nStartPage'];
$arResult['END_PAGE_LINK']   = $arResult['PATH_TEMPLATE'] . $arResult['nEndPage'];

if ($arResult['NavPageNomer'] > $arResult['nStartPage']) {
    $arResult['PREV_PAGE_NUMBER'] = $arResult['NavPageNomer'] - 1;
    $arResult['PREV_PAGE_LINK']   = $arResult['PATH_TEMPLATE'] . $arResult['PREV_PAGE_NUMBER'];
}

if ($arResult['NavPageNomer'] < $arResult['nEndPage']) {
    $arResult['NEXT_PAGE_NUMBER'] = $arResult['NavPageNomer'] + 1;
    $arResult['NEXT_PAGE_LINK']   = $arResult['PATH_TEMPLATE'] . $arResult['NEXT_PAGE_NUMBER'];
}

if ($arResult['bShowAll']) {
    if ($arResult['NavShowAll']) {
        $showAllFlag = 0;
    } else {
        $showAllFlag = 1;
    }
    $arResult['SHOW_ALL_LINK'] = $arResult['sUrlPathParams'].'SHOWALL_'.$arResult["NavNum"].'='.$showAllFlag;
}
