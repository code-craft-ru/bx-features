<?php
/**
 * Created by PhpStorm.
 * Company: Code Craft
 * User: Manriel
 * Date: 02.06.2015
 * Time: 1:08
 */

function dbg($data, $die = false, $msg = null, $color = null) {
    \CodeCraft\DBG::dbg($data, $die, $msg, $color);
}

function dbg2log($data, $die = false, $msg = null, $color = null) {
    \CodeCraft\DBG::dbg2File($data, $die, $msg);
}
