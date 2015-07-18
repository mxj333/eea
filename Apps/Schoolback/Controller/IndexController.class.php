<?php
namespace Schoolback\Controller;
class IndexController extends SchoolbackController {
    public function index(){
        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . '.css'));
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . '.js'));
        
        // 总数
        $total_num = getApi(array('s_id' => session('s_id'), 'belong' => 3, 'type' => 'member,resource,article,class'), 'Statistics', 'getCounts');
        $this->assign('total_num', $total_num);

        parent::index('index');
    }

    // 统计
    public function getStatistics() {
        $type = I('request.type', 1, 'intval'); // 数据种类
        $sType = I('request.sType', 1, 'intval'); // 统计图类型
        $data = $this->getData($type);

        echo $this->getChart($data, $sType);
    }

    // 首页统计数据
    private function getData($type = 1) {
        $return = array();

        // 统计数据
        switch ($type) {
            case 2:
                // 资源统计
                $result = getApi(array('s_id' => session('s_id'), 'way' => 'subject'), 'Statistics', 'getResource');
                if ($result) {
                    $tag = reloadCache('tag');
                    foreach ($result as $key => $total) {
                        $return[] = array(
                            'x_name' => strval($tag[5][$key]),
                            'num' => intval($total),
                        );
                    }
                }
                break;
            case 3:
                // 资讯
                $result = getApi(array('s_id' => session('s_id')), 'Statistics', 'getArticle');
                if ($result) {
                    $tag = getApi(array('fields' => 'ca_id,ca_title', 's_id' => session('s_id'), 'is_page' => false), 'Category', 'lists');
                    foreach ($result as $key => $total) {
                        $return[] = array(
                            'x_name' => strval($tag['list'][$key]),
                            'num' => intval($total),
                        );
                    }
                }
                break;
            case 4:
                // 班级
                $result = getApi(array('s_id' => session('s_id')), 'Statistics', 'getClass');
                if ($result) {
                    $tag = reloadCache('tag');
                    foreach ($result as $key => $total) {
                        $return[] = array(
                            'x_name' => strval($tag[7][$key]),
                            'num' => intval($total),
                        );
                    }
                }
                break;
            default:
                // 用户统计
                $result = getApi(array('s_id' => session('s_id'), 'way' => 'type'), 'Statistics', 'getMember');
                if ($result) {
                    $tag = explode(',', C('MEMBER_TYPE'));
                    foreach ($result as $key => $total) {
                        $return[] = array(
                            'x_name' => strval($tag[$key]),
                            'num' => intval($total),
                        );
                    }
                }
        }

        return $return;
    }

    // 统计图 json 字符串
    private function getChart($data, $type = 1) {

        $data1 = array();
        $label = array();
        foreach ((array)$data as $key => $value) {
            $data1[] = $value['num'];
            $label[] = $value['x_name'];
            $dis_value[] = array('value' => $value['num'], 'label' => $value['x_name']);
        }

        // 加载
        Vendor('PHPOfcLibrary.open-flash-chart');

        if ($type == 2) {
            // 饼状图
            $s1 = new \pie(); 
            $s1->set_alpha(0.6); 
            $s1->set_start_angle( 32 ); 
            $s1->add_animation( new \pie_fade() );
            $s1->set_tooltip( '#val# #percent#' ); 
            //$s1->set_tooltip( '#val# of #total##percent# of 100%' ); 
            $s1->set_colours(array('#1C9E05','#FF368D','#0099cc','#d853ce','#ff7400','#006e2e', 
            '#d15600','#4096ee','#c79810'));
            $s1->set_values( $dis_value );
        } else {
            // 柱状图
            $s1 = new \bar(); 
            $s1->set_colour('#5555FF');

            // 柱状图小于 4个 时 补齐4个
            $bar_num = count($data1);
            if ($bar_num < 4) {
                $bu_num = 4 - $bar_num;
                $data1 = array_pad($data1, 4, 0);
                $label = array_pad($label, 4, '');
            }
            $s1->set_values( $data1 );
        }

        $chart = new \open_flash_chart();
        $chart->set_bg_colour('#FFFFFF');
        $chart->add_element( $s1 );

        if ($type != 2) {
            // 饼状图不需要以下属性
            $max_val = max($data1);
            if ($max_val > 10) {
                $sing_val = ceil($max_val/10);
            } else {
                $sing_val = 1;
            }
            $y = new \y_axis(); 
            $y->set_range( 0, $sing_val*10, $sing_val);
            $y->set_stroke(3);
            $y->set_colour('#3D5C56');
            $y->set_tick_length(5);

            $x = new \x_axis();
            $x->set_labels_from_array($label);
        
            $chart->set_y_axis( $y ); 
            $chart->set_x_axis( $x );
        }

        return $chart->toPrettyString();
    }
}