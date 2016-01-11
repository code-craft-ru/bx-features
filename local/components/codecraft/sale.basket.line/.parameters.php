<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentParameters = ['PARAMETERS' => ['PATH_TO_BASKET'     => ['PARENT'  => 'BASE',
                                                                    'NAME'    => Loc::getMessage('CP_CC_SBL_PATH_TO_BASKET'),
                                                                    'TYPE'    => 'STRING',
                                                                    'DEFAULT' => '',],
                                           'PATH_TO_FAVORITE'   => ['PARENT'  => 'BASE',
                                                                    'NAME'    => Loc::getMessage('CP_CC_SBL_PATH_TO_FAVORITE'),
                                                                    'TYPE'    => 'STRING',
                                                                    'DEFAULT' => '',],
                                           'RECALCULATE_BASKET' => ['PARENT'  => 'BASE',
                                                                    'NAME'    => Loc::getMessage('CP_CC_SBL_RECALCULATE'),
                                                                    'TYPE'    => 'CHECKBOX',
                                                                    'DEFAULT' => 'N',]]];