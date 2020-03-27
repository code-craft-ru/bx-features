<?

namespace CodeCraft;

use \Bitrix\Main\Loader as BitrixLoader,
    \Bitrix\Main\LoaderException;

/**
 * Class MultipleLoader
 *
 * @author    Dmitry Panychev <panychev@code-craft.ru>
 * @version   1.0
 * @package   CodeCraft
 * @category  Bitrix, loader
 * @copyright Copyright © 2015, Dmitry Panychev
 */
class MultipleLoader
{
    /**
     * @param array $moduleList
     *
     * @throws LoaderException
     */
    public static function loadModuleList(array $moduleList) {
        foreach ($moduleList as $module) {
            if (!BitrixLoader::includeModule($module)) {
                throw new LoaderException('Module ' . $module . ' is not installed');
            }
        }
    }
}