<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$strNavQueryString     = ($arResult['NavQueryString'] != '' ? $arResult['NavQueryString'] . '&amp;' : '');
$strNavQueryStringFull = ($arResult['NavQueryString'] != '' ? '?' . $arResult['NavQueryString'] : '');
// to show always first and last pages
$arResult['nStartPage'] = 1;
$arResult['nEndPage']   = $arResult['NavPageCount'];

$sPrevHref = '';
if ($arResult['NavPageNomer'] > 1) {

    if ($arResult['bSavePage'] || $arResult['NavPageNomer'] > 2) {
        $sPrevHref = $arResult['sUrlPath'] . '?' . $strNavQueryString . 'PAGEN_' . $arResult['NavNum'] . '='
                     . ($arResult['NavPageNomer'] - 1);
    } else {
        $sPrevHref = $arResult['sUrlPath'] . $strNavQueryStringFull;
    }
}

$sNextHref = '';
if ($arResult['NavPageNomer'] < $arResult['NavPageCount']) {
    $sNextHref = $arResult['sUrlPath'] . '?' . $strNavQueryString . 'PAGEN_' . $arResult['NavNum'] . '='
                 . ($arResult['NavPageNomer'] + 1);
}
