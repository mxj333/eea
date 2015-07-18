<?php
namespace School\Controller;
class NewsController extends BaseController {
    public function index() {
        // 获取学校信息
        $sConfig['s_id'] = I('s_id', 0, 'intval');

        // 要闻速递
        $article = $this->apiReturnDeal(getApi(array('belong'=>3, 'type'=>array(2), 's_id'=>$sConfig['s_id']), 'Article', 'lists'));      
        $this->assign('article', $article);
  
        //热点资讯
        $suData['art_designated_published'] = time();
        $suData['s_id'] = intval($sConfig['s_id']);
        $suConfig['type'] = intval(3);
        $suConfig['every_page_num'] = intval(10);
        $suConfig['is_deal_result'] = false;
        $suConfig['order'] = 'art_hits DESC';
        $articleHits = D('Article')->lists($suData, $suConfig);
        $this->assign('articleHits', $articleHits);


        // 广告列表
        $advertList = $this->apiReturnDeal(getApi(array('type' => '8', 'belong' => 3), 'Advert', 'getShows'));
        $this->assign('advertList', $advertList[8]);

        $this->display();
    }


    //详情页
    public function show() {
        // 获取学校信息
        $sConfig['s_id'] = 1;
        $art_id = I('art_id', 0, 'intval');


        $article = $this->apiReturnDeal(getApi(array('art_id' => $art_id), 'Article', 'shows'));
        $this->assign('article', $article);
        dump($article);

        $this->display();
    }

}