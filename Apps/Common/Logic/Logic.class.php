<?php
namespace Common\Logic;
use Think\Model;
class Logic extends Model {

    /*
     * getListByPage
     * 根据页码获取列表
     * @config array
               + string $table 表名
               + string $order 排序
               + array $where 条件 默认为array()
               + int $every_page_num 每页显示数量 默认为10
               + int $is_ajax 是否AJAX
     * @return array $result 数组
     *         + array $result['list'] 结果集
               + string $result['page'] 分页
     */
    public function getListByPage($config = array()) {

        $default = array(
            'order' => '',
            'where' => '1',
            'every_page_num' => C('EVERY_PAGE_NUM'),
            'is_ajax' => 1,
            'p' => intval($_GET['p'])? intval($_GET['p']) : 1,
            'fields' => '*',
            'is_page' => 1,
        );

        $config = array_merge($default, $config);

        if (!$config['is_page']) {
            $result['list'] = D($this->name, 'Model')->getAll($config);
            return $result;
        }

        // 要返回的数组
        $result = array();
        $everyPageMaxNum = intval(C('EVERY_PAGE_MAX_NUM'));
        if ($config['every_page_num'] > $everyPageMaxNum) {
            $config['every_page_num'] = $everyPageMaxNum;
        }

        // 初始化参数
        $_GET['p'] = $config['p'];
        $count = D($this->name, 'Model')->total($config);

        // 获取总页数
        $regNum = ceil($count / $config['every_page_num']);

        // 验证当前请求页码是否大于总页数
        if ($_GET['p'] > $regNum) {
            return $result;
        }
        if (intval($config['is_ajax'])) {
            $Page = new \Think\AjaxPage($count, $config['every_page_num']);
        } else {
            $Page = new \Think\Page($count, $config['every_page_num']);
        }

        $config['limit'] = $Page->firstRow . ',' . $Page->listRows;
        $result['page'] = trim($Page->show());
        $result['list'] = (array)D($this->name, 'Model')->getAll($config);

        return $result;
    }

    /*
     * getDataByArray
     * 通过关联数组获取数据
     * @param string $table 表名
     * @param array $array 数组
     * @param string $arrayField 数组的字段
     * @param string $getField 要获取的字段
     *
     * @return array $result 获取的数据
     *      使用参考：通过活动获取对应的课时列表,传递M(课时), 活动数组及课时ID字段
     */
    public function getDataByArray($array, $arrayField, $getField = '*') {
        $result = array();
        $config['where'][$arrayField] = array('IN', getValueByField($array, $arrayField));
        $config['fields'] = $getField;
        $result = D($this->name, 'Model')->getAll($config);
        return setArrayByField($result, $arrayField);
    }

    public function getAll($config = array()) {
        $default = array(
            'order' => '',
            'where' => '1',
            'fields' => '*',
            'limit' => '',
        );

        $config = array_merge($default, $config);
        return D($this->name, 'Model')->getAll($config);
    }

    public function total($config = array()) {

        return D($this->name, 'Model')->total($config);
    }

    public function getOne($config) {

        return D($this->name, 'Model')->getOne($config);
    }

    public function update($data, $config = array()) {

        if (!$data) {
            return 0;
        }

        return D($this->name, 'Model')->update($data, $config);
    }

    public function delete($config) {

        if (!$config) {
            return 0;
        }
        return D($this->name, 'Model')->delete($config);
    }

    public function insert($data) {

        if (!$data) {
            return 0;
        }

        return D($this->name, 'Model')->insert($data);
    }

    // 控制器写方法 转移到 logic 层
    public function data($table = '', $data = array()) {

        // 如果table有值则替换当前控制器mod
        $table = empty($table) ? $this->name : $table;
        $model = D($table, 'Model');

        if (!$data) {
            $data = $_POST;
        }

        $save_data = $model->create($data);
        if (false === $save_data) {
            $this->error = $model->getError();
            return false;
        }

        $pk = $model->getPk();
        if ($data[$pk]) {
            return $model->update($save_data);
        } else {
            unset($save_data[$pk]);
            $_POST[$pk] = $model->insert($save_data);
            return $_POST[$pk];
        }
    }

