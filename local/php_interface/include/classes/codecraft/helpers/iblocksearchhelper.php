<?

namespace CodeCraft\Helpers;

use \CodeCraft\MultipleLoader;

/**
 * Class IBlockSearchHelper
 *
 * @todo write class description
 *
 * @author    Dmitry Panychev <panychev@code-craft.ru>
 * @version   1.0
 * @package   CodeCraft
 * @category  Bitrix, search
 * @copyright Copyright © 2015, Dmitry Panychev
 */
class IBlockSearchHelper
{
    const MODULE_ID         = 'iblock';
    const IBLOCK_TYPE_PARAM = 'PARAM1';
    const IBLOCK_ID_PARAM   = 'PARAM2';

    private static $moduleList = ['iblock',
                                  'search'];

    private $searchResult        = [];
    private $iBlockElementList   = [];
    private $iBlockElementIdList = [];
    private $error               = '';

    /**
     * @param string $query
     * @param string $iBlockType
     * @param int    $iBlockId
     *
     * @return bool
     */
    public function search($query, $iBlockType = '', $iBlockId = 0) {
        $search = new \CSearch();

        $params = ['SITE_ID'   => SITE_ID,
                   'MODULE_ID' => static::MODULE_ID,
                   'QUERY'     => $query];

        if ($iBlockType) {
            $params[self::IBLOCK_TYPE_PARAM] = $iBlockType;
        }

        if ($iBlockId) {
            $params[self::IBLOCK_ID_PARAM] = $iBlockId;
        }

        $search->Search($params);

        if ($result = (bool)$search->error) {
            $this->error = $search->error;
        } else {
            while ($item = $search->Fetch()) {
                $this->searchResult[]        = $item;
                $this->iBlockElementIdList[] = $item['ITEM_ID'];
            }
        }

        return $result;
    }

    /**
     * Set iBlock element list by founded id
     */
    public function setIBlockElementList() {
        if (!($iBlockElementIdList = $this->getIBlockElementIdList())) {
            return;
        }

        $elementCollection = \CIBlockElement::GetList([], ['ID' => $iBlockElementIdList]);

        while ($element = $elementCollection->GetNext()) {
            $this->iBlockElementList[] = $element;
        }
    }

    /**
     * @throws \Bitrix\Main\LoaderException
     */
    public function checkModules() {
        MultipleLoader::loadModuleList(static::$moduleList);
    }

    /**
     * IBlockSearchHelper constructor.
     *
     * @throws \Bitrix\Main\LoaderException
     */
    public function __construct() {
        self::checkModules();
    }

    /**
     * @return array
     */
    public function getNormalSearchResult() {
        return $this->searchResult;
    }

    /**
     * @return array
     */
    public function getIBlockElementIdList() {
        return $this->iBlockElementIdList;
    }

    /**
     * @return array
     */
    public function getIBlockElementList() {
        if (!$this->iBlockElementList) {
            $this->setIBlockElementList();
        }

        return $this->iBlockElementList;
    }

    /**
     * @return string
     */
    public function getError() {
        return $this->error;
    }
}