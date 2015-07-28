<?php
/**
 * Created by PhpStorm.
 * Company: Code Craft
 * User: Manriel
 * Date: 31.03.2015
 * Time: 22:19
 */
IncludeModuleLangFile(__FILE__);

class codecraft_null extends CModule {

    public $MODULE_ID = "codecraft.null";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_CSS;

    public $PARTNER_NAME;
    public $PARTNER_URI;

    public function codecraft_null() {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path . "/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION      = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->PARTNER_NAME       = GetMessage("CODECRAFT_PARTNER");
        $this->PARTNER_URI        = GetMessage("CODECRAFT_PARTNER_URI");
        $this->MODULE_NAME        = GetMessage("CODECRAFT_NULL_INSTALL_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("CODECRAFT_NULL_INSTALL_DESCRIPTION");
    }

    function InstallFiles($arParams = array()) {

        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/" . $this->MODULE_ID . "/install/components", $_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/components", true, true);

        return true;
    }

    function UnInstallFiles() {
        DeleteDirFilesEx(BX_ROOT . "/components/codecraft/null");

        return true;
    }

    function InstallEvents() {
        return true;
    }

    function UnInstallEvents() {
        return true;
    }

    function DoInstall() {
        global $DOCUMENT_ROOT, $APPLICATION;

        $this->InstallFiles();

        $this->InstallEvents();

        RegisterModule($this->MODULE_ID);

        $APPLICATION->IncludeAdminFile(GetMessage('CODECRAFT_NULL_INSTALL_TITLE', array('#MODULE#', $this->MODULE_NAME)), $DOCUMENT_ROOT . BX_ROOT . "/modules/" . $this->MODULE_ID . "/install/step.php");
    }

    function DoUninstall() {
        global $DOCUMENT_ROOT, $APPLICATION;

        COption::RemoveOption($this->MODULE_ID, 'null');

        $this->UnInstallFiles();

        $this->UnInstallEvents();

        UnRegisterModule($this->MODULE_ID);

        $APPLICATION->IncludeAdminFile(GetMessage('CODECRAFT_NULL_UNINSTALL_TITLE', array('#MODULE#', $this->MODULE_NAME)), $DOCUMENT_ROOT . BX_ROOT . "/modules/" . $this->MODULE_ID . "/install/unstep.php");
    }
}