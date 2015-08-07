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

?>

<div class="inb-m lang-group">
    <? foreach ($arResult['LANGUAGE_LIST'] as $language) { ?>
        <a href="<?= $language['URL'] ?>" class="btn-round btn<?= $language['ACTIVE'] ? '' : ' disabled' ?> inb-m"><?= $language['NAME'] ?></a>
    <? } ?>
</div>