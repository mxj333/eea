<?php
namespace Reagional\Controller;
class IndexController extends ReagionalController {
    public function index(){
        
        // 总数
        $total_num = getApi(array('re_id' => session('re_id'), 'belong' => 2), 'Statistics', 'getCounts');
        $this->assign('total_num', $total_num);

        // 待审资源个数
        //$resource_pass_num = getApi(array('re_id' => session('re_id'), 'res_is_published' => 1, 'res_is_pass' => 0), 'Resource', 'counts');
        $this->assign('resource_pass_num', 20);

        parent::index('index');
    }

    // 用户新增统计
    public function memberAddRate() {
        $year = I('year', date('Y'), 'intval');
        $type = I('type', 1, 'intval');

        if ($type == 2) {
            // 同比统计图
            $starttime = ($year-1) . '-01-01 00:00:00';
            $endtime = $year . '-12-31 23:59:59';
        } else {
            // 增长表格
            $starttime = ($year-1) . '-12-01 00:00:00';
            $endtime = $year . '-12-31 23:59:59';
        }

        $member_list = getApi(array('re_id' => session('re_id'), 'starttime' => $starttime, 'endtime' => $endtime, 'way_type' => 'month'), 'Statistics', 'getMember');

        if ($type == 2) {
            $data1 = array();
            $data2 = array();

            for ($start = 1; $start < 13; $start++) {
                $month = $start < 10 ? '0' . $start : $start;
                // 上一年
                $data1[] = intval($member_list[($year-1) . '-' . $month]);
                // 本年
                $data2[] = intval($member_list[$year . '-' . $month]);
            } 
            
            // 加载
            Vendor('PHPOfcLibrary.open-flash-chart');
            
            $d = new \hollow_dot();  
            $d->size(1)->halo_size(0)->colour('#3D5C56'); 

            $line1 = new \line();
            $line1->set_default_dot_style($d);  
            $line1->set_colour('#D4D4D4');  
            $line1->set_values( $data1 );  
            $line1->attach_to_right_y_axis(); 

            $line2 = new \line();
            $line2->set_default_dot_style($d);  
            $line2->set_colour('#7171C6');  
            $line2->set_values( $data2 );  
            $line2->attach_to_right_y_axis(); 

            $max_val = max(max($data1), max($data2));
            if ($max_val > 10) {
                $sing_val = ceil($max_val/10);
            } else {
                $sing_val = 1;
            }
            $y = new \y_axis();
            $y->set_stroke( 3 );  
            $y->set_colour( '#3D5C56' );  
            $y->set_tick_length( 5 );
            $y->set_range( 0, $sing_val*10, $sing_val);
            $y->set_steps($sing_val);

            $y_r = new \y_axis_right();
            $y_r->set_stroke( 3 );  
            $y_r->set_colour( '#3D5C56' );  
            $y_r->set_tick_length( 5 );
            $y_r->set_range( 0, $sing_val*10, $sing_val);
            $y_r->set_steps($sing_val);

            $x = new \x_axis();  
            $x->set_labels_from_array(array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'));  

            $chart = new \open_flash_chart();
            $chart->set_bg_colour('#FFFFFF');
            $chart->add_element( $line1 );
            $chart->add_element( $line2 );
            $chart->set_y_axis( $y ); 
            $chart->set_y_axis_right($y_r); 
            $chart->set_x_axis( $x );

            echo $chart->toPrettyString();
        } else {
            // 用户新增统计
            $add_list = array();
            for ($start = 1; $start < 13; $start++) {
                // 上个月
                $prev_month = ($start - 1) ? ($start - 1) : 12;
                $prev_year = ($start - 1) ? $year : $year-1;
                $prev_month = $prev_month < 10 ? '0' . $prev_month : $prev_month;
                // 当前月
                $curr_month = $start < 10 ? '0' . $start : $start;
                // 百分比
                if (intval($member_list[$prev_year . '-' . $prev_month])) {
                    $percent = (intval($member_list[$year . '-' . $curr_month]) - intval($member_list[$prev_year . '-' . $prev_month])) * 100 / intval($member_list[$prev_year . '-' . $prev_month]);
                } else {
                    $percent = intval($member_list[$year . '-' . $curr_month]) ? '100' : '0';
                }

                $add_list[] = array(
                    'month' => $start,
                    'num' => intval($member_list[$year . '-' . $curr_month]),
                    'percent' => round($percent,2) . '%',
                );
            }
            echo json_encode($add_list);
        }

    }

    // 资源全国排行
    public function resourceRank() {
        $type = I('type', 1, 'intval');

        // 资源统计结果
        $resource_list = getApi(array('re_id' => 1, 'way' => 'region'), 'Statistics', 'getResource');
        // 按省份统计
        unset($resource_list['中国']);

        if ($type == 2) {
            // 统计图
        } else {
            // 表格
            echo json_encode($resource_list);
        }
    }

    // 应用好评率
    public function appGoodRate() {
        $app_list = getApi(array(), 'Statistics', 'getApp');

        echo json_encode($app_list);
    }

    // 应用好评
    public function appGoodNum() {
        $app_list = getApi(array('way' => 'number'), 'Statistics', 'getApp');
        
        $data1 = array();
        $data2 = array();
        $label = array();
        foreach ($app_list as $info) {
            $data1[] = intval($info['good']['num']);
            $data2[] = intval($info['general']['num']);
            $label[] = strval($info['good']['ac_title']);
        }

        // 加载
        Vendor('PHPOfcLibrary.open-flash-chart');

        $bar1 = new \bar(); 
        $bar1->set_colour('#5555FF');
        $bar1->set_values( $data1 );

        $bar2 = new \bar(); 
        $bar2->set_colour('#CCDDFF');
        $bar2->set_values( $data2 );

        $max_val = max(max($data1), max($data2));
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

        $chart = new \open_flash_chart();
        $chart->set_bg_colour('#FFFFFF');
        $chart->add_element( $bar1 );
        $chart->add_element( $bar2 );
        $chart->set_y_axis( $y ); 
        $chart->set_x_axis( $x );

        echo $chart->toPrettyString();
    }
}