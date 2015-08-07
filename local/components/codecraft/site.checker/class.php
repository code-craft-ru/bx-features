<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use CodeCraft\Helpers\LanguageLink;

class CCodeCraftSiteCheckerComponent extends CBitrixComponent
{
    private $siteLanguageMap = ['ru' => '/ru/',
                                'en' => '/en/',];
    private $nameLanguageMap = ['ru' => 'рус',
                                'en' => 'eng',];

    /**
     * @return array
     */
    public function getLanguageList() {
        $languageList = [];

        foreach ($this->siteLanguageMap as $language => $languageRoot) {
            if ($language != LANGUAGE_ID) {
                LanguageLink::setAlternateHeader($language, LANGUAGE_ID, null);
            }

            $languageList[] = ['ID'     => $language,
                               'NAME'   => $this->nameLanguageMap[$language],
                               'URL'    => LanguageLink::getLanguageLink($language, LANGUAGE_ID, null, true),
                               'ACTIVE' => $language == LANGUAGE_ID,];
        }

        return $languageList;
    }
}