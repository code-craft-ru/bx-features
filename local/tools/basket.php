<?

if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
    return;
}

define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC', 'Y');
define('PUBLIC_AJAX_MODE', true);
define('NOT_CHECK_PERMISSIONS', true);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Loader;
use CodeCraft\UserBasket;

$result     = null;
$mode       = $_REQUEST['mode'];
$userBasket = UserBasket::getInstance();

Loader::includeModule('catalog');
Loader::includeModule('sale');

switch ($mode) {
    case 'add':
        $id       = (int)$_REQUEST['id'];
        $quantity = (int)$_REQUEST['quantity'] ?: 1;

        $properties = [];

        if ($_REQUEST['prop']['color']) {
            $properties[] = ['NAME'  => 'Цвет',
                             'CODE'  => 'color',
                             'SORT'  => '10',
                             'VALUE' => $_REQUEST['prop']['color']];
        }

        if ($_REQUEST['prop']['size']) {
            $properties[] = ['NAME'  => 'Размер',
                             'CODE'  => 'size',
                             'SORT'  => '20',
                             'VALUE' => $_REQUEST['prop']['size']];
        }

        if ($_REQUEST['favorite'] == 'y') {
            $properties[] = ['NAME'  => 'В избранное',
                             'CODE'  => 'favorite',
                             'SORT'  => '1',
                             'VALUE' => 'y'];
        }

        $result = $_REQUEST['favorite'] == 'y'
            ? (bool)Add2BasketByProductID($id, $quantity, ['DELAY' => 'Y'], $properties)
            : (bool)Add2BasketByProductID($id, $quantity, $properties);

        if (!$result) {
            break;
        }
    case 'reload':
        $APPLICATION->IncludeComponent("codecraft:sale.basket.line", ".default", array("COMPONENT_TEMPLATE" => ".default",
                                                                                       "RECALCULATE_BASKET" => "Y",
                                                                                       "PATH_TO_BASKET"     => "/personal/cart/",
                                                                                       "PATH_TO_FAVORITE"   => "/personal/favorite/"), false);
        die;
        break;
    case 'recalculate':
        $id       = (int)$_REQUEST['id'];
        $quantity = (int)$_REQUEST['quantity'] ?: 1;

        $result = CSaleBasket::Update($id, ['QUANTITY' => $quantity]);
        break;
    case 'delete':
        $id = $_REQUEST['favorite'] == 'y' ? $userBasket->getFavoriteId((int)$_REQUEST['productId'])
            : (int)$_REQUEST['id'];

        $result = CSaleBasket::Delete($id);
        break;
}

echo \Bitrix\Main\Web\Json::encode(['result' => $result]);