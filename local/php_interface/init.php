<?
$includeFilesList = array(
	$_SERVER["DOCUMENT_ROOT"].BX_ROOT.'/php_interface/include/defines.php',
	$_SERVER["DOCUMENT_ROOT"].BX_ROOT.'/php_interface/include/autoload.php',
	$_SERVER["DOCUMENT_ROOT"].BX_ROOT.'/php_interface/include/functions.php',
	$_SERVER["DOCUMENT_ROOT"].BX_ROOT.'/php_interface/include/handlers.php',
);

foreach ($includeFilesList as $fileToInclude) {
	if (file_exists($fileToInclude)) {
		include_once($fileToInclude);
	}
}
