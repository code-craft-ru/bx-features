<?

namespace CodeCraft\Helpers;

class LanguageLink
{
    public static function getLanguageLink($languageTo, $languageFrom = false, $path = '', $withHost = false) {
        $protocol = $_SERVER['HTTPS'] ? 'https://' : 'http://';

        if (!$path) {
            global $APPLICATION;

            $path = $APPLICATION->GetCurPageParam();
        }

        $isOffSites = !(strpos($path, '/'.$languageFrom.'/') === 0);

        return ($withHost ? $protocol . $_SERVER['SERVER_NAME'] : '') .
               ($isOffSites ? '/' . $languageTo . $path : str_replace('/' . $languageFrom . '/', '/' . $languageTo . '/', $path));
    }

    public static function setAlternateHeader($languageTo, $languageFrom = false, $path = '') {
        header('Link: <' . self::getLanguageLink($languageTo, $languageFrom, $path, true)
               . '>; rel="alternate"; hreflang="' . $languageTo . '"');
    }

    public static function setRootAlternateHeader($language) {
        self::setAlternateHeader($language, false, '/');
    }
}