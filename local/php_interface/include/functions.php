<?

/**
 * @param mixed      $data
 * @param bool|false $die
 * @param string     $msg
 * @param string     $color
 */
function dbg($data, $die = false, $msg = null, $color = null) {
    \CodeCraft\DBG::dbg($data, $die, $msg, $color);
}

/**
 * @TODO delete unused parameter $color, #Manriel!
 *
 * @param mixed      $data
 * @param bool|false $die
 * @param string     $msg
 * @param string     $color
 */
function dbg2log($data, $die = false, $msg = null, $color = null) {
    \CodeCraft\DBG::dbg2File($data, $die, $msg);
}
