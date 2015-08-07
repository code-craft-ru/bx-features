<?

namespace CodeCraft;

use CodeCraft\Helpers\LanguageLink;

class SiteRouter
{
    private $language        = [];
    private $defaultLanguage = 'ru';
    private $languageList    = ['ru' => ['ru',
                                         'be',
                                         'uk',
                                         'ky',
                                         'ab',
                                         'mo',
                                         'et',
                                         'lv'],
                                'en' => 'en'];

    public function __construct() {
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
     * @param array $languageUrlMap - ['language' => 'url', ..., 'default' => 'url']
     */
    public function route(array $languageUrlMap) {
        $language = $this->getBestMatch($this->languageList);

        $languageUrlMap['default'] = $languageUrlMap['default'] ?: '/404.php';

        LanguageLink::setRootAlternateHeader($language);
        LocalRedirect($languageUrlMap[$language] ?: $languageUrlMap['default'], false, '301 Moved Permanently');
    }
}