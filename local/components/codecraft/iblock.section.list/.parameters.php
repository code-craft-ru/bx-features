<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var array               $arCurrentValues
 * @global CUserTypeManager $USER_FIELD_MANAGER
 */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

Loc::loadLanguageFile(__FILE__);

global $USER_FIELD_MANAGER;

if (!Loader::includeModule('iblock')) {
    return;
}

$iBlockTypeList = CIBlockParameters::GetIBlockTypes();

$iBlockCollection = CIBlock::GetList(['sort' => 'asc'], ['TYPE'   => $arCurrentValues['IBLOCK_TYPE'],
                                                         'ACTIVE' => 'Y']);

while ($arr = $iBlockCollection->Fetch()) {
    $iBlockList[$arr['ID']] = '[' . $arr['ID'] . '] ' . $arr['NAME'];
}

$userFieldPropertyList = [];
$userFieldList         = $USER_FIELD_MANAGER->GetUserFields('IBLOCK_' . $arCurrentValues['IBLOCK_ID'] . '_SECTION');
foreach ($userFieldList as $FIELD_NAME => $userField) {
    $userFieldPropertyList[$FIELD_NAME] = $userField['LIST_COLUMN_LABEL'] ? $userField['LIST_COLUMN_LABEL']
        : $FIELD_NAME;
}

$arComponentParameters = ['GROUPS'     => [],
                          'PARAMETERS' => ['IBLOCK_TYPE'         => ['PARENT'  => 'BASE',
                                                                     'NAME'    => Loc::getMessage('CC_IBSL_IBLOCK_TYPE'),
                                                                     'TYPE'    => 'LIST',
                                                                     'VALUES'  => $iBlockTypeList,
                                                                     'REFRESH' => 'Y',],
                                           'IBLOCK_ID'           => ['PARENT'            => 'BASE',
                                                                     'NAME'              => Loc::getMessage('CC_IBSL_IBLOCK_ID'),
                                                                     'TYPE'              => 'LIST',
                                                                     'ADDITIONAL_VALUES' => 'Y',
                                                                     'VALUES'            => $iBlockList,
                                                                     'REFRESH'           => 'Y',],
                                           'SECTION_ID'          => ['PARENT'  => 'BASE',
                                                                     'NAME'    => Loc::getMessage('CC_IBSL_SECTION_ID'),
                                                                     'TYPE'    => 'STRING',
                                                                     'DEFAULT' => '={$_REQUEST["SECTION_ID"]}',],
                                           'SECTION_CODE'        => ['PARENT'  => 'BASE',
                                                                     'NAME'    => Loc::getMessage('CC_IBSL_SECTION_CODE'),
                                                                     'TYPE'    => 'STRING',
                                                                     'DEFAULT' => '',],
                                           'FILTER_NAME'         => ['PARENT'  => 'DATA_SOURCE',
                                                                     'NAME'    => Loc::getMessage('CC_IBSL_FILTER_NAME'),
                                                                     'TYPE'    => 'STRING',
                                                                     'DEFAULT' => '',],
                                           'SECTION_URL'         => CIBlockParameters::GetPathTemplateParam('SECTION', 'SECTION_URL', Loc::getMessage('CC_IBSL_SECTION_URL'), '', 'URL_TEMPLATES'),
                                           'COUNT_ELEMENTS'      => ['PARENT'  => 'DATA_SOURCE',
                                                                     'NAME'    => Loc::getMessage('CC_IBSL_COUNT_ELEMENTS'),
                                                                     'TYPE'    => 'CHECKBOX',
                                                                     'DEFAULT' => 'Y',],
                                           'TOP_DEPTH'           => ['PARENT'  => 'DATA_SOURCE',
                                                                     'NAME'    => Loc::getMessage('CC_IBSL_TOP_DEPTH'),
                                                                     'TYPE'    => 'STRING',
                                                                     'DEFAULT' => '2',],
                                           'SECTION_FIELDS'      => CIBlockParameters::GetSectionFieldCode(Loc::getMessage('CC_IBSL_SECTION_FIELDS'), 'DATA_SOURCE', []),
                                           'SECTION_USER_FIELDS' => ['PARENT'            => 'DATA_SOURCE',
                                                                     'NAME'              => Loc::getMessage('CC_IBSL_SECTION_USER_FIELDS'),
                                                                     'TYPE'              => 'LIST',
                                                                     'MULTIPLE'          => 'Y',
                                                                     'ADDITIONAL_VALUES' => 'Y',
                                                                     'VALUES'            => $userFieldPropertyList,],
                                           'ADD_SECTIONS_CHAIN'  => ['PARENT'  => 'ADDITIONAL_SETTINGS',
                                                                     'NAME'    => Loc::getMessage('CC_IBSL_ADD_SECTIONS_CHAIN'),
                                                                     'TYPE'    => 'CHECKBOX',
                                                                     'DEFAULT' => 'Y',],
                                           'CACHE_TIME'          => ['DEFAULT' => 36000000],
                                           'CACHE_GROUPS'        => ['PARENT'  => 'CACHE_SETTINGS',
                                                                     'NAME'    => Loc::getMessage('CC_IBSL_CACHE_GROUPS'),
                                                                     'TYPE'    => 'CHECKBOX',
                                                                     'DEFAULT' => 'Y',],],];