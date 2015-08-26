<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

$arComponentDescription = ['NAME'        => Loc::getMessage('CC_ISL_COMPONENT_NAME'),
                           'DESCRIPTION' => Loc::getMessage('CC_ISL_COMPONENT_DESCRIPTION'),
                           'ICON'        => '/images/icon.gif',
                           'COMPLEX'     => 'N',
                           'PATH'        => ['ID' => 'utility',],];