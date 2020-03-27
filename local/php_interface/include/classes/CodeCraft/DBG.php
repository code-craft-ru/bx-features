<?
/**
 * Class DBG
 * Simple debug class
 *
 * @author    Roman Shershnev <readytoban@gmail.com>
 * @version   1.4
 * @package   CodeCraft
 * @category  Debug
 * @copyright Copyright © 2014, Roman Shershnev
 */

namespace CodeCraft;

use IPLib\Factory;
use IPLib\Address\Type;

class DBG {
    
    public static $validRanges = [];
    
    /* function  __construct(){

    } */
    
    /**
     * @param mixed                 $data
     * @param bool | string | false $die
     * @param string | null         $msg
     * @param string | null         $color
     */
    public function __invoke($data, $die = false, $msg = null, $color = null) {
        self::dbg($data, $die, $msg, $color);
    }
    
    /**
     * @param  mixed $data
     *
     * @return string
     */
    public function __toString() {
        list($data) = func_get_arg(1);
        
        return self::debugmessage($data);
    }
    
    /**
     * @param mixed                 $data
     * @param bool | string | false $die
     * @param string | null         $msg
     * @param string | null         $color
     *
     * @return null
     */
    public static function dbg($data, $die = false, $msg = null, $color = null) {
        
        if (!is_bool($die)) {
            $msg = $die;
            $die = false;
        }
        
        if (is_string($msg) && preg_match("/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/", $msg)) {
            $color = $msg;
            $msg   = null;
        }
        
        if (self::isValidIP()) {
            echo self::debugmessage($data, (!empty($msg) ? $msg : null), $color);
            if ($die)
                die();
        }
        
    }
    
    /**
     * @param mixed                 $data
     * @param bool | string | false $die
     * @param string                $msg
     *
     * @return null
     */
    public static function dbg2EventLog($data, $die = false, $msg = 'DEBUG') {
        
        if (!is_bool($die)) {
            $msg = $die;
            $die = false;
        }
        
        $sDebug    = self::_debugmessage($data);
        $oEventLog = new \CEventLog();
        $oEventLog->Add(array(
                            "SEVERITY"      => "SECURITY",
                            "AUDIT_TYPE_ID" => "DEBUG_MESSAGE",
                            "MODULE_ID"     => "DEBUG",
                            "ITEM_ID"       => $msg,
                            "DESCRIPTION"   => $sDebug
                        ));
        if ($die && self::isValidIP())
            die();
    }
    
    /**
     * @param mixed                 $data
     * @param bool | string | false $die
     * @param string                $msg
     *
     * @return null
     */
    public static function dbg2File($data, $die = false, $msg = 'DEBUG') {
        
        if (!is_bool($die)) {
            $msg = $die;
            $die = false;
        }
        
        if (!defined('LOG_FILENAME')) {
            define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"] . "/debug.log");
        }
        
        AddMessage2Log(self::_debugmessage($data), $msg);
        
        if ($die && self::isValidIP())
            die();
    }
    
    /**
     * @param  mixed  $data
     * @param  string $header
     * @param  string $color
     *
     * @return string
     */
    public static function debugmessage($data, $header = '', $color = '') {
        if ($color == null || $color == '')
            $color = '#008000';
        $dbg = debug_backtrace();
        
        $sRetStr = "<table border='0' style='color: ${color}; font-size: 8pt; border: 1px solid ${color};'>";
        if (strlen($header) > 0)
            $sRetStr .= "<tr><td>[${header}]</td></tr>";
        $sRetStr .= "<tr><td>" . $dbg[2]["file"] . " (" . $dbg[2]["line"] . ")</td></tr><tr><td><pre>";
        $sRetStr .= self::_debugmessage($data);
        $sRetStr .= "</pre></td></tr>";
        $sRetStr .= "</table>";
        
        return $sRetStr;
    }
    
    /**
     * @param mixed $data
     *
     * @return string | NULL
     */
    public static function _debugmessage($data) {
        return var_export($data, true);
    }
    
    public static function addAllowed($address) {
        $range = Factory::rangeFromString($address);
        if ($range === null) {
            $ips = self::resolveHostname($address);
            foreach ($ips['v4'] as $ip) {
                if ($resolvedAddress = Factory::rangeFromString($ip)) {
                    self::$validRanges[] = $resolvedAddress;
                }
            }
            foreach ($ips['v6'] as $ip) {
                if (strpos($ip, '/') === false) {
                    $ip .= '/64';
                }
                if ($resolvedRange = Factory::rangeFromString($ip)) {
                    self::$validRanges[] = $resolvedRange;
                }
            }
        } else {
            self::$validRanges[] = $range;
        }
    }
    
    public static function resolveHostname($hostname) {
        $ips = [
            'v4' => [],
            'v6' => [],
        ];
        $ipv4 = gethostbynamel($hostname);
        foreach ($ipv4 as $ip) {
            $ips['v4'][] = $ip;
        }
        
        $ipv6 = dns_get_record($hostname, DNS_AAAA);
        foreach ($ipv6 as $record) {
            if ($record["type"] == "AAAA") {
                $ips['v6'][] = $record["ipv6"];
            }
        }
        
        return $ips;
    }
    
    /**
     * @return bool
     */
    public static function isValidIP() {
        
        if (isset($_COOKIE['DEBUG'])) {
            return true;
        }
        
        $remoteAddress = Factory::addressFromString($_SERVER['REMOTE_ADDR']);
        if ($remoteAddress === null) {
            return false;
        }
        $remoteAddressType = $remoteAddress->getAddressType();
        
        foreach (self::$validRanges as $range) {
            if ($range === null) continue;
            if ($remoteAddress->matches($range)) {
                return true;
            }
        }
    }
    
}
