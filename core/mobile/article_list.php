<?php
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if($operation == 'article_list')
{
    $where ="";
    $art_id = !empty($_GPC['art_id']) ? $_GPC['art_id'] : '';
    if (!empty($_GPC['title'])) {
        $_GPC['title'] = trim($_GPC['title']);
        $where .= " AND a.title like '%{$_GPC['title']}%' ";
    }else{
    if($art_id == '')
    {
        $nav_id = intval($_GPC['nav_id']);
        $where.= " AND pat.nav_id = $nav_id";
    }
    else{
        $nav = pdo_fetch("SELECT nav_id, title FROM " . tablename('pc_article_type') . " WHERE id = $art_id");
        $nav_id = $nav['nav_id'];
        $where.= " AND a.art_id = $art_id ";
    }
    $count = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('pc_article_type') . " WHERE nav_id = $nav_id");
    //标题
    $article_title = pdo_fetch("SELECT id, title FROM " . tablename('pc_navigation') . " WHERE id = $nav_id");
    if($count >=7)
    {
        $article_type = pdo_fetchall("SELECT id, nav_id, title FROM " . tablename('pc_article_type') . " WHERE is_show = 1 AND nav_id = $nav_id LIMIT 6");
        $article_type_two = pdo_fetchall("SELECT id, nav_id, title FROM " . tablename('pc_article_type') . " WHERE nav_id = $nav_id AND is_show = 1 LIMIT 6,100");
    }
    else
    {
         $article_type = pdo_fetchall("SELECT id, nav_id, title FROM " . tablename('pc_article_type') . " WHERE is_show = 1 AND nav_id = $nav_id LIMIT 6");
    }
    }
    $pindex    = max(1, intval($_GPC['page']));
    $psize     = 20;
    $condition = " and article_list.uniacid=:uniacid";
    $params    = array(
        ':uniacid' => $_W['uniacid']
    );
    $total = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('pc_article') . 
        ' a LEFT JOIN ' . tablename('pc_article_type') . " pat ON (pat.id = a.art_id) " . 
        " WHERE a.is_show = 1 $where");
    $fenye = ceil($total/$psize);
    $page = intval($_GPC['page']);
    if($fenye == $page && $total > 0)
    {
        $sql .= " limit " . ($total-$psize) . ',' . $total;
    }else
    {
        $sql .= " limit " . ($pindex - 1) * $psize . ',' . $psize;
    }
    $list = pdo_fetchall("SELECT a.id, a.title, a.author, a.addtime FROM " . 
        tablename('pc_article') . ' a LEFT JOIN ' . tablename('pc_article_type') . 
        " pat ON (pat.id = a.art_id) " . " WHERE a.is_show = 1 $where $sql ");
    foreach ($list as $key => $value) {
        $list[$key]['addtime'] = date("Y-m-d h:i",$value['addtime']);
    }
    $pager = pagination($total, $pindex, $psize);
}

//导航栏
$nav = pdo_fetchall("SELECT id, title, link FROM " . tablename('pc_navigation') . " WHERE is_show = 1 ORDER BY sort ASC");

//全站置顶
$set_top = pdo_fetchall("SELECT id, title, author, addtime FROM " . tablename('pc_article') . " WHERE is_stick = 1 AND is_show = 1 LIMIT 0,3");
foreach ($set_top as $key => $value) {
    $set_top[$key]['addtime'] = date('Y-m-d h:i',$value['addtime']);
}

//猜你喜欢
$lovely = pdo_fetchall("SELECT pa.id, pa.title, pa.thumb, pa.detail FROM " . 
    tablename('pc_page_plate') . ' pp ' . 
    ' LEFT JOIN ' . tablename('pc_article') . " pa ON (pa.pag_id = pp.id) " . 
    "WHERE pa.is_show = 1 AND pp.id = 7 LIMIT 8");
foreach ($lovely as $key => $value) {
    $lovely[$key]['detail'] = strip_tags($value['detail']);
    $lovely[$key]['thumb'] = '../attachment/'.$value['thumb'];
}

//最近更新
$updates = pdo_fetchall("SELECT pa.id, pa.title, pa.thumb, pa.detail FROM " . 
    tablename('pc_page_plate') . ' pp ' . 
    ' LEFT JOIN ' . tablename('pc_article') . " pa ON (pa.pag_id = pp.id) " . 
    "WHERE pa.is_show = 1 AND pp.id = 8 LIMIT 0,3");
foreach ($updates as $key => $value) {
    $updates[$key]['detail'] = strip_tags(str_replace("&nbsp;","",htmlspecialchars_decode($value['detail'])));
    $updates[$key]['thumb'] = '../attachment/'.$value['thumb'];
}

//热门标签上的广告
$advert5 = pdo_fetch("SELECT thumb, link FROM " .tablename('pc_advertisement') . " WHERE id = 5");
$advert5['thumb'] = '../attachment/'.$advert5['thumb'];

//热门标签
$hot_label = pdo_fetchall("SELECT title, link FROM " . tablename('pc_hot_label') . " WHERE is_show = 1 LIMIT 10");

//精选推送
$selected_push = pdo_fetchall("SELECT pa.id, pa.title, pa.thumb FROM " . 
    tablename('pc_page_plate') . ' pp ' . 
    ' LEFT JOIN ' . tablename('pc_article') . " pa ON (pa.pag_id = pp.id) " . 
    "WHERE pa.is_show = 1 AND pp.id = 9 LIMIT 10");
foreach ($selected_push as $key => $value) {
    $selected_push[$key]['thumb'] = '../attachment/'.$value['thumb'];
}

//及时推荐
$timely = pdo_fetchall("SELECT id, title, addtime FROM " . tablename('pc_article') . " WHERE is_show = 1 AND id >= (SELECT floor(RAND() * (SELECT MAX(id) FROM " . tablename('pc_article') . " ))) ORDER BY id LIMIT 12");
foreach ($timely as $key => $value) {
    $timely[$key]['addtime'] = date("h:i",$value['addtime']);
}
include $this->template('article_list');