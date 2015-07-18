<?php
namespace Manage\Controller;
class DataBackupLogController extends ManageController {

    public function index() {
        // 显示字段
        $tplList['id'] = array('id' => 'dblo_id', 'percent' => '8', 'title' => L('NUMBER'));
        $tplList[] = array('id' => 'dblo_title', 'percent' => '20', 'title' => L('FILE_TITLE'), 'class' => 'showContent');
        $tplList[] = array('id' => 'u_id', 'percent' => '15', 'title' => L('U_ID'), 'class' => 'showContent');
        $tplList[] = array('id' => 'dblo_type', 'percent' => '10', 'title' => L('TYPE'));
        $tplList[] = array('id' => 'dblo_created', 'percent' => '20', 'title' => L('CREATED'));
        $tplList['action'] = array('id' => 'action', 'percent' => '20', 'title' => L('OPERATION'));

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
        // 检查备份频率
        if (!D('DataBackupLog')->check()) {
            $this->error(D('DataBackupLog')->getError());
        } else {
            // 备份
            $this->show(D('DataBackupLog')->backup(1), L('BACKUP'));
        }
    }

    public function delete() {
        $this->show(D('DataBackupLog')->delete($_GET['id']), L('DELETE'));
    }

    public function resume() {
        $this->show(D('DataBackupLog')->resume($_GET['id']), L('RESUME'));
    }
}