<?php
namespace Common\Logic;
class AppLevelDetailLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'order' => 'ald_rank ASC',
            'where' => array('al_id' => intval($param['al_id'])),
        );

        $config = array_merge($default, $config);

        // 获取数据
        $lists = D('AppLevelDetail', 'Model')->getAll($config);

        $return = array();
        foreach ($lists as $key => $value) {
            
            // 图片
            $level = C('UPLOADS_ROOT_PATH') . C('APP_LEVEL_PATH') . $value['ald_id'] . '.' . C('DEFAULT_IMAGE_EXT');
            $level = file_exists($level) ? $level : C('UPLOADS_ROOT_PATH') . C('CONFIG_FILE_PATH') . C('DEFAULT_IMAGE') . '.' . C('DEFAULT_IMAGE_EXT');
            $return[$key] = $value;
            $return[$key]['level'] = substr($level, 1);
        }

        // 输出数据
        return $return;
    }
}