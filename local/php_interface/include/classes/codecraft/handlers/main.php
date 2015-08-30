<?

namespace CodeCraft\Handlers;

use Bitrix\Main\Loader;

class Main
{
    /**
     * @param array $aGlobalMenu
     * @param array $aModuleMenu
     */
    public static function addReviewMenu(&$aGlobalMenu, &$aModuleMenu) {
        global $USER;
        if (!$USER->IsAdmin() || !Loader::includeModule('forum')) {
            return;
        }

        $aMenu = ['parent_menu' => 'global_menu_services',
                  'section'     => 'services',
                  'sort'        => 1,
                  'text'        => 'Отзывы к товарам',
                  'title'       => 'Отзывы к товарам',
                  'url'         => '/bitrix/admin/codecraft/forum_review.php?lang=' . LANGUAGE_ID,
                  'icon'        => 'forum_menu_icon',
                  'page_icon'   => 'forum_menu_icon',
                  'items_id'    => 'menu_product_reviews',
                  'more_url'    => [],
                  'items'       => []];

        $aModuleMenu[] = $aMenu;
    }
}