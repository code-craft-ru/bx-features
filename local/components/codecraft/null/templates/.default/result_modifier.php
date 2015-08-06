<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var CBitrixComponentTemplate $this
 *
 * @var array                    $arParams
 * @var array                    $arResult
 *
 * @global CUser                 $USER
 * @global CMain                 $APPLICATION
 * @global CDatabase             $DB
 */

$arResult['TITLE']       = $arResult['TEST_STRING'];
$arResult['TEST_STRING'] = 'Replaced test string';