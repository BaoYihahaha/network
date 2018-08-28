<?php
global $_W, $_GPC;

$id = intval($_GPC['id']);
if($id == 0)
{
	echo "无法访问";die;
}
//导航栏
$nav = pdo_fetchall("SELECT id, title, link FROM " . tablename('pc_navigation') . " WHERE is_show = 1 ORDER BY sort ASC");

//文章详情
$article_info = pdo_fetch("SELECT pn.id,pa.art_id, pa.title, pa.author, pa.detail, pa.addtime FROM " . 
	tablename('pc_article_type') . ' pat ' . 
	' LEFT JOIN ' . tablename('pc_article') . " pa ON (pa.art_id = pat.id) " . 
	' LEFT JOIN ' . tablename('pc_navigation') . " pn ON (pn.id = pat.nav_id) " . 
	"WHERE pa.id = $id");

//文章精选
$article_choice = pdo_fetchall("SELECT pa.id, pa.title FROM " . tablename('pc_article') . 
	' pa LEFT JOIN ' . tablename('pc_article_type') . " pat ON (pat.id = pa.art_id) " . 
	" WHERE pa.is_show = 1 AND pat.nav_id = $article_info[id] AND pa.id != $id ORDER BY RAND() LIMIT 5");

//热文推荐
$groom_choice = pdo_fetchall("SELECT pa.id, pa.thumb, pa.detail FROM " . 
    tablename('pc_page_plate') . ' pp ' . 
    ' LEFT JOIN ' . tablename('pc_article') . " pa ON (pa.pag_id = pp.id) " . 
    "WHERE pa.is_show = 1 AND pp.id = 13 LIMIT 4");
foreach ($groom_choice as $key => $value) {
    $groom_choice[$key]['detail'] = strip_tags($value['detail']);
    $groom_choice[$key]['thumb'] = '../attachment/'.$value['thumb'];
}

//详情页广告
$advert6 = pdo_fetch("SELECT thumb, link FROM " . tablename('pc_advertisement') . " WHERE id = 6");
$advert6['thumb'] = '../attachment/'.$advert6['thumb'];

//精选推送
$selected_push = pdo_fetchall("SELECT pa.id, pa.title as article_title, pa.thumb FROM " . 
	tablename('pc_page_plate') . ' pp ' . 
	' LEFT JOIN ' . tablename('pc_article') . " pa ON (pa.pag_id = pp.id) " . 
	"WHERE pa.is_show = 1 AND pp.id = 2");
foreach ($selected_push as $key => $value) {
        $selected_push[$key]['thumb']='../attachment/'.$value['thumb'];
    }

//热文推荐
$selected_groom = pdo_fetchall("SELECT pa.id, pa.title, pa.thumb, pa.addtime, pa.detail FROM " . 
	tablename('pc_page_plate') . ' pp ' . 
	' LEFT JOIN ' . tablename('pc_article') . " pa ON (pa.pag_id = pp.id) " . 
	"WHERE pa.is_show = 1 AND pp.id = 9 LIMIT 10");
foreach ($selected_groom as $key => $value) {
        $selected_groom[$key]['thumb'] = '../attachment/'.$value['thumb'];
        $selected_groom[$key]['detail'] = strip_tags(str_replace("&nbsp;","",htmlspecialchars_decode($value['detail'])));
    }  

//上一页
$front = pdo_fetch("SELECT id, title FROM " . tablename('pc_article') . 
	" WHERE id > $id AND is_show = 1 ORDER BY id ASC LIMIT 1");

//下一页 
$after = pdo_fetch("SELECT id, title FROM " . tablename('pc_article') . 
	" WHERE id < $id AND is_show = 1 ORDER BY id DESC LIMIT 1");   
include $this->template('article_info');