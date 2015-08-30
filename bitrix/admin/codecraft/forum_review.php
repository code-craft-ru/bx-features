<?
/**
 * @author Panychev Dmitry, Code Craft
 * @link http://code-craft.ru
 *
 * Forum reviews v 0.8 provide forum`s messages moderate on admin panel.
 *
 * @global CMain     $APPLICATION
 * @global CDatabase $DB
 * @global CUser     $USER
 */

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');

Loader::includeModule('forum');
Loader::includeModule('iblock');

$headerList = [['id'      => 'ID',
                'content' => 'ID',
                'sort'    => 'id',
                'default' => true,
                'align'   => 'right',],
               ['id'      => 'AUTHOR_NAME',
                'content' => 'Имя автора',
                'sort'    => 'author_name',
                'default' => true,
                'align'   => 'right',],
               ['id'      => 'AUTHOR_ID',
                'content' => 'Автор',
                'sort'    => 'author_id',
                'default' => true,
                'align'   => 'right',],
               ['id'      => 'IBLOCK_ELEMENT',
                'content' => 'Элемент',
                'sort'    => 'topic_id',
                'default' => true,
                'align'   => 'right',],
               ['id'      => 'POST_MESSAGE',
                'content' => 'Комментарий',
                'default' => true,
                'align'   => 'right',],
               ['id'      => 'POST_DATE',
                'content' => 'Дата поста',
                'sort'    => 'post_date',
                'default' => true,
                'align'   => 'right',],];

$tableId = md5('review_list');
$sort    = new CAdminSorting($tableId, 'id', 'desc', 'by', 'order');

$lAdmin             = new CAdminList($tableId, $sort);
$lAdmin->bMultipart = true;
$lAdmin->AddHeaders($headerList);

if ($lAdmin->EditAction() && check_bitrix_sessid()) {
    $fieldList = [];

    foreach ($_REQUEST['FIELDS'] as $id => $field) {
        if ($field == $_REQUEST['FIELDS_OLD'][$id]) {
            continue;
        }

        foreach ($field as &$value) {
            $value = htmlspecialcharsEx($value);
        }
        $fieldList[$id] = $field;
    }

    foreach ($fieldList as $id => $fields) {
        if (!CForumMessage::Update($id, $fields)) {
            if ($exception = $APPLICATION->GetException()) {
                $lAdmin->AddGroupError($exception->GetString(), $id);
            } else {
                $lAdmin->AddGroupError('Ошибка редактирования отзывов', $id);
            }
        }
    }
}

if (($idList = $lAdmin->GroupAction()) && check_bitrix_sessid()) {
    foreach ($idList as $id) {
        if (!(int)$id) {
            continue;
        }

        switch ($_REQUEST['action']) {
            case 'delete' :
                CForumMessage::Delete($id);
                break;
        }
    }
}

$themeList        = [];
$orderList        = (strtoupper($by) === 'ID'
    ? [$by => $order]
    : [$by  => $order,
       'ID' => 'DESC']);

$reviewCollection = CForumMessage::GetList($orderList);

$adminResult = new CAdminResult($reviewCollection, $tableId);
$adminResult->NavStart();
$lAdmin->NavText($adminResult->GetNavPrint('На странице:', true));

while ($res = $adminResult->NavNext(true, 'f_')) {
    $row =& $lAdmin->AddRow($f_ID, $res);

    if ((int)$res['PARAM2']) {
        $element               = CIBlockElement::GetList([], ['ID' => $res['PARAM2']], false, false, ['ID',
                                                                                                      'IBLOCK_ID',
                                                                                                      'IBLOCK_TYPE',
                                                                                                      'NAME'])->Fetch();
        $res['IBLOCK_ELEMENT'] = $element ? '<a href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID='
                                            . $element['IBLOCK_ID'] . '&type=' . $element['IBLOCK_TYPE'] . '&ID='
                                            . $element['ID'] . '" target="_blank"]>' . $element['NAME'] . '</a>'
            : $res['PARAM2'] . ' (Удалён)';
    } else {
        $res['IBLOCK_ELEMENT'] = '';
    }

    foreach ($headerList as $col) {
        switch ($col['id']) {
            case 'AUTHOR_ID':
                if ($res['AUTHOR_ID'] && $user = CUser::GetByID($res['AUTHOR_ID'])->Fetch()) {
                    $row->AddViewField($col['id'], '[<a href="user_edit.php?lang=' . LANGUAGE_ID . '&ID='
                                                   . $res['AUTHOR_ID'] . '" target="_blank">' . $user['ID']
                                                   . '</a>]&nbsp;(' . htmlspecialcharsEx($user['LOGIN']) . ') '
                                                   . htmlspecialcharsEx($user['NAME'] . ' ' . $user['LAST_NAME']));
                } else {
                    $row->AddViewField($col['id'], $res[$col['id']] . $res['AUTHOR_ID']);
                }
                break;
            case 'POST_MESSAGE':
                $input = '<div style="width:400px;"><label for="' . $res['ID'] . '_edit">Отзыв</label><br>';
                $input .= '<textarea rows="10" cols="50" name="FIELDS[' . $res['ID'] . '][POST_MESSAGE]" id="'
                          . $res['ID'] . '_edit">' . htmlspecialcharsex($res['POST_MESSAGE']) . '</textarea></div>';
                $row->AddEditField('POST_MESSAGE', $input);
                break;
            default:
                $row->AddViewField($col['id'], $res[$col['id']]);
        }
    }

    $actions = [['DEFAULT' => 'Y',
                 'ICON'    => 'edit',
                 'TEXT'    => 'Редактировать',
                 'ACTION'  => $lAdmin->ActionRedirect($APPLICATION->GetCurPageParam(bitrix_sessid_get() . '&ID[]='
                                                                                    . $f_ID
                                                                                    . '&action_button=edit', ['sessid',
                                                                                                              'ID',
                                                                                                              'action_button']))],
                ['ICON'   => 'delete',
                 'TEXT'   => 'Удалить',
                 'ACTION' => 'if(confirm(\'Вы действительно хотите удалить отзыв?\')) window.location=\''
                             . $APPLICATION->GetCurPage() . '?lang=' . LANGUAGE_ID . '&action=delete&ID=' . $f_ID . '&'
                             . bitrix_sessid_get() . '\';']];

    $row->AddActions($actions);
}

$lAdmin->AddFooter([['title' => Loc::getMessage('MAIN_ADMIN_LIST_SELECTED'),
                     'value' => $adminResult->SelectedRowsCount()],
                    ['counter' => true,
                     'title'   => Loc::getMessage('MAIN_ADMIN_LIST_CHECKED'),
                     'value'   => '0'],]);

$chain = $lAdmin->CreateChain();
$chain->AddItem(['TEXT' => 'Отзывы на сайте',
                 'LINK' => $APPLICATION->GetCurPageParam('', [], true)]);

$lAdmin->ShowChain($chain);

$lAdmin->AddGroupActionTable(['delete' => 'Удалить отзывы',]);
$lAdmin->AddAdminContextMenu([], false, false);

$lAdmin->CheckListMode();

$APPLICATION->SetTitle('Отзывы на сайте');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');

$lAdmin->DisplayList();

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');