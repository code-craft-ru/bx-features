<?php
/**
 * Created by PhpStorm.
 * Company: Code Craft
 * User: Manriel
 * Date: 20.10.2015
 * Time: 12:59
 */

namespace CodeCraft\Menu;

abstract class Menu {

    protected $menu     = [];
    protected $maxDepth = 4;

    public function __construct($arResult = [], $arParams = []) {
        $this->_setMenu($arResult);
        $this->_setParams($arParams);
    }

    private function _setMenu($arResult) {
        $this->menu = $this->_makeTree(self::_resetMenuIndexes($arResult));
    }

    private function _setParams($arParams) {
        if (isset($arParams['MAX_LEVEL'])) {
            $this->maxDepth = $arParams['MAX_LEVEL'];
        }
    }

    private static function _resetMenuIndexes($menu) {
        $result = array();
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
                $childMenu   = array();
                while (isset($inputMenu[$index]) && $inputMenu[$index]['DEPTH_LEVEL'] > $parentDepth) {
                    $childMenu[] = $inputMenu[$index];
                    unSet($inputMenu[$index]);
                    $index++;
                }
                unSet($index);
                $childMenu                           = self::_makeTree($childMenu);
                $inputMenu[$parentIndex]['CHILDREN'] = $childMenu;
                unSet($childMenu);
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
    protected function _drawMenuNextLevel($menu = [], $depth = 1, $title = "") {
        if ($depth > $this->maxDepth || $depth < 1) {
            return '';
        }
        switch ($depth) {
            case 1:
                return $this->_drawMenu1Level($menu, $title);
                break;
            case 2:
                return $this->_drawMenu2Level($menu, $title);
                break;
            case 3:
                return $this->_drawMenu3Level($menu, $title);
                break;
            case 4:
                return $this->_drawMenu4Level($menu, $title);
                break;
            default:
                return $this->_drawMenu1Level($menu, $title);
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
    abstract protected function _drawMenu1Level($menu = [], $title = '');

    /**
     * Returns 2nd level menu html
     *
     * @param array  $menu
     * @param string $title
     *
     * @return string
     */
    abstract protected function _drawMenu2Level($menu = [], $title = '');

    /**
     * Returns 3rd level menu html
     *
     * @param array  $menu
     * @param string $title
     *
     * @return string
     */
    abstract protected function _drawMenu3Level($menu = [], $title = '');

    /**
     * Returns 4th level menu html
     *
     * @param array  $menu
     * @param string $title
     *
     * @return string
     */
    abstract protected function _drawMenu4Level($menu = [], $title = '');

}