<?

namespace CodeCraft;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;

/**
 * Class UserBasket
 *
 * Class for simple work with basket and favorite in Bitrix; gor simple watching it state.
 *
 * @todo add static methods for get position/product count in basket/favorite with query getUserBasket (with group by)
 *
 * @author    Dmitry Panychev <panychev@code-craft.ru>
 * @version   1.0
 * @package   CodeCraft
 * @category  Bitrix, eCommerce
 * @copyright Copyright © 2015, Dmitry Panychev
 **/

class UserBasket
{
    private static $instance;

    private $basketItemList        = [];
    private $favoriteItemList      = [];
    private $basketProductIdList   = [];
    private $favoriteProductIdList = [];

    /**
     * UserBasket constructor.
     *
     * @throws LoaderException
     */
    final private function __construct() {
        static::checkModules();

        $this->actualizeUserBasket();
        $this->setUserBasket();
    }

    private function __clone() {
    }

    /**
     * @throws LoaderException
     */
    private static function checkModules() {
        if (!Loader::includeModule('sale')) {
            throw new LoaderException('Module sale must be installed');
        }
    }

    /**
     * @return UserBasket
     */
    final public static function getInstance() {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Get user basket from database.
     * Supported additional filter, order id, group.
     *
     * @param int   $userId
     * @param int   $orderId
     * @param array $filter - additional filter for basket
     * @param array $group
     * @param array $select
     *
     * @return array
     *
     * @throws LoaderException
     */
    public static function getUserBasket($userId = 0, $orderId = 0, array $filter = [], array $group = [], array $select = []) {
        static::checkModules();

        $basketList = [];
        $orderId    = (int)$orderId ?: false;
        $userId     = (int)$userId ?: 'NULL';
        $filter     = array_merge(['FUSER_ID' => \CSaleBasket::GetBasketUserID($userId),
                                   'LID'      => SITE_ID,
                                   'ORDER_ID' => $orderId], $filter);
        $group      = $group ?: false;

        $basketCollection = \CSaleBasket::GetList(['ID' => 'ASC'], $filter, $group, false, $select);

        if ($group) {
            $result = $basketCollection;
        } else {
            while ($basket = $basketCollection->Fetch()) {
                $basketList[] = $basket;
            }

            $result = $basketList;
        }

        return $result;
    }

    /**
     * Actualize price (and etc) in basket
     *
     * @throws LoaderException
     */
    public function actualizeUserBasket() {
        $basketList = static::getUserBasket();

        foreach ($basketList as $basket) {
            if ($basket['CAN_BUY'] != 'Y') {
                \CSaleBasket::Delete($basket['ID']);
                continue;
            }

            if (!($basket['PRODUCT_PROVIDER_CLASS'] || $basket['CALLBACK_FUNC'])) {
                continue;
            }

            \CSaleBasket::UpdatePrice($basket['ID'], $basket['CALLBACK_FUNC'], $basket['MODULE'], $basket['PRODUCT_ID'], $basket['QUANTITY'], 'N', $basket['PRODUCT_PROVIDER_CLASS']);
        }
    }

    /**
     * Recalculate user basket
     */
    public function recalculate() {
        $this->setUserBasket();
    }

    /**
     * Set current user actual basket
     */
    private function setUserBasket() {
        $basketItemList = $favoriteItemList = $basketProductIdList = $favoriteProductIdList = [];
        $basketList     = $this->getUserBasket();

        foreach ($basketList as $basket) {
            if ($basket['DELAY'] == 'Y') {
                $favoriteItemList[]                           = $basket;
                $favoriteProductIdList[$basket['PRODUCT_ID']] = $favoriteProductIdList[$basket['PRODUCT_ID']]
                    ? $favoriteProductIdList[$basket['PRODUCT_ID']] + $basket['QUANTITY'] : $basket['QUANTITY'];
                continue;
            }

            $basketItemList[]                           = $basket;
            $basketProductIdList[$basket['PRODUCT_ID']] = $basketProductIdList[$basket['PRODUCT_ID']]
                ? $basketProductIdList[$basket['PRODUCT_ID']] + $basket['QUANTITY'] : $basket['QUANTITY'];
        }

        $this->basketItemList        = $basketItemList;
        $this->favoriteItemList      = $favoriteItemList;
        $this->basketProductIdList   = $basketProductIdList;
        $this->favoriteProductIdList = $favoriteProductIdList;
    }

    /**
     * @return array
     */
    public function getBasketProductIdList() {
        return $this->basketProductIdList;
    }

    /**
     * @return array
     */
    public function getFavoriteProductIdList() {
        return $this->favoriteProductIdList;
    }

    /**
     * @param int $productId
     *
     * @return bool
     */
    public function inBasket($productId) {
        $basketProductIdList = $this->getBasketProductIdList();

        return $basketProductIdList[$productId] > 0;
    }

    /**
     * @param int $productId
     *
     * @return bool
     */
    public function inFavorite($productId) {
        $favoriteProductIdList = $this->getFavoriteProductIdList();

        return $favoriteProductIdList[$productId] > 0;
    }

    /**
     * @param int $productId
     *
     * @return int
     */
    public function getBasketId($productId) {
        $basketId = 0;

        foreach ($this->basketItemList as $basket) {
            if ($productId == $basket['PRODUCT_ID']) {
                $basketId = (int)$basket['ID'];
            }
        }

        return $basketId;
    }

    /**
     * @param int $productId
     *
     * @return int
     */
    public function getFavoriteId($productId) {
        $favoriteId = 0;

        foreach ($this->favoriteItemList as $favorite) {
            if ($productId == $favorite['PRODUCT_ID']) {
                $favoriteId = (int)$favorite['ID'];
            }
        }

        return $favoriteId;
    }

    /**
     * @return int
     */
    public function getBasketPositionCount() {
        return count($this->basketItemList);
    }

    /**
     * @return int
     */
    public function getFavoritePositionCount() {
        return count($this->favoriteItemList);
    }
}