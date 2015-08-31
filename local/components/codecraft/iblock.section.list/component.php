<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var CCodeCraftIBlockSectionList $this
 * @var array                       $arParams
 * @var array                       $arResult
 * @var string                      $componentPath
 * @var string                      $componentName
 * @var string                      $componentTemplate
 *
 * @global CUser                    $USER
 * @global CMain                    $APPLICATION
 * @global CDatabase                $DB
 */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

global $USER;
global $APPLICATION;
global $DB;

$this->prepareParams($arParams);

$arResult['SECTIONS'] = array();

if ($this->StartResultCache(false, (!$arParams['CACHE_GROUPS'] ? false : $USER->GetGroups()))) {
    if (!$this->checkComponent()) {
        return;
    }

    $arFilter = $this->prepareFilter();
    $arSelect = $this->prepareSelect();

    $arResult['SECTION'] = false;
    $intSectionDepth     = 0;
    if ($arParams['SECTION_ID'] > 0) {
        $arFilter['ID'] = $arParams['SECTION_ID'];
        $rsSections     = CIBlockSection::GetList(array(), $arFilter, $arParams['COUNT_ELEMENTS'], $arSelect);
        $rsSections->SetUrlTemplates('', $arParams['SECTION_URL']);
        $arResult['SECTION'] = $rsSections->GetNext();
    } elseif ('' != $arParams['SECTION_CODE']) {
        $arFilter['=CODE'] = $arParams['SECTION_CODE'];
        $rsSections        = CIBlockSection::GetList(array(), $arFilter, $arParams['COUNT_ELEMENTS'], $arSelect);
        $rsSections->SetUrlTemplates('', $arParams['SECTION_URL']);
        $arResult['SECTION'] = $rsSections->GetNext();
    }

    if (is_array($arResult['SECTION'])) {
        unset($arFilter['ID']);
        unset($arFilter['=CODE']);
        $arFilter['LEFT_MARGIN']        = $arResult['SECTION']['LEFT_MARGIN'] + 1;
        $arFilter['RIGHT_MARGIN']       = $arResult['SECTION']['RIGHT_MARGIN'];
        $arFilter['<=' . 'DEPTH_LEVEL'] = $arResult['SECTION']['DEPTH_LEVEL'] + $arParams['TOP_DEPTH'];

        $ipropValues                             = new \Bitrix\Iblock\InheritedProperty\SectionValues($arResult['SECTION']['IBLOCK_ID'], $arResult['SECTION']['ID']);
        $arResult['SECTION']['IPROPERTY_VALUES'] = $ipropValues->getValues();

        $arResult['SECTION']['PATH'] = array();
        $rsPath                      = CIBlockSection::GetNavChain($arResult['SECTION']['IBLOCK_ID'], $arResult['SECTION']['ID']);
        $rsPath->SetUrlTemplates('', $arParams['SECTION_URL']);
        while ($arPath = $rsPath->GetNext()) {
            $ipropValues                   = new \Bitrix\Iblock\InheritedProperty\SectionValues($arParams['IBLOCK_ID'], $arPath['ID']);
            $arPath['IPROPERTY_VALUES']    = $ipropValues->getValues();
            $arResult['SECTION']['PATH'][] = $arPath;
        }
    } else {
        $arResult['SECTION']            = array(
            'ID'          => 0,
            'DEPTH_LEVEL' => 0
        );
        $arFilter['<=' . 'DEPTH_LEVEL'] = $arParams['TOP_DEPTH'];
    }
    $intSectionDepth = $arResult['SECTION']['DEPTH_LEVEL'];

    $arSort     = array('left_margin' => 'asc',);
    $rsSections = CIBlockSection::GetList($arSort, $arFilter, $arParams['COUNT_ELEMENTS'], $arSelect);
    $rsSections->SetUrlTemplates('', $arParams['SECTION_URL']);
    while ($arSection = $rsSections->GetNext()) {
        $ipropValues                   = new \Bitrix\Iblock\InheritedProperty\SectionValues($arSection['IBLOCK_ID'], $arSection['ID']);
        $arSection['IPROPERTY_VALUES'] = $ipropValues->getValues();

        if ($boolPicture) {
            $mxPicture            = false;
            $arSection['PICTURE'] = intval($arSection['PICTURE']);
            if (0 < $arSection['PICTURE']) {
                $mxPicture = CFile::GetFileArray($arSection['PICTURE']);
            }
            $arSection['PICTURE'] = $mxPicture;
            if ($arSection['PICTURE']) {
                $arSection['PICTURE']['ALT'] = $arSection['IPROPERTY_VALUES']['SECTION_PICTURE_FILE_ALT'];
                if ($arSection['PICTURE']['ALT'] == '') {
                    $arSection['PICTURE']['ALT'] = $arSection['NAME'];
                }
                $arSection['PICTURE']['TITLE'] = $arSection['IPROPERTY_VALUES']['SECTION_PICTURE_FILE_TITLE'];
                if ($arSection['PICTURE']['TITLE'] == '') {
                    $arSection['PICTURE']['TITLE'] = $arSection['NAME'];
                }
            }
        }
        $arSection['RELATIVE_DEPTH_LEVEL'] = $arSection['DEPTH_LEVEL'] - $intSectionDepth;

        $arButtons                = CIBlock::GetPanelButtons($arSection['IBLOCK_ID'], 0, $arSection['ID'], array(
            'SESSID'  => false,
            'CATALOG' => true
        ));
        $arSection['EDIT_LINK']   = $arButtons['edit']['edit_section']['ACTION_URL'];
        $arSection['DELETE_LINK'] = $arButtons['edit']['delete_section']['ACTION_URL'];

        $arResult['SECTIONS'][] = $arSection;
    }

    $arResult['SECTIONS_COUNT'] = count($arResult['SECTIONS']);

    $this->SetResultCacheKeys(array(
                                  'SECTIONS_COUNT',
                                  'SECTION',
                              ));

    $this->IncludeComponentTemplate();
}

if ($arResult['SECTIONS_COUNT'] > 0 || isset($arResult['SECTION'])) {
    if ($USER->IsAuthorized() && $APPLICATION->GetShowIncludeAreas() && \Bitrix\Main\Loader::includeModule('iblock')) {
        $UrlDeleteSectionButton = '';
        if (isset($arResult['SECTION']) && $arResult['SECTION']['IBLOCK_SECTION_ID'] > 0) {
            $rsSection = CIBlockSection::GetList(array(), array('=ID' => $arResult['SECTION']['IBLOCK_SECTION_ID']), false, array('SECTION_PAGE_URL'));
            $rsSection->SetUrlTemplates('', $arParams['SECTION_URL']);
            $arSection              = $rsSection->GetNext();
            $UrlDeleteSectionButton = $arSection['SECTION_PAGE_URL'];
        }

        if (empty($UrlDeleteSectionButton)) {
            $url_template            = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'LIST_PAGE_URL');
            $arIBlock                = CIBlock::GetArrayByID($arParams['IBLOCK_ID']);
            $arIBlock['IBLOCK_CODE'] = $arIBlock['CODE'];
            $UrlDeleteSectionButton  = CIBlock::ReplaceDetailURL($url_template, $arIBlock, true, false);
        }

        $arReturnUrl = array(
            'add_section'    => ('' != $arParams['SECTION_URL'] ? $arParams['SECTION_URL'] : CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'SECTION_PAGE_URL')),
            'add_element'    => ('' != $arParams['SECTION_URL'] ? $arParams['SECTION_URL'] : CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'SECTION_PAGE_URL')),
            'delete_section' => $UrlDeleteSectionButton,
        );
        $arButtons   = CIBlock::GetPanelButtons($arParams['IBLOCK_ID'], 0, $arResult['SECTION']['ID'], array(
            'RETURN_URL' => $arReturnUrl,
            'CATALOG'    => true
        ));

        $this->AddIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));
    }

    if ($arParams['ADD_SECTIONS_CHAIN'] && isset($arResult['SECTION']) && is_array($arResult['SECTION']['PATH'])) {
        foreach ($arResult['SECTION']['PATH'] as $arPath) {
            if (isset($arPath['IPROPERTY_VALUES']['SECTION_PAGE_TITLE']) && $arPath['IPROPERTY_VALUES']['SECTION_PAGE_TITLE'] != '') {
                $APPLICATION->AddChainItem($arPath['IPROPERTY_VALUES']['SECTION_PAGE_TITLE'], $arPath['~SECTION_PAGE_URL']);
            } else {
                $APPLICATION->AddChainItem($arPath['NAME'], $arPath['~SECTION_PAGE_URL']);
            }
        }
    }
}