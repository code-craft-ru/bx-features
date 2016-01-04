<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var CBitrixComponentTemplate $this
 *
 * @var array                    $arParams
 * @var array                    $arResult
 * @var array                    $templateData
 *
 * @var string                   $componentPath
 * @var string                   $templateName
 * @var string                   $templateFile
 * @var string                   $templateFolder
 *
 * @global CUser                 $USER
 * @global CMain                 $APPLICATION
 * @global CDatabase             $DB
 */

use Bitrix\Main\Localization\Loc;
?>
<div class="header-favs js-small-basket">
    <a href="<?= $arParams['PATH_TO_FAVORITE'] ?>" title="<?= Loc::getMessage('CC_SBL_IN_FAVORITE') ?>">
        <i class="icon icon-star-white"></i>
        <span class="header-favs__info<?= $arResult['FAVORITE_POSITION_COUNT'] ? ' header-favs__info--fill' : '' ?>">
            <?= $arResult['FAVORITE_POSITION_COUNT'] ?>
        </span>
    </a>

    <a href="<?= $arParams['PATH_TO_BASKET'] ?>" title="<?= Loc::getMessage('CC_SBL_IN_BASKET') ?>">
        <i class="icon icon-cart-white"></i>
        <span class="header-favs__info<?= $arResult['BASKET_POSITION_COUNT'] ? ' header-favs__info--fill' : '' ?>">
            <?= $arResult['BASKET_POSITION_COUNT'] ?>
        </span>
    </a>
</div>