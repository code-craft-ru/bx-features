<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;

class CCodeCraftIBlockSectionList extends CBitrixComponent
{
    private $paramList = array();

    /**
     * @param &$paramList
     */
    public function prepareParams(&$paramList) {

        $paramList['CACHE_TIME'] = (int)$paramList['CACHE_TIME'] ?: 36000000;
        $paramList['TOP_DEPTH']  = (int)$paramList['TOP_DEPTH'] ?: 2;

        $paramList['IBLOCK_ID']  = (int)$paramList['IBLOCK_ID'];
        $paramList['SECTION_ID'] = (int)$paramList['SECTION_ID'];

        $paramList['IBLOCK_TYPE']  = trim($paramList['IBLOCK_TYPE']);
        $paramList['SECTION_CODE'] = trim($paramList['SECTION_CODE']);
        $paramList['FILTER_NAME']  = trim($paramList['FILTER_NAME']);
        $paramList['SECTION_URL']  = trim($paramList['SECTION_URL']);

        $paramList['COUNT_ELEMENTS']     = $paramList['COUNT_ELEMENTS'] != 'N';
        $paramList['ADD_SECTIONS_CHAIN'] = $paramList['ADD_SECTIONS_CHAIN'] != 'N';
        $paramList['CACHE_GROUPS']       = $paramList['ADD_SECTIONS_CHAIN'] != 'N';

        $this->paramList = $paramList;
    }

    /**
     * @return bool
     */
    public function checkComponent() {
        if (!Loader::includeModule('iblock')) {
            $this->AbortResultCache();
            ShowError(GetMessage('IBLOCK_MODULE_NOT_INSTALLED'));

            return false;
        }

        return true;
    }

    /**
     * @param string $paramName
     *
     * @return bool
     */
    public function checkArrayParam($paramName) {
        return array_key_exists($paramName, $this->paramList) && !empty($this->paramList[$paramName])
               && is_array($this->paramList[$paramName]);
    }

    /**
     * @return array
     */
    public function prepareFilter() {
        $filter = array('ACTIVE'        => 'Y',
                        'GLOBAL_ACTIVE' => 'Y',
                        'IBLOCK_ID'     => $this->paramList['IBLOCK_ID'],
                        'CNT_ACTIVE'    => 'Y',);

        if ($this->paramList['FILTER_NAME']) {
            global ${$this->paramList['FILTER_NAME']};
            $additionalFilter = ${$this->paramList['FILTER_NAME']} && is_array(${$this->paramList['FILTER_NAME']})
                ? ${$this->paramList['FILTER_NAME']} : array();
        } else {
            $additionalFilter = array();
        }

        return array_merge($filter, $additionalFilter);
    }

    public function prepareSelect() {
        $select = array('ID',
                        'NAME',
                        'LEFT_MARGIN',
                        'RIGHT_MARGIN',
                        'DEPTH_LEVEL',
                        'IBLOCK_ID',
                        'IBLOCK_SECTION_ID',
                        'LIST_PAGE_URL',
                        'SECTION_PAGE_URL',);
        if ($this->checkArrayParam('SECTION_FIELDS')) {
            foreach ($this->paramList['SECTION_FIELDS'] as &$field) {
                if (!empty($field) && is_string($field)) {
                    $select[] = $field;
                }
            }
            if (isset($field)) {
                unset($field);
            }
        }

        if ($this->checkArrayParam('SECTION_USER_FIELDS')) {
            foreach ($this->paramList['SECTION_USER_FIELDS'] as &$field) {
                if (is_string($field) && preg_match('/^UF_/', $field)) {
                    $select[] = $field;
                }
            }
            if (isset($field)) {
                unset($field);
            }
        }

        return $select;
    }
}
