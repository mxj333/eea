<?php
namespace School\Controller;
class ResourceController extends BaseController {
    public function search() {
        set_time_limit(120);

        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Search.css'));
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Search.js'));

        // 筛选条件
        $tag = reloadCache('tag');
        $this->assign('tag', $tag);
        $resType = reloadCache('resourceType');
        $this->assign('resType', $resType);
        $resCate = reloadCache('resourceCategory');
        $this->assign('resCate', $resCate);

        // 根据参数查询 检索结果
        $resourceList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 3, 'every_num' => 10, 'is_page' => true, 'page' => $this->current_param['p'], 's_id' => $this->sInfo['s_id'], 'keywords' => $keywords), 'Resource', 'getShows'));
        $this->assign('resourceList', $resourceList);

        // 右侧热门
        $hotList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 3, 'every_num' => 10, 'is_page' => true, 'order' => 'res_hits DESC,res_created DESC', 's_id' => $this->sInfo['s_id']), 'Resource', 'getShows'));
        $this->assign('hotList', $hotList);

        // 右侧最新
        $newList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 3, 'every_num' => 10, 'is_page' => true, 'order' => 'res_created DESC,res_id DESC', 's_id' => $this->sInfo['s_id']), 'Resource', 'getShows'));
        $this->assign('newList', $newList);
        
        $this->assign('search_keywords_link', getUrlAddress(array('res_title' => 'keywords'), 'search', 'resource'));

        $this->display();
    }

    // 检索页面 返回数据
    public function getData() {
        
        // 学制
        $schoolType = I('st_id', 0, 'intval');
        // 学科
        $subject = I('su_id', 0, 'intval');
        // 年级
        $grade = I('gr_id', 0, 'intval');
        // 类型
        $category = I('ca_id', 0, 'intval');
        // 格式
        $type = I('ty_id', 0, 'intval');

        // 排序字段
        $order = I('order', 'time', 'strval');
        // 排序
        $sort = I('sort', 'down', 'strval');

        // 关键词
        $keywords = I('keywords', '', 'strval');

        // 分页
        $page = I('page', 1, 'intval');

        // 排序
        $order = strval($order) ? strval($order) : 'time';
        $orderConfig = array('time' => 'res_created', 'comment' => 'res_comment_count', 'download' => 'res_downloads');
        $orderField = $orderConfig[$order] ? $orderConfig[$order] : 'res_created';
        // 顺序
        $sortOrder = $sort == 'up' ? 'ASC' : 'DESC';

        $config['type'] = 3;
        $config['belong'] = 3;
        $config['every_num'] = 10;
        $config['is_page'] = true;
        $config['page'] = $page;
        $config['school_type'] = $schoolType;
        $config['subject'] = $subject;
        $config['grade'] = $grade;
        $config['category'] = $category;
        $config['rt_id'] = $type;
        $config['s_id'] = $this->sInfo['s_id'];
        $config['order'] = $orderField . ' ' . $sortOrder;
        $config['keywords'] = $keywords;
        $resourceList = $this->apiReturnDeal(getApi($config, 'Resource', 'getShows'));

        $return = array();
        $return['page'] = $resourceList[3]['page'];
        // 数据拼成html
        foreach ($resourceList[3]['list'] as $res_info) {
            $return['list'] .= $this->dataToHtml($res_info);
        }
        echo json_encode($return);
    }

    // 列表 html 页面
    private function dataToHtml($info) {
        
        $html = '<li class="re_item">
                    <a class="fl" href="' . getUrlAddress($info, 'detail', 'resource') . '">
                        <img width="80" border="0" height="80" src="' . $info['res_image'] . '" alt="' . $info['res_title'] . '" title="' . $info['res_title'] . '">
                    </a>
                    <div>
                        <a href="' . getUrlAddress($info, 'detail', 'resource') . '">' . getShortTitle($info['res_title'], 20) . '</a>
                        <p><span>' . $info['rc_title'] . '</span>/' . $info['school_type'] . '/' . $info['grade'] . '/' . $info['subject'] . '</p><p>浏览：<span>' . intval($info['res_hits']) . '</span>次&nbsp;&nbsp;&nbsp;&nbsp;下载：<span>' . intval($info['res_downloads']) . '</span>次&nbsp;&nbsp;评论：<span>' . intval($info['res_comment_count']) . '</span>次&nbsp;&nbsp;智慧豆：' . intval($info['res_download_points']) . '个</p>
                    </div>
                    <p>';
        if ($info['res_score_num']) {
            $average = round($info['res_score_num']/$info['res_score_count']);
        }
        for ($i = 0; $i < $average; $i++) {
            $html .= '<img src="' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Images/star1.png">';
        }
        for ($i = 0; $i < 5-$average; $i++) {
            $html .= '<img src="' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Images/star2.png">';
        }
        $html .= '<span>' . intval($average) . '分</span>
                    </p>
                </li>';

        return $html;
    }

    // 详情页
    public function detail() {
        set_time_limit(120);

        $this->everyModelCss = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Css/' . strtolower(CONTROLLER_NAME) . 'Detail.css'));
        $this->everyModelJs = intval(file_exists('.' . MPUBLIC_NAME . '/' . C('DEFAULT_THEME') . '/Js/model/' . strtolower(CONTROLLER_NAME) . 'Detail.js'));

        // 获取资源信息
        $vo = $this->apiReturnDeal(getApi(array('res_id' => $this->current_param['res_id'], 'is_deal_result' => true), 'Resource', 'shows'));
        /*if (substr($vo['res_is_eliminated'], 1, 1) == 1) {
            $this->error('资源不存在');
        }
        if ($vo['res_is_published'] != 1) {
            $this->error('资源未发布');
        }
        if ($vo['res_is_pass'] != 1) {
            $this->error('资源审核中');
        }
        if ($vo['res_valid'] > time() && $vo['res_avaliable'] < time()) {
            $this->error('资源已失效');
        }*/
        $this->assign('vo', $vo);

        // 阅读数
        getApi(array('res_id' => $this->current_param['res_id']), 'Resource', 'increase');

        // 相关文章
        $relationList = $this->apiReturnDeal(getApi(array('type' => '3', 'belong' => 3, 'every_num' => 10, 'is_page' => true, 'page' => 1, 'subject' => $vo['res_subject'], 's_id' => $this->sInfo['s_id']), 'Resource', 'getShows'));
        $this->assign('relationList', $relationList);

        // 格式类型
        $this->assign('res_type', reloadCache('resourceType'));

        $this->display();
    }

    // 下载文件
    public function download() {

        // TO DO 验证是否登录

        $res_id = intval($this->current_param['res_id']);
        if (!$res_id) {
            echo '参数错误';
            exit;
        }

        $info = $this->apiReturnDeal(getApi(array('res_id' => $res_id, 'is_deal_result' => true), 'Resource', 'shows'));
        /*if (substr($info['res_is_eliminated'], 1, 1) == 1) {
            $this->error('资源不存在');
        }
        if ($info['res_is_published'] != 1) {
            $this->error('资源未发布');
        }
        if ($info['res_is_pass'] != 1) {
            $this->error('资源审核中');
        }
        if ($info['res_valid'] > time() && $info['res_avaliable'] < time()) {
            $this->error('资源已失效');
        }*/

        // TO DO 是否需要智慧豆   用户账号减豆,创建人加豆   记录日志

        if (!$info['res_original_path']) {
            echo '文件不存在';
            exit;
        }

        // TO DO 记录下载日志

        // 下载次数加 1
        getApi(array('res_id' => $this->current_param['res_id'], 'type' => 1), 'Resource', 'increase');

        header("Content-type:" . getContentType($info['rf_ext']) . ";charset=utf-8");
        header("Content-Disposition: attachment; filename=".$info['res_title'].'.'.$info['rf_ext']);
        @readfile($info['res_original_path']);
        exit;
    }

    public function getDirectory() {
        // 课文目录
        $d_id = I('request.id', 0, 'intval');
        if ($d_id) {
            $d_info = $this->apiReturnDeal(getApi(array('d_id' => $d_id), 'Directory', 'shows'));
            $config['d_pid'] = $d_id;
            $config['d_level'] = $d_info['d_level'] + 1;
        } else {
            $config['d_level'] = 0;
        }
        $config['is_upper_level'] = true;
        $config['is_page'] = false;
        //$config['is_open_sub'] = true;
        $directoryList = $this->apiReturnDeal(getApi($config, 'Directory', 'lists'));
        $tag = reloadCache('tag');
        foreach ($directoryList['list'] as $dir_key => $dir_val) {
            if ($dir_val['d_level'] == 0) {
                $directoryList['list'][$dir_key]['d_title'] = $tag[6][$dir_val['d_version']];
            } elseif ($dir_val['d_level'] == 1) {
                $directoryList['list'][$dir_key]['d_title'] = $tag[4][$dir_val['d_school_type']];
            } elseif ($dir_val['d_level'] == 2) {
                $directoryList['list'][$dir_key]['d_title'] = $tag[7][$dir_val['d_grade']];
            } elseif ($dir_val['d_level'] == 3) {
                $directoryList['list'][$dir_key]['d_title'] = $tag[8][$dir_val['d_semester']];
            } elseif ($dir_val['d_level'] == 4) {
                $directoryList['list'][$dir_key]['d_title'] = $tag[5][$dir_val['d_subject']];
            }
        }
        
        // 课文目录转树结构
        header("Content-Type:text/xml; charset=utf-8");
        $xml  = '<?xml version="1.0" encoding="utf-8" ?>'."\n";
        $xml .= '<tree caption="课文目录" id="0">'."\n";
        $xml .= html_entity_decode(toTree(tree($directoryList['list'], 'd_id', 'd_pid', 'list', $d_id), 'd_id', 'd_pid', 'd_level', 'd_title', 'list'));
        $xml .= '</tree>';
        exit($xml);
    }

    public function getKnowledge() {
        $kp_id = I('request.id', 0, 'intval');
        if ($kp_id) {
            $config['kp_pid'] = $kp_id;
        } else {
            $config['kp_pid'] = 0;
        }
        $config['is_page'] = false;
        // 知识点
        $knowledgeList = $this->apiReturnDeal(getApi($config, 'KnowledgePoints', 'lists'));
        // 知识点转树结构
        header("Content-Type:text/xml; charset=utf-8");
        $xml  = '<?xml version="1.0" encoding="utf-8" ?>'."\n";
        $xml .= '<tree caption="知识点" id="0">'."\n";
        $xml .= html_entity_decode(toTree(tree($knowledgeList['list'], 'kp_id', 'kp_pid', 'list', $kp_id), 'kp_id', 'kp_pid', 'kp_level', 'kp_title', 'list'));
        $xml .= '</tree>';
        exit($xml);
    }
}