<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var CCodeCraftNullComponent $this
 * @var array                   $arParams
 *
 * @global CUser                $USER
 * @global CMain                $APPLICATION
 * @global CDatabase            $DB
 */

use Bitrix\Main\Localization\Loc;

global $USER;
global $APPLICATION;
global $DB;

$arResult = [];

if ($this->StartResultCache()) {
    if ($this->getFalse()) {
        ShowError(Loc::getMessage('COMPONENT_NULL_ERROR_MESSAGE'));
        $this->AbortResultCache();

        return;
    }

    $arResult['TEST_STRING'] = $this->getTestString();

    $this->SetResultCacheKeys(['TEST_STRING']);

    $this->IncludeComponentTemplate();
}