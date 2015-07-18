<?php
namespace Manage\Controller;
class DatabaseBackupLogController extends ManageController {

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'dbl_id', 'percent' => '8', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'dbl_title', 'percent' => '20', 'title' => L('FILE_TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'u_id', 'percent' => '10', 'title' => L('U_ID'), 'class' => 'showContent');
        $tplList[] = array('id' => 'dbl_type', 'percent' => '5', 'title' => L('BACKUP_TYPE'));
        $tplList[] = array('id' => 'dbl_created', 'percent' => '15', 'title' => L('CREATED'));
        $tplList['action'] = array('id' => 'action', 'percent' => '12', 'title' => L('OPERATION'));

        // 操作
        $action[] = array('id' => 'resume', 'percent' => '6', 'title' => L('RESUME'));
        $action[] = array('id' => 'del', 'percent' => '6', 'title' => L('DELETE'));

        // 工具
        $tools = array('add');
        $indexTpl = generationTpl($tplList, $action, $search, $tools);
        $this->assign('indexTpl', str_replace(L('ADD'), L('BACKUP'), $indexTpl));
        parent::index();
    }

    public function add() {
        $this->show(D('DatabaseBackupLog')->backup(1), L('BACKUP'));
    }

    public function delete() {
        $this->show(D('DatabaseBackupLog')->delete($_GET['id']), L('DELETE'));
    }

    public function resume() {
        $this->show(D('DatabaseBackupLog')->resume($_GET['id']), L('RESUME'));
    }
}