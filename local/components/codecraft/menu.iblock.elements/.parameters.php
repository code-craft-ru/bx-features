<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

Loc::loadLanguageFile(__FILE__);

\Bitrix\Main\Loader::includeModule('iblock');

$types = CIBlockParameters::GetIBlockTypes(array('-' => ' '));

$iblocksList       = array();
$iblocksCollection = CIBlock::GetList(array('SORT' => 'ASC'), array(
    'SITE_ID' => $_REQUEST['site'],
    'TYPE'    => ($arCurrentValues['IBLOCK_TYPE'] != '-' ? $arCurrentValues['IBLOCK_TYPE'] : '')
));
while ($iblock = $iblocksCollection->Fetch())
    $iblocksList[$iblock['ID']] = $iblock['NAME'];

$arComponentParameters = [
    'GROUPS'     => array(),
    'PARAMETERS' => array(
        'IBLOCK_TYPE'       => array(
            'PARENT'  => 'BASE',
            'NAME'    => Loc::getMessage('COMPONENT_CODECRAFT_MENU_IBLOCK_ELEMENTS_PARAMS_IBLOCK_LIST_TYPE'),
            'TYPE'    => 'LIST',
            'VALUES'  => $types,
            'DEFAULT' => 'news',
            'REFRESH' => 'Y',
        ),
        'IBLOCK_ID'         => array(
            'PARENT'            => 'BASE',
            'NAME'              => Loc::getMessage('COMPONENT_CODECRAFT_MENU_IBLOCK_ELEMENTS_PARAMS_IBLOCK_DESC_LIST_ID'),
            'TYPE'              => 'LIST',
            'VALUES'            => $iblocksList,
            'DEFAULT'           => "={$_REQUEST['ID']}",
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH'           => 'Y',
        ),
        'INCLUDE_ELEMENTS'  => array(
            'NAME' => Loc::getMessage('COMPONENT_CODECRAFT_MENU_IBLOCK_ELEMENTS_PARAMS_IBLOCK_INCLUDE_ELEMENTS'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
        ),
        'CALLBACK_SECTIONS' => array( // @TODO: Implement bitrix event support
            'NAME'    => 'CALLBACK_SECTIONS',
            'TYPE'    => 'STRING',
            'DEFAULT' => '',
        ),
        'CALLBACK_ELEMENTS' => array( // @TODO: Implement bitrix event support
            'NAME'    => 'CALLBACK_ELEMENTS',
            'TYPE'    => 'STRING',
            'DEFAULT' => '',
        ),
        /**
         * @TODO: Implement custom URL templates
         */
    )
];