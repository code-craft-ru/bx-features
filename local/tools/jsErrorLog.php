<?

if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
    return;
}

define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC', 'Y');
define('PUBLIC_AJAX_MODE', true);
define('NOT_CHECK_PERMISSIONS', true);
define('LOG_FILENAME', $_SERVER['DOCUMENT_ROOT'] . '/logs/js_error.txt');

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use CodeCraft\Loggers\JsErrorLogger;

$logger = new JsErrorLogger();
$logger->log();