<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Page\Asset, CodeCraft\Loggers\JsErrorLogger;

$logger = new JsErrorLogger();
$asset  = Asset::getInstance();

$asset->addJs(SITE_TEMPLATE_PATH . '/js/project.js');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="robots" content="noindex">
    <? $APPLICATION->ShowHead() ?>
    <!--[if IE]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    <title><? $APPLICATION->ShowTitle(); ?></title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico" />
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
</head>

<body>
<? $APPLICATION->ShowPanel(); ?>
<? $logger->watch('/local/tools/jsErrorLog.php') ?>
