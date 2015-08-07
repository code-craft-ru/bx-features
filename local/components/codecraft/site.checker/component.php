<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var CCodeCraftSiteCheckerComponent $this
 * @var array                          $arParams
 *
 * @global CUser                       $USER
 * @global CMain                       $APPLICATION
 * @global CDatabase                   $DB
 */

$arResult = [];

$arResult['LANGUAGE_LIST'] = $this->getLanguageList();

$this->IncludeComponentTemplate();