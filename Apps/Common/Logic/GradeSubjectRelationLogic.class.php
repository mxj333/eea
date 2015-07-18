<?php
namespace Common\Logic;
class GradeSubjectRelationLogic extends Logic {
    public function lists($param = array(), $config = array()) {

        $param = $param ? $param : $_REQUEST;
        
        $default = array(
            'is_deal_result' => true,
            'order' => 'gsr_id ASC',
            'p' => intval($param['p']),
        );

        // 查询条件
        if ($param['re_id']) {
            $regConfig['where']['re_ids'] = $param['re_id'];
            $regConfig['fields'] = 're_ids_children';
            $region = D('Region', 'Model')->getOne($regConfig);
            $region = $region ? $region . ',' . $param['re_id'] : $param['re_id'];
            $default['where']['re_id'] = array('IN', $region);
        }

        if ($param['gsr_school_type']) {
            $default['where']['gsr_school_type'] = $param['gsr_school_type'];
        }

        if ($param['gsr_grade']) {
            $default['where']['gsr_grade'] = $param['gsr_grade'];
        }

        $config = array_merge($default, $config);

        // 分页获取数据
        $lists = D($this->name)->getListByPage($config);

        if ($config['is_deal_result']) {
            $tag = reloadCache('tag');
            // 处理数据
            foreach ($lists['list'] as $key => $value) {
                
                $lists['list'][$key]['gsr_school_type'] = $tag[4][$value['gsr_school_type']];
                $lists['list'][$key]['gsr_grade'] = $tag[7][$value['gsr_grade']];
                if ($value['gsr_subject']) {
                    $subjects = explode(',', $value['gsr_subject']);
                    $sub_name = array();
                    foreach ($subjects as $sub) {
                        $sub_name[] = $tag[5][$sub];
                    }
                    $lists['list'][$key]['gsr_subject'] = implode(',', $sub_name);
                }
            }
        }

        return $lists;
    }

    public function insert($data) {

        if (!$data) {
            $data = $_POST;
        }

        $saveData = D('GradeSubjectRelation', 'Model')->create($data);
        if ($saveData === false) {
            $this->error = D('GradeSubjectRelation', 'Model')->getError();
            return false;
        }

        if ($saveData['gsr_id']) {
            // 修改
            $res = D('GradeSubjectRelation', 'Model')->update($saveData);
        } else {
            // 添加
            $res = D('GradeSubjectRelation', 'Model')->insert($saveData);
        }

        return $res;
    }
}