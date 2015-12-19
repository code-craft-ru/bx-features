<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use CodeCraft\Menu\MenuSimple;

if (!empty($arResult)) {
    $menu = new MenuSimple($arResult, $arParams);
    $menu->drawMenu();
}
