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
 * @param mixed      $data
 * @param bool|false $die
 * @param string     $msg
 */
function dbg2log($data, $die = false, $msg = null) {
    \CodeCraft\DBG::dbg2File($data, $die, $msg);
}