    public function insertAll($config) {

        if (!$config['values']) {
            return 0;
        }

        return D($this->name, 'Model')->insertAll($config);
    }

    public function getSortList($id = '') {
        // 获取主键
        $pk = D($this->name)->getPk();
        $pre = substr($pk, 0, strpos($pk, '_')) . '_';

        $config['where'] = array($pre . 'status' => 1);

        if($id) {
            $config['where'] = array($pk => array('IN', $id));
        }

        $config['order'] = $pre . 'sort ASC';

        // 获取数据
        return D($this->name, 'Model')->getAll($config);
    }

    public function getById($id = '', $config = array()) {
        // 获取主键
        $pk = D($this->name)->getPk();

        $default['where'][$pk] = intval($id);

        $config = array_merge($default, $config);

        // 获取数据
        return D($this->name, 'Model')->getOne($config);
    }

    public function dealImage($old_file_name, $file_name = '', $config = array(), $water = true) {
        $thumb_list = explode(',', C('THUMB_LIST'));
        $file_name = $file_name ? $file_name : $old_file_name;

        if (!$thumb_list) {
            // 切图尺寸不存在 直接走默认 缩略图、水印
            image($old_file_name, $file_name, $config, $water);
            return true;
        }

        $path_info = get_path_info($file_name);

        $size = array('s', 'm', 'b');
        foreach ($thumb_list as $key => $thumb_size) {

            image($old_file_name, '.' . $path_info['path'] . '/' . $path_info['name'] . '_' . $size[$key] . '.' . $path_info['ext'], array('width' => $thumb_size, 'height' => $thumb_size), $config, $water);

            if ($key >= 2) {
                // 只切三种图
                break;
            }
        }
    }

    public function deleteImage($file_name) {

        $path_info = get_path_info($file_name);

        // 删除 原图
        @unlink($file_name);

        // 删除 大图
        @unlink('.' . $path_info['path'] . '/' . $path_info['name'] . '_b.' . $path_info['ext']);

        // 删除 中图
        @unlink('.' . $path_info['path'] . '/' . $path_info['name'] . '_m.' . $path_info['ext']);

        // 删除 小图
        @unlink('.' . $path_info['path'] . '/' . $path_info['name'] . '_s.' . $path_info['ext']);
    }

    public function dealValueDelimiter($string, $last = false) {
        if (!$string) {
            return '';
        }
        // -1-2  过滤 第一个 -
        if ($string[0] == '-') {
            $string = substr($string, 1);
        }
        // 1-2-0  过滤 -0
        if (substr($string, -2) == '-0') {
            $string = substr($string, 0, -2);
        }

        // 是否返回末级id
        if ($last) {
            return array_pop(explode('-', $string));
        }

        return $string;
    }

    // 在已知添加值时  处理 平台、区域、学校 字段的值
    public function dealTypeValue($value, $type = 1, $default = '') {

        if ($default === '' || $default === NULL) {
            $default = '999';
        }

        if ($type == 3) {
            //  学校
            $string = substr($default, 0, 2) . intval($value);
        } elseif ($type == 2) {
            // 区域
            $string = $default[0] . intval($value) . $default[2];
        } else {
            $string = intval($value) . substr($default, 1, 2);
        }
        return $string;
    }

    // 在未知添加值时  处理 平台、区域、学校 字段的值
    public function dealTypeFields($value, $type = 1, $fields = '') {

        if (!$fields) {
            return false;
        }

        if ($type == 3) {
            //  学校
            $string = "CONCAT(SUBSTRING(" . $fields . " FROM 1 FOR 2), '" . $value . "')";
        } elseif ($type == 2) {
            // 区域
            $string = "CONCAT(SUBSTRING(" . $fields . " FROM 1 FOR 1), '" . $value . "', SUBSTRING(" . $fields . " FROM 3 FOR 1))";
        } else {
            $string = "CONCAT('" . $value . "', SUBSTRING(" . $fields . " FROM 2 FOR 2))";
        }

        return $string;
    }
}