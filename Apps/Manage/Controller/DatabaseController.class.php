<?php
namespace Manage\Controller;
class DatabaseController extends ManageController {

    protected $db =  NULL;

    public function index() {

        $this->assign('dbs', D('Database')->getDbList());
        $this->assign('useDb', D('Database')->getUseDb());
        $this->assign('selectDb', D('Database')->getSelectDb());
        $this->assign('tables', $tables);
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . '.js'));
        $this->display();
    }

    public function add() {
        if ($_POST['attr']) {
            switch($_REQUEST['attr']) {
                case 'database':
                    $result = D('Database')->createDatabase($_POST['dbName'], $_POST['charset'], $_POST['collation']);
                    $this->show($result);
                    break;
                case 'table':
                    switch ($_POST['action']) {
                        case 'copy':
                            $result = D('Database')->cloneTable($_SESSION['selectDb'], $_POST['sourceTable'], $_POST['dbName'], $_POST['tableName'], $_POST['option']);
                            $this->show($result, '', __CONTROLLER__ . '/shows/attr/database');
                            break;
                        case 'move':
                            D('Database')->cloneTable($_SESSION['selectDb'], $_POST['sourceTable'], $_POST['dbName'], $_POST['tableName'], $_POST['option']);
                            $result = D('Database')->deleteTable($_SESSION['selectDb'], $_POST['sourceTable']);
                            $this->show($result, '', __CONTROLLER__ . '/shows/attr/database');
                            break;
                        case 'new':
                            D('Database')->createTable($_SESSION['selectDb'], $_POST);
                            $this->show($result, '', __CONTROLLER__ . '/shows/attr/database');
                            break;
                    }
                    break;
            }
        } else {
            $this->display($_REQUEST['attr']);
        }
    }

    public function delete() {
        if ($_POST) {
            switch($_POST['attr']) {
                case 'table' :
                    D('Database')->deleteTable($_SESSION['selectDb'], $_POST['tableName']);
                    echo json_encode(array('url' => __CONTROLLER__ . '/shows/attr/database'));
                    break;
                case 'database' :
                    D('Database')->deleteDatabase($_SESSION['selectDb']);
                    echo json_encode(array('url' => __CONTROLLER__ . '/index'));
                    break;
            }
        }
    }

    public function lists() {
        switch ($_POST['attr']) {
            case 'table' :
                $tables = D('Database')->getTables($_POST['useDb']);
                echo json_encode($tables);
                break;
            case 'sql' :
                echo json_encode(D('Database')->getSqlData($_SESSION['selectDb'], $_POST['sql']));
                break;
        }
    }

    public function shows() {
        if ($_POST) {
           echo json_encode(D('Database')->getTableStatus($_POST['dbName']));
        } else {
            $this->assign('dbs', D('Database')->getDbList());
            $this->assign('selectDb', D('Database')->getSelectDb());
            $this->display('show' . ucfirst($_REQUEST['attr']));
        }
    }

    public function edit() {
        if ($_POST) {
            switch($_POST['attr']) {
                case 'table' :
                    switch ($_POST['action']) {
                        case 'clear':
                            D('Database')->clearTable($_SESSION['selectDb'], $_POST['Name']);
                            echo json_encode(array('url' => __CONTROLLER__ . '/shows/attr/database'));
                            break;
                        default:
                            D('Database')->updateTable($_SESSION['selectDb'], $_POST['Name'], $_POST['Engine'], $_POST['Comment'], $_POST['Charset'], $_POST['Collation']);
                            $this->show($result, '', __CONTROLLER__ . '/shows/attr/database');
                    }
                    break;
            }
        } else {
            switch($_GET['attr']) {
                case 'table' :
                    if (in_array($_GET['actionName'], array('copy', 'move'))) {
                        $this->assign('dbs', D('Database')->getDbList());
                    }
                    if (in_array($_GET['actionName'], array('edit'))) {
                        $vo = D('Database')->getTableStatus($_SESSION['selectDb'], $_GET['tableName']);
                        $this->assign('vo', $vo[0]);
                    }

                    $this->assign('tableName', $_GET['tableName']);
                    $this->display($_GET['actionName'] . 'Table');
                    break;
            }
        }
    }
}