<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Loader,
    \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\SystemException;

class CCodeCraftNullComponent extends \CBitrixComponent {

    protected $requiredModules = [];

    protected function isAjax() {
        return isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == 'y';
    }

    protected function checkModules() {
        foreach ($this->requiredModules as $moduleName) {
            if (!Loader::includeModule($moduleName)) {
                throw new SystemException(Loc::getMessage('COMPONENT_NULL_NO_MODULE', ['#MODULE#', $moduleName]));
            }
        }

        return $this;
    }

    /**
     * Event called from includeComponent before component execution.
     * Takes component parameters as argument and should return it formatted as needed.
     *
     * @param  array[string]mixed $arParams
     * @return array[string]mixed
     */
    public static function onPrepareComponentParams($params) {
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

    protected function prepareResult() {
        $this->arResult['TEST_STRING'] = 'Test string';

        return $this;
    }

    public function executeComponent() {
        global $APPLICATION;

        try {
            $this->checkModules()->prepareResult();

            if ($this->isAjax()) {
                $APPLICATION->restartBuffer();
                echo json_encode([
                                     'status' => 'ok',
                                     'data'   => $this->arResult['TEST_STRING']
                                 ], JSON_FORCE_OBJECT);
                die();
            }

            $this->includeComponentTemplate();
        } catch (SystemException $e) {

            if ($this->isAjax()) {
                $APPLICATION->restartBuffer();
                echo json_encode([
                                     'status' => 'error',
                                     'data'   => $e->getMessage()
                                 ], JSON_FORCE_OBJECT);
                die();
            }

            self::__showError($e->getMessage());
        }
    }
}