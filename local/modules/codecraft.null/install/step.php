<? if (!check_bitrix_sessid())
    return;
/**
 * Created by PhpStorm.
 * Company: Code Craft
 * User: Manriel
 * Date: 31.03.2015
 * Time: 22:16
 */

echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));
?>
<form action="<? echo $APPLICATION->GetCurPage() ?>">
    <input type="hidden" name="lang" value="<? echo LANG ?>">
    <input type="submit" name="" value="<? echo GetMessage("MOD_BACK") ?>">
</form>
