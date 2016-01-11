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
 * @copyright Copyright © 2016, Dmitry Panychev
 */
class IBlockSearchHelper
{
    const MODULE_ID             = 'iblock';
    const CACHE_PATH            = '/search_helper/';
    const IBLOCK_TYPE_PARAM     = 'PARAM1';
    const IBLOCK_ID_PARAM       = 'PARAM2';
    const TITLE_MIN_WORD_LENGTH = 3;

    const SEARCH_MODE_SEARCH       = 0;
    const SEARCH_MODE_SEARCH_TITLE = 1;

    private static $moduleList = ['iblock',
                                  'search'];
    private        $searchMode;

    private $isUseCache = false;
    private $cacheTime  = 0;
    /** @var \CPHPCache $cPhpCache */
    private $cPhpCache = null;

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
     * @param bool $isUseCache
     * @param int  $cacheTime
     */
    public function setCacheOptions($isUseCache, $cacheTime = 360000) {
        $this->cacheTime = (int)$cacheTime;
        if (!$this->cacheTime) {
            $isUseCache = false;
        }

        $this->isUseCache = (bool)$isUseCache;
    }

    /**
     * @param $query
     * @param $params
     *
     * @return string
     */
    private function buildCacheId($query, $params) {
        return md5($query . $this->searchMode . serialize($params));
    }

    /**
     * @param $query
     * @param $params
     *
     * @return string
     */
    private function startCache($query, $params) {
        $this->cPhpCache = new \CPHPCache();

        if ($this->cPhpCache->InitCache($this->cacheTime, $this->buildCacheId($query, $params), self::CACHE_PATH)
            && $cachedData = $this->cPhpCache->GetVars()
        ) {
            $this->searchResult        = $cachedData['searchResult'];
            $this->iBlockElementIdList = $cachedData['iBlockElementIdList'];

            return true;
        } else {
            $this->cPhpCache->StartDataCache($this->cacheTime, $this->buildCacheId($query, $params), self::CACHE_PATH);
        }

        return false;
    }

    private function endCache() {
        $this->cPhpCache->EndDataCache(['searchResult'        => $this->searchResult,
                                        'iBlockElementIdList' => $this->iBlockElementIdList]);
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

        if ($this->isUseCache && $this->startCache($query, $params)) {
            return true;
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

        if ($this->isUseCache) {
            $this->endCache();
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