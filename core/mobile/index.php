<?php
global $_W, $_GPC;
//首页基础模块查询
//查询导航栏
$apido = $_GPC['apido'];
$nav = pdo_fetchall("SELECT id, title, link FROM " . tablename('pc_navigation') . " WHERE is_show = 1 ORDER BY sort ASC");
//轮播图
$sowing = pdo_fetchall("SELECT title, thumb, link FROM " . tablename('pc_sowing') . " WHERE is_show = 1 ORDER BY sort ASC");
foreach ($sowing as $key => $value) {
        $sowing[$key]['thumb']='../attachment/'.$value['thumb'];
    }
//今日推荐
$recommend = pdo_fetchall("SELECT pa.id, pa.title as article_title, pat.id as art_id, pat.title FROM " . 
	tablename('pc_page_plate') . ' pp ' . 
	' LEFT JOIN ' . tablename('pc_article') . " pa ON (pa.pag_id = pp.id) " . 
	' LEFT JOIN ' . tablename('pc_article_type') . " pat ON (pat.id = pa.art_id) " . 
	"WHERE pa.is_show = 1 AND pp.id = 1 LIMIT 0,7");

//热门帖子推荐
$hot_post = pdo_fetchall("SELECT pa.id,pa.title as article_title, pat.title, pat.id as art_id, pa.author FROM " . tablename('pc_page_plate') . ' pp ' . 
	' LEFT JOIN ' . tablename('pc_article') . " pa ON (pa.pag_id = pp.id) " . 
	' LEFT JOIN ' . tablename('pc_article_type') . " pat ON (pat.id = pa.art_id) " . 
	"WHERE pa.is_show = 1 AND pp.id = 2 LIMIT 0,10");

//热门帖子下的两张图片
$hot_img = pdo_fetchall("SELECT pa.id, pa.title, pa.thumb FROM " . 
	tablename('pc_page_plate') . ' pp ' . 
	' LEFT JOIN ' . tablename('pc_article') . " pa ON (pa.pag_id = pp.id) " . 
	' LEFT JOIN ' . tablename('pc_article_type') . " pat ON (pat.id = pa.art_id) " . 
	"WHERE pa.is_show = 1 AND pp.id = 12 LIMIT 0,2");
foreach ($hot_img as $key => $value) {
        $hot_img[$key]['thumb']='../attachment/'.$value['thumb'];
    }

//广告两个
$advert1 = pdo_fetch("SELECT thumb, link FROM " . tablename('pc_advertisement') . " WHERE id = 1");
$advert1['thumb'] = '../attachment/'.$advert1['thumb'];
$advert2 = pdo_fetch("SELECT thumb, link FROM " . tablename('pc_advertisement') . " WHERE id = 2");
$advert2['thumb'] = '../attachment/'.$advert2['thumb'];

//热门系统
$hot_plate = pdo_fetchall("SELECT id, title, thumb FROM " . tablename('pc_article_type') . " WHERE is_hot = 1 AND is_show = 1 ORDER BY sort ASC");
foreach ($hot_plate as $key => $value) {
        $hot_plate[$key]['thumb']='../attachment/'.$value['thumb'];
    }

//最新咨询
$newest = pdo_fetchall("SELECT pa.id, pa.title, pa.author, pa.addtime FROM " . 
	tablename('pc_page_plate') . ' pp ' . 
	' LEFT JOIN ' . tablename('pc_article') . " pa ON (pa.pag_id = pp.id) " . 
	"WHERE pa.is_show = 1 AND pp.id = 6 LIMIT 0,10");

//排行榜默认--点击排行
$click_rank = pdo_fetchall("SELECT pa.id, pa.title, pa.author, pa.thumb, pa.addtime FROM " . 
	tablename('pc_page_plate') . ' pp ' . 
	' LEFT JOIN ' . tablename('pc_article') . " pa ON (pa.pag_id = pp.id) " . 
	"WHERE pa.is_show = 1 AND pp.id = 3 LIMIT 0,17");
foreach ($click_rank as $key => $value) {
        $click_rank[$key]['thumb']='../attachment/'.$value['thumb'];
    }

//排行榜--观看排行
$watch_rank = pdo_fetchall("SELECT pa.id, pa.title, pa.author, pa.thumb, pa.addtime FROM " . 
	tablename('pc_page_plate') . ' pp ' . 
	' LEFT JOIN ' . tablename('pc_article') . " pa ON (pa.pag_id = pp.id) " . 
	"WHERE pa.is_show = 1 AND pp.id = 4 LIMIT 0,17");
foreach ($watch_rank as $key => $value) {
        $watch_rank[$key]['thumb']='../attachment/'.$value['thumb'];
    }

//排行榜--分享排行
$share_rank = pdo_fetchall("SELECT pa.id, pa.title, pa.author, pa.thumb, pa.addtime FROM " . 
	tablename('pc_page_plate') . ' pp ' . 
	' LEFT JOIN ' . tablename('pc_article') . " pa ON (pa.pag_id = pp.id) " . 
	"WHERE pa.is_show = 1 AND pp.id = 5 LIMIT 0,17");
foreach ($share_rank as $key => $value) {
        $share_rank[$key]['thumb']='../attachment/'.$value['thumb'];
    }  

//排行榜下方广告
$advert3 = pdo_fetch("SELECT thumb, link FROM " . tablename('pc_advertisement') . " WHERE id = 3");
$advert3['thumb'] = '../attachment/'.$advert3['thumb'];

//干货
$dry_cargo = pdo_fetchall("SELECT pat.title, pa.id, pa.title as article_title,pat.id as art_id FROM " . 
	tablename('pc_article_type') . ' pat ' . 
	' LEFT JOIN ' . tablename('pc_article') . " pa ON (pa.art_id = pat.id) " . 
	"WHERE pa.is_show = 1 AND pat.id IN ('7','8','9') ORDER BY RAND() LIMIT 15");

//干货分享上方广告
$advert4 = pdo_fetch("SELECT thumb, link FROM " . tablename('pc_advertisement') . " WHERE id = 4");
$advert4['thumb'] = '../attachment/'.$advert4['thumb'];

//热门标签
$hot_label = pdo_fetchall("SELECT title, link FROM " . tablename('pc_hot_label') . " WHERE is_show = 1 LIMIT 10");

//今日热搜排行
$hot_search = pdo_fetchall("SELECT pa.id,pa.title FROM " . 
	tablename('pc_page_plate') . ' pp ' . 
	' LEFT JOIN ' . tablename('pc_article') . " pa ON (pa.pag_id = pp.id) " . 
	"WHERE pa.is_show = 1 AND pp.id = 10 LIMIT 0,10");
if ($_W['isajax'] && $_W['ispost']) 
{
	$limit = $_GPC['limit'];
	if (!empty($limit)) {
		$limit = " LIMIT 4,10 ";
	}else{
		$limit = " LIMIT 0,4 ";
	}
	$selected_push = pdo_fetchall("SELECT pa.id,pa.title,pa.addtime,pa.thumb,pa.detail FROM " . 
	tablename('pc_page_plate') . ' pp ' . 
	' LEFT JOIN ' . tablename('pc_article') . " pa ON (pa.pag_id = pp.id) " . 
	"WHERE pa.is_show = 1 AND pp.id = 9 ". $limit);
	foreach ($selected_push as $key => $value) 
	{
		$selected_push[$key]['thumb'] = '../attachment/'.$value['thumb'];
		$selected_push[$key]['addtime'] = date('Y-m-d',$value['addtime']);
		$selected_push[$key]['detail'] = strip_tags(str_replace("&nbsp;","",htmlspecialchars_decode($value['detail'])));
	}
	show_json($selected_push);
}
include $this->template('index');