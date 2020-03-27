<?
namespace CodeCraft;

/**
 * Class CClassLoader
 * Provides autoloading for other classes any package
 * Singleton.
 *
 * To correctly autoload place classes into /bitrix/php_interface/include/classes/ folder
 * by this path template: ./package_name/class_name.php
 * @author    Roman Shershnev <readytoban@gmail.com>
 * @version   1.2
 * @package   CodeCraft
 * @category  Init
 * @copyright Copyright (c) 2014, Roman Shershnev
 */
class CClassLoader {

    /**
     * @access protected
     * @static object CClassLoader
     */
    private static $instance;

    /**
     * Get Instance function
     * @access public
     * @static
     */
    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new CClassLoader;
        }
        return self::$instance;
    }

    /**
     * Constructor function
     * @access private
     */
    private function __construct() {
        $this->setRootDir();
        spl_autoload_register(array($this, 'includeClass'));
    }

    /**
     * @access private
     */
    private function __clone() { /* this is Singleton class */ }
    /**
     * @access private
     */
    private function __wakeup()  { /* this is Singleton class */ }

    /**
     * Include file with required class
     * @access private
     * @param  $class string
     *
     * @return null
     */
    private function includeClass($class) {
        $name = str_replace("\\", "/", $class);
        $classesDir = "/local/php_interface/include/classes/";
        $classFile = $_SERVER["DOCUMENT_ROOT"] . $classesDir . $name . ".php";
        if (file_exists($classFile)) {
            require_once($classFile);
        } else {
            $classesDir = BX_ROOT."/php_interface/include/classes/";
            $classFile = $_SERVER["DOCUMENT_ROOT"] . $classesDir . $name . ".php";
            if (file_exists($classFile)) {
                require_once($classFile);
            }/* else {
                throw new \Exception("Unable to load ".$class);
            }*/
        }
    }

    /**
     * Function check server variable DOCUMENT_ROOT and set it if unset to provide console calls
     * @access private
     *
     * @return null
     */
    private function setRootDir() {
        if (empty($_SERVER['DOCUMENT_ROOT']))
            $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__)."/../../../");
    }
}

CClassLoader::getInstance();
