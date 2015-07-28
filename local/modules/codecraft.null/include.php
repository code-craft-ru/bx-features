<?php
/**
 * Created by PhpStorm.
 * Company: Code Craft
 * User: Manriel
 * Date: 31.03.2015
 * Time: 22:03
 */

global $DBType;

$arClasses = array(
    'CodeCraft\\Null\\Simple' => 'classes/general/simple.php',
);

\Bitrix\Main\Loader::registerAutoLoadClasses("codecraft.null", $arClasses);
