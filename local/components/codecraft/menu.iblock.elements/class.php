<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Loader, \Bitrix\Main\Localization\Loc, \Bitrix\Main\SystemException;

class CCodeCraftMenuIblockElementsComponent extends \CBitrixComponent {

    protected $requiredModules = ['iblock'];

    protected function checkModules() {
        foreach ($this->requiredModules as $moduleName) {
            if (!Loader::includeModule($moduleName)) {
                throw new SystemException(Loc::getMessage('COMPONENT_CODECRAFT_MENU_IBLOCK_ELEMENTS_NO_MODULE', [
                    '#MODULE#',
                    $moduleName
                ]));
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
        $params['IBLOCK_ID']         = (intval($params['IBLOCK_ID']) >= 0) ? $params['IBLOCK_ID'] : 0;
        $params['INCLUDE_ELEMENTS']  = ($params['INCLUDE_ELEMENTS'] == 'Y') ? true : false;
        $params['CALLBACK_SECTIONS'] = (is_callable($params['CALLBACK_SECTIONS'])) ? $params['CALLBACK_SECTIONS'] : null;
        $params['CALLBACK_ELEMENTS'] = (is_callable($params['CALLBACK_ELEMENTS'])) ? $params['CALLBACK_ELEMENTS'] : null;

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

        $order           = [
            'SORT' => 'ASC',
            'NAME' => 'ASC',
        ];
        $filter          = [
            'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
        ];
        $groupBy         = false;
        $navStringParams = false;

        $sectionsCollection = CIBlockSection::GetList($order, $filter, false, [], $navStringParams);
        $sectionsCollection->SetUrlTemplates(); // @TODO: Implement custom URL templates
        while ($section = $sectionsCollection->GetNext()) {
            $section['SECTIONS']                        = [];
            $section['ITEMS']                           = [];
            $this->arResult['SECTIONS'][$section['ID']] = $section;
        }

        if ($this->arParams['INCLUDE_ELEMENTS']) {
            $elementsCollection = CIBlockElement::GetList($order, $filter, $groupBy, $navStringParams);
            $elementsCollection->SetUrlTemplates(); // @TODO: Implement custom URL templates
            while ($elementEntity = $elementsCollection->GetNextElement(true, true)) {
                $element               = $elementEntity->GetFields();
                $element['PROPERTIES'] = $elementEntity->GetProperties();

                if ($element['IBLOCK_SECTION_ID'] > 0) {
                    $this->arResult['SECTIONS'][$element['IBLOCK_SECTION_ID']]['ITEMS'][] = $element;
                } else {
                    $this->arResult['ITEMS'][] = $element;
                }
            }
        }

        $unsetIds = [];
        foreach ($this->arResult['SECTIONS'] as $id => &$section) {
            if ($section['IBLOCK_SECTION_ID'] > 0) {
                $this->arResult['SECTIONS'][$section['IBLOCK_SECTION_ID']]['SECTIONS'][] = $section;
                $unsetIds[]                                                              = $id;
            }
        }

        foreach ($unsetIds as $id) {
            unset($this->arResult['SECTIONS'][$id]);
        }

        return $this;
    }

    public function executeComponent() {
        global $APPLICATION;

        try {
            $this->checkModules()->prepareResult();

            //$this->includeComponentTemplate();
            return $this->buildMenuItems();
        } catch (SystemException $e) {
            self::__showError($e->getMessage());
        }

        return false;
    }

    protected function buildMenuItems() {
        $menu = [];

        $menu = array_merge($menu, $this->buildSection($this->arResult['SECTIONS']));
        if ($this->arParams['INCLUDE_ELEMENTS']) {
            $menu = array_merge($menu, $this->buildElements($this->arResult['ITEMS']));
        }

        return $menu;
    }

    protected function buildSection($sections) {
        $menu = [];

        $callbackSections = $this->arParams['CALLBACK_SECTIONS'];

        foreach ($sections as $section) {

            $item = array(
                htmlspecialcharsbx($section["~NAME"]),
                $section["~SECTION_PAGE_URL"],
                array(),
                array(
                    "FROM_IBLOCK" => true,
                    "IS_PARENT"   => (!empty($section['ITEMS']) && $this->arParams['INCLUDE_ELEMENTS']) || !empty($section['SECTIONS']),
                    "DEPTH_LEVEL" => $section['DEPTH_LEVEL'],
                ),
            );

            if ($callbackSections) { // @TODO: Implement bitrix event support
                call_user_func_array($callbackSections, [&$section, &$item]);
            }

            $menu[] = $item;

            $sectionsItems = [];
            if (!empty($section['SECTIONS'])) {
                $sectionsItems = $this->buildSection($section['SECTIONS']);
            }

            $menu = array_merge($menu, $sectionsItems);

            $elementsItems = [];
            if (!empty($section['ITEMS'])) {
                $elementsItems = $this->buildElements($section['ITEMS'], $section['DEPTH_LEVEL'] + 1);
            }

            $menu = array_merge($menu, $elementsItems);
        }

        return $menu;
    }

    protected function buildElements($elements, $level = 1) {
        if (!$this->arParams['INCLUDE_ELEMENTS']) {
            return [];
        }

        $menu = [];

        $callbackElements = $this->arParams['CALLBACK_ELEMENTS'];

        foreach ($elements as $element) {
            $item = array(
                htmlspecialcharsbx($element["~NAME"]),
                $element["~DETAIL_PAGE_URL"],
                array(),
                array(
                    "FROM_IBLOCK" => true,
                    "IS_PARENT"   => false,
                    "DEPTH_LEVEL" => $level,
                ),
            );

            if ($callbackElements) { // @TODO: Implement bitrix event support
                call_user_func_array($callbackElements, [&$element, &$item]);
            }

            $menu[] = $item;
        }

        return $menu;
    }
}