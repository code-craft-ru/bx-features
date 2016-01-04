<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentParameters = ['PARAMETERS' => ['STRING_PARAMETER'   => ['PARENT'  => 'BASE',
                                                                    'NAME'    => Loc::getMessage('COMPONENT_NULL_STRING_PARAMETER'),
                                                                    'TYPE'    => 'STRING',
                                                                    'DEFAULT' => '',],
                                           'CHECKBOX_PARAMETER' => ['PARENT'  => 'BASE',
                                                                    'NAME'    => Loc::getMessage('COMPONENT_NULL_CHECKBOX_PARAMETER'),
                                                                    'TYPE'    => 'CHECKBOX',
                                                                    'DEFAULT' => 'Y',],
                                           'CACHE_TIME'         => ['DEFAULT' => 36000000],
                                           'AJAX_MODE'          => []

]];