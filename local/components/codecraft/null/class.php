<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class CCodeCraftNullComponent extends CBitrixComponent
{
    /**
     * @param string $testString
     *
     * @return string
     */
    public function getTestString ($testString = 'test') {
        return $testString;
    }

    /**
     * @return bool
     */
    public function getFalse () {
        return false;
    }
}