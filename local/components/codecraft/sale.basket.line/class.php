<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Loader, \Bitrix\Main\Localization\Loc, \Bitrix\Main\SystemException, \CodeCraft\UserBasket;

class CCodeCraftSaleBasketLineComponent extends \CBitrixComponent
{

    protected $requiredModules = ['sale'];

    protected function isAjax() {
        return isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == 'y';
    }

    protected function checkModules() {
        foreach ($this->requiredModules as $moduleName) {
            if (!Loader::includeModule($moduleName)) {
                throw new SystemException(Loc::getMessage('COMPONENT_CC_SBL_NO_MODULE', ['#MODULE#',
                                                                                         $moduleName]));
            }
        }

        return $this;
    }

    /**
     * Event called from includeComponent before component execution.
     * Takes component parameters as argument and should return it formatted as needed.
     *
     * @param  array [string]mixed $arParams
     *
     * @return array[string]mixed
     */
    public function onPrepareComponentParams($params) {
        return $params;
    }

    /**
     * Event called from includeComponent before component execution.
     * Includes component.php from within lang directory of the component.
     *
     * @return void
     */
    public function onIncludeComponentLang() {
        $this->includeComponentLang(basename(__FILE__));
        Loc::loadMessages(__FILE__);
    }

    /**
     * Prepare arResult
     *
     * @return $this
     */
    protected function prepareResult() {
        $userBasket = UserBasket::getInstance();

        if($this->arParams['RECALCULATE_BASKET'] == 'Y') {
            $userBasket->recalculate();
        }

        $this->arResult['BASKET_POSITION_COUNT']   = $userBasket->getBasketPositionCount();
        $this->arResult['FAVORITE_POSITION_COUNT'] = $userBasket->getFavoritePositionCount();

        return $this;
    }

    public function executeComponent() {
        global $APPLICATION;

        try {
            $this->checkModules()->prepareResult();

            if ($this->isAjax()) {
                $APPLICATION->restartBuffer();
                echo json_encode(['status' => 'ok',
                                  'data'   => $this->arResult['TEST_STRING']], JSON_FORCE_OBJECT);
                die();
            }

            $this->includeComponentTemplate();
        } catch (SystemException $e) {

            if ($this->isAjax()) {
                $APPLICATION->restartBuffer();
                echo json_encode(['status' => 'error',
                                  'data'   => $e->getMessage()], JSON_FORCE_OBJECT);
                die();
            }

            self::__showError($e->getMessage());
        }
    }
}