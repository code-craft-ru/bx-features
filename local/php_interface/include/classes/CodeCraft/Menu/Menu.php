<?php
/**
 * Abstract class Menu
 * Provides recursive menu
 *
 * @author    Roman Shershnev <readytoban@gmail.com>
 * @version   1.0
 * @package   CodeCraft
 * @category  Menu
 * @copyright Copyright � 2015, Roman Shershnev
 */

namespace CodeCraft\Menu;

abstract class Menu {

    protected $menu     = [];
    protected $maxDepth = 4;

    /**
     * @param array $arResult
     * @param array $arParams
     */
    public function __construct($arResult = [], $arParams = []) {
        $this->init($arResult, $arParams);
    }

    public function init($arResult = [], $arParams = []) {
        $this->_setMenu($arResult)->_setParams($arParams);

        return $this;
    }

    private function _setMenu($arResult) {
        $this->menu = self::_makeTree(self::_resetMenuIndexes($arResult));

        return $this;
    }

    private function _setParams($arParams) {
        if (isset($arParams['MAX_LEVEL'])) {
            $this->maxDepth = $arParams['MAX_LEVEL'];
        }

        return $this;
    }

    private static function _resetMenuIndexes($menu) {
        $result = [];
        foreach ($menu as $index => $item) {
            $result[] = $item;
        }

        return $result;
    }

    /**
     * Recursive makes tree from standard Bitrix $arResult
     *
     * @param array $inputMenu
     * @param int   $parentIndex
     *
     * @return array
     */
    private static function _makeTree($inputMenu = [], $parentIndex = 0) {
        if (!isSet($inputMenu[$parentIndex])) {
            return $inputMenu;
        } else {
            if ($inputMenu[$parentIndex]['IS_PARENT']) {
                $parentDepth = $inputMenu[$parentIndex]['DEPTH_LEVEL'];
                $index       = $parentIndex + 1;
                $childMenu   = [];
                while (isset($inputMenu[$index]) && $inputMenu[$index]['DEPTH_LEVEL'] > $parentDepth) {
                    $childMenu[] = $inputMenu[$index];
                    unset($inputMenu[$index]);
                    $index++;
                }
                unset($index);
                $childMenu                           = self::_makeTree($childMenu);
                $inputMenu[$parentIndex]['CHILDREN'] = $childMenu;
                unset($childMenu);
            }
            $inputMenu = self::_resetMenuIndexes($inputMenu);

            return self::_makeTree($inputMenu, $parentIndex + 1);
        }
    }

    /**
     * Prints Menu
     *
     * @return $this
     */
    public function drawMenu() {
        echo $this->_drawMenuNextLevel($this->menu);

        return $this;
    }

    /**
     * @param array  $menu
     * @param int    $depth
     * @param string $title
     *
     * @return string
     */
    protected function _drawMenuNextLevel($menu = [], $depth = 1, $title = "", $parent = null) {
        if ($depth > $this->maxDepth || $depth < 1) {
            return '';
        }
        switch ($depth) {
            case 1:
                return $this->_drawMenu1Level($menu, $title, $parent);
                break;
            case 2:
                return $this->_drawMenu2Level($menu, $title, $parent);
                break;
            case 3:
                return $this->_drawMenu3Level($menu, $title, $parent);
                break;
            case 4:
                return $this->_drawMenu4Level($menu, $title, $parent);
                break;
            default:
                return $this->_drawMenu1Level($menu, $title, $parent);
                break;
        }
    }

    /**
     * Returns 1st level menu html
     *
     * @param array  $menu
     * @param string $title
     *
     * @return string
     */
    abstract protected function _drawMenu1Level($menu = [], $title = '', $parent = null);

    /**
     * Returns 2nd level menu html
     *
     * @param array  $menu
     * @param string $title
     *
     * @return string
     */
    abstract protected function _drawMenu2Level($menu = [], $title = '', $parent = null);

    /**
     * Returns 3rd level menu html
     *
     * @param array  $menu
     * @param string $title
     *
     * @return string
     */
    abstract protected function _drawMenu3Level($menu = [], $title = '', $parent = null);

    /**
     * Returns 4th level menu html
     *
     * @param array  $menu
     * @param string $title
     *
     * @return string
     */
    abstract protected function _drawMenu4Level($menu = [], $title = '', $parent = null);

}
