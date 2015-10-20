<?php
/**
 * Created by PhpStorm.
 * Company: Code Craft
 * User: Manriel
 * Date: 20.10.2015
 * Time: 13:51
 */

namespace CodeCraft\Menu;

class MenuSimple extends Menu {

    protected function _drawMenu1Level($menu = [], $title = '') {
        if (empty($menu)) {
            return '';
        }

        $outString = '<ul>';
        foreach ($menu as $index => $item) {

            $class = [];
            if (!isSet($arResult[$index - 1])) {
                $class[] = 'first';
            } elseif (!isSet($arResult[$index + 1])) {
                $class[] = 'last';
            };
            if ($item["SELECTED"]) {
                $class[] = 'selected';
            };

            $outString .= '<li';
            if (!empty($class)) {
                $outString .= ' class="' . implode($class, ' ') . '""';
            }
            $outString .= '>';
            $outString .= '<a href="' . $item['LINK'] . '"';
            // if (!empty($class)) {
            //     $outString .= ' class="' . implode($class, ' ') . '""';
            // }
            $outString .= '>';
            $outString .= $item['TEXT'];
            $outString .= '</a>';
            $outString .= $this->_drawMenuNextLevel($item['CHILDREN'], $item['DEPTH_LEVEL'] + 1, $item['TEXT']);
            $outString .= '</li>';
        }
        $outString .= '</ul>';

        return $outString;
    }

    protected function _drawMenu2Level($menu = [], $title = '') {
        return $this->_drawMenu1Level($menu, $title);
    }

    protected function _drawMenu3Level($menu = [], $title = '') {
        return $this->_drawMenu1Level($menu, $title);
    }

    protected function _drawMenu4Level($menu = [], $title = '') {
        return $this->_drawMenu1Level($menu, $title);
    }

}