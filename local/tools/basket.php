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

$mode = $_REQUEST['mode'];

Loader::includeModule('catalog');
Loader::includeModule('sale');

switch ($mode) {
    case 'add':
        $id       = (int)$_REQUEST['id'];
        $quantity = (int)$_REQUEST['quantity'] ?: 1;

        $properties = [];
        if ($_POST['prop']['color']) {
            $properties[] = ['NAME'  => 'Цвет',
                             'CODE'  => 'color',
                             'SORT'  => '10',
                             'VALUE' => $_POST['prop']['color']];
        }
        if ($_POST['prop']['size']) {
            $properties[] = ['NAME'  => 'Размер',
                             'CODE'  => 'size',
                             'SORT'  => '20',
                             'VALUE' => $_POST['prop']['size']];
        }

        $result = (bool)Add2BasketByProductID($id, $quantity, $properties);
        if (!$result) {
            break;
        }
    case 'reload':
        $APPLICATION->IncludeComponent('bitrix:sale.basket.basket.line', 'template.header.basket', Array('PATH_TO_BASKET'     => '/personal/cart/',
                                                                                                         'PATH_TO_PERSONAL'   => '/personal/',
                                                                                                         'SHOW_PERSONAL_LINK' => 'Y',
                                                                                                         'SHOW_NUM_PRODUCTS'  => 'Y',
                                                                                                         'SHOW_TOTAL_PRICE'   => 'Y',
                                                                                                         'SHOW_EMPTY_VALUES'  => 'Y',
                                                                                                         'SHOW_PRODUCTS'      => 'Y',
                                                                                                         'POSITION_FIXED'     => 'Y',
                                                                                                         'PATH_TO_ORDER'      => '/personal/order/',
                                                                                                         'SHOW_DELAY'         => 'N',
                                                                                                         'SHOW_NOTAVAIL'      => 'N',
                                                                                                         'SHOW_SUBSCRIBE'     => 'N',
                                                                                                         'SHOW_PRICE'         => 'Y',
                                                                                                         'SHOW_SUMMARY'       => 'Y'));
        die;
        break;
    case 'recalculate':
        $id       = (int)$_REQUEST['id'];
        $quantity = (int)$_REQUEST['quantity'] ?: 1;

        $result = CSaleBasket::Update($id, ['QUANTITY' => $quantity]);
        break;
    case 'delete':
        $id = (int)$_REQUEST['id'];

        $result = CSaleBasket::Delete($id);
        break;
}

echo \Bitrix\Main\Web\Json::encode(['result' => $result]);