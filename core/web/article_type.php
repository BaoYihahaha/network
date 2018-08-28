<?php
global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
    //所属导航栏
    $navigation = pdo_fetchall('select n.id, n.title from ' . tablename('pc_article_type') . ' at left join ' . tablename('pc_navigation') . " n on (n.id=at.nav_id) " . " GROUP BY at.nav_id");

    if (!empty($_GPC["sort"])) { foreach ($_GPC["sort"] as $id => $sort) { pdo_update("pc_article_type", array("sort" => $sort), array("id" => $id)); } plog("network.article_type.edit","批量修改文章类型排序"); message("文章类型更新成功！", $this->createPluginWebUrl('network',array('method'=>'article_type','op'=>'display')), "success"); 
    } 

    $pindex    = max(1, intval($_GPC['page']));
    $psize     = 15;
    $condition = " and article_type.uniacid=:uniacid";
    $params    = array(
        ':uniacid' => $_W['uniacid']
    );


    $condition = 'WHERE 1';
     if (!empty($_GPC['title'])) {
        $_GPC['title'] = trim($_GPC['title']);
        $condition .= " AND at.title like '%{$_GPC['title']}%' ";
    }
    if (!empty($_GPC['nav_id'])) {
        $condition .= ' and at.nav_id = ' . intval($_GPC['nav_id']);
    }
    if($_GPC['is_hot'] == 1){
        $condition .= ' and at.is_hot = ' . intval($_GPC['is_hot']);
    }
    if($_GPC['is_hot'] == 2){
        $condition .= ' and at.is_hot = 0';
    }
     if($_GPC['is_hot'] == 3){
        $_GPC['is_hot'] = 3;
        $condition .= '';
    }
    
    if (empty($_GPC['export'])) {

        $sql .= " limit " . ($pindex - 1) * $psize . ',' . $psize;

    }

    $list = pdo_fetchall('select at.*,n.title as nav_title from ' . tablename('pc_article_type') . ' at left join ' . tablename('pc_navigation') . " n on (n.id=at.nav_id) " . " {$condition} ORDER BY at.id DESC" . $sql);
    foreach ($list as $key => $value) {
        $list[$key]['thumb']='../attachment/'.$value['thumb'];
    }

     $total = pdo_fetchcolumn("select count(*) from " . tablename('pc_article_type') . ' at left join ' . tablename('pc_navigation') . " n on (n.id=at.nav_id) " .
    " {$condition} ");

    $pager = pagination($total, $pindex, $psize);

} elseif ($operation == 'post') {
    $id = intval($_GPC['id']);
    if (checksubmit('submit')) {
        $data = array(
            'title' => trim($_GPC['title']),
            'thumb' => save_media($_GPC['thumb']),
            'sort' => trim($_GPC['sort']),
            'nav_id' => intval($_GPC['nav_id']),
            'is_hot' => intval($_GPC['is_hot']),
            'is_show' => intval($_GPC['is_show'])
            // 'is_recommend' => intval($_GPC['is_recommend'])
        );
        if (!empty($id)) {
            pdo_update('pc_article_type', $data, array(
                'id' => $id
            ));
            plog('network.article_type.edit', "修改文章类型 ID: {$id}");
        } else {
            pdo_insert('pc_article_type', $data);
            $id = pdo_insertid();
            plog('network.article_type.add', "添加文章类型 ID: {$id}");
        }
        message('更新文章类型成功！', $this->createPluginWebUrl('network',array('method'=>'article_type','op'=>'display')), 'success');
    }
    $notice = pdo_fetch("SELECT * FROM " . tablename('pc_article_type') . " WHERE id = '$id'");
    $styles = pdo_fetchall("SELECT id,title FROM " . tablename('pc_navigation'));
} elseif ($operation == 'delete') {
    $id     = intval($_GPC['id']);
    $notice = pdo_fetch("SELECT id,title  FROM " . tablename('pc_article_type') . " WHERE id = '$id'");
    if (empty($notice)) {
        message('抱歉，文章类型不存在或是已经被删除！', $this->createPluginWebUrl('network',array('method'=>'article_type','op'=>'display')), 'error');
    }
    pdo_delete('pc_article_type', array(
        'id' => $id
    ));
    plog('network.article_type.delete', "删除文章类型 ID: {$id} 标题: {$notice['title']}");
    message('文章类型删除成功！', $this->createPluginWebUrl('network',array('method'=>'article_type','op'=>'display')), 'success');
}
load()->func('tpl');
include $this->template('article_type');
