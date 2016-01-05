<?

namespace CodeCraft\Helpers;

use \CodeCraft\MultipleLoader;

/**
 * Class IBlockSearchHelper
 *
 * Helper to search in iBlocks. Use to custom ajax search, to simple get iBlock element ids with search by
 * iBlocks/iBlocks types, etc
 *
 * @author    Dmitry Panychev <panychev@code-craft.ru>
 * @version   1.0
 * @package   CodeCraft
 * @category  Bitrix, search
 * @copyright Copyright © 2015, Dmitry Panychev
 */
class IBlockSearchHelper
{
    const MODULE_ID             = 'iblock';
    const IBLOCK_TYPE_PARAM     = 'PARAM1';
    const IBLOCK_ID_PARAM       = 'PARAM2';
    const TITLE_MIN_WORD_LENGTH = 3;

    const SEARCH_MODE_SEARCH       = 0;
    const SEARCH_MODE_SEARCH_TITLE = 1;

    private static $moduleList = ['iblock',
                                  'search'];
    private        $searchMode;

    private $searchResult        = [];
    private $iBlockElementList   = [];
    private $iBlockElementIdList = [];
    private $error               = '';

    /**
     * IBlockSearchHelper constructor.
     *
     * @param int $searchMode
     *
     * @throws \Bitrix\Main\LoaderException
     */
    public function __construct($searchMode = self::SEARCH_MODE_SEARCH) {
        $this->setSearchMode($searchMode);
        self::checkModules();
    }

    /**
     * @throws \Bitrix\Main\LoaderException
     */
    public function checkModules() {
        MultipleLoader::loadModuleList(static::$moduleList);
    }

    /**
     * @param int $mode
     */
    public function setSearchMode($mode) {
        $this->searchMode = $mode;
    }

    /**
     * @param string $query
     * @param string $iBlockType
     * @param int    $iBlockId
     * @param int    $count
     * @param int    $page
     *
     * @return bool
     */
    public function search($query, $iBlockType = '', $iBlockId = 0, $count = 10, $page = 0) {
        $params = ['SITE_ID'   => SITE_ID,
                   'MODULE_ID' => static::MODULE_ID,
                   'QUERY'     => $query];

        if ($iBlockType) {
            $params[self::IBLOCK_TYPE_PARAM] = $iBlockType;
        }

        if ($iBlockId) {
            $params[self::IBLOCK_ID_PARAM] = $iBlockId;
        }

        if ($this->searchMode == self::SEARCH_MODE_SEARCH_TITLE) {
            $search = new \CSearchTitle();
            $search->setMinWordLength(self::TITLE_MIN_WORD_LENGTH);
            $search->Search($query, $count, $params);
        } else {
            $search = new \CSearch();
            $search->Search($params);
            $search->NavStart($count, false, $page);
        }

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