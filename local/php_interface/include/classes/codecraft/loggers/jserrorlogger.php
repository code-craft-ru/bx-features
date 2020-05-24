<?

/**
 * Class JsErrorLogger
 * Simple js error logger for Bitrix
 *
 *
 * @author    Dmitry Panychev <panychev@code-craft.ru>
 * @based     Anton Dolganin post - <http://blog.d-it.ru/dev/logging-js-errors-on-the-server/>
 * @version   1.0
 * @package   CodeCraft
 * @category  Debug, Logging
 * @copyright Copyright © 2016, Dmitry Panychev
 */

namespace CodeCraft\Loggers;

use \Bitrix\Main\Context;

class JsErrorLogger {

    /**
     * JsErrorLogger constructor.
     *
     * @throws \Exception
     */
    public function __construct() {
        if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
            throw new \Exception ('Bitrix prolog is not included');
        }
    }

    /**
     * Print onError script, it handle and send error
     *
     * @param $pathToAction
     */
    public function watch($pathToAction) {
        $js = '<script>
           window.onerror = function (msg, url, line, col, exception) {
              BX.ajax.get(\'' . $pathToAction . '\', {
                    data: {
                        msg: msg,
                        exception: exception,
                        url: url,
                        line: line,
                        col: col
                    }
              });
           }
        </script>';

        echo $js;
    }

    /**
     * Log errors from request to file
     *
     * @use \Bitrix\Main\Context
     * @use AddMessage2Log
     */
    public function log() {
        $request = Context::getCurrent()->getRequest();

        if ($request->get('data')) {
            AddMessage2Log(var_export($request->get('data')));
        }
    }
}