<?

namespace CodeCraft;

use CodeCraft\Helpers\LanguageLink;

class SiteRouter
{
    private $language        = [];
    private $defaultLanguage = 'ru';
    private $languageUrlMap  = [];
    private $languageList    = ['ru' => ['ru',
                                         'be',
                                         'uk',
                                         'ky',
                                         'ab',
                                         'mo',
                                         'et',
                                         'lv'],
                                'en' => 'en'];

    /**
     * @param array $languageUrlMap - ['language' => 'url', ..., 'default' => 'url']
     */
    public function __construct($languageUrlMap) {
        $this->setLanguageUrlMap($languageUrlMap);

        if (($list = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']))) {
            if (preg_match_all('/([a-z]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/', $list, $list)) {
                $this->language = array_combine($list[1], $list[2]);
                foreach ($this->language as $n => $v) {
                    $this->language[$n] = $v ? $v : 1;
                }
                arsort($this->language, SORT_NUMERIC);
            }
        } else {
            $this->language = [];
        }
    }

    /**
     * @param array $languageUrlMap
     */
    public function setLanguageUrlMap(array $languageUrlMap) {
        $this->languageUrlMap = $languageUrlMap;
    }

    /**
     * @return array
     */
    public function getLanguageUrlMap() {
        return $this->languageUrlMap;
    }

    /**
     * @param array $languageList
     *
     * @return string
     */
    private function getBestMatch(array $languageList) {
        $languages = array();
        foreach ($languageList as $lang => $alias) {
            if (is_array($alias)) {
                foreach ($alias as $alias_lang) {
                    $languages[strtolower($alias_lang)] = strtolower($lang);
                }
            } else {
                $languages[strtolower($alias)] = strtolower($lang);
            }
        }
        foreach ($this->language as $l => $v) {
            $s = strtok($l, '-');
            if (isset($languages[$s])) {
                return $languages[$s];
            }
        }

        return $this->defaultLanguage;
    }

    /**
     * @param array $languageList
     */
    public function setLanguageList(array $languageList) {
        $this->languageList = $languageList;
    }

    /**
     * @param string $pathTo404
     */
    public function route($pathTo404 = '') {
        $language       = $this->getBestMatch($this->languageList);
        $languageUrlMap = $this->getLanguageUrlMap();

        LanguageLink::setRootAlternateHeader($language);

        if ($languageUrlMap[$language] || $languageUrlMap['default']) {
            LocalRedirect($languageUrlMap[$language] ?: $languageUrlMap['default'], false, '301 Moved Permanently');
        } else {
            $this->showNotFoundPage($pathTo404);
        }
    }

    /**
     * @param string $pathTo404
     *
     * @buffer_restart
     *
     * @require 404 page
     *
     * @die
     */
    public function showNotFoundPage($pathTo404 = '') {
        if (!$pathTo404) {
            $pathTo404 = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->getBestMatch($this->languageList) . '/404.php';
        }

        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        try {
            require_once($pathTo404);
        } catch (\Exception $e) {
            \CHTTP::SetStatus('404 Not Found');

            echo '<h1>404 Not Found</h1>';
        }

        die;
    }
}