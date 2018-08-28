<?php
global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
	if (!empty($_GPC["sort"])) { foreach ($_GPC["sort"] as $id => $sort) { pdo_update("pc_navigation", array("sort" => $sort), array("id" => $id)); } plog("network.network.edit","批量修改导航栏排序"); message("导航栏更新成功！", $this->createPluginWebUrl('network',array('method'=>'network','op'=>'display')), "success"); 
    } 
    $list = pdo_fetchall("SELECT * FROM " . tablename('pc_navigation') . " ORDER BY sort DESC");
} elseif ($operation == 'post') {
    $id = intval($_GPC['id']);
    if (checksubmit('submit')) {
        $data = array(
            'title' => trim($_GPC['title']),
            'link' => save_media($_GPC['link']),
            'sort' => trim($_GPC['sort']),
            'is_show' => intval($_GPC['is_show']),
            'addtime' => time()
        );
        if (!empty($id)) {
            pdo_update('pc_navigation', $data, array(
                'id' => $id
            ));
            plog('network.network.edit', "修改导航栏 ID: {$id}");
        } else {
            pdo_insert('pc_navigation', $data);
            $id = pdo_insertid();
            plog('network.network.add', "添加导航栏 ID: {$id}");
        }
        message('更新导航栏成功！', $this->createPluginWebUrl('network',array('method'=>'network','op'=>'display')), 'success');
    }
    $notice = pdo_fetch("SELECT * FROM " . tablename('pc_navigation') . " WHERE id = '$id'");
} elseif ($operation == 'delete') {
    $id     = intval($_GPC['id']);
    $notice = pdo_fetch("SELECT id,title  FROM " . tablename('pc_navigation') . " WHERE id = '$id'");
    if (empty($notice)) {
        message('抱歉，导航栏不存在或是已经被删除！', $this->createPluginWebUrl('network',array('method'=>'network','op'=>'display')), 'error');
    }
    pdo_delete('pc_navigation', array(
        'id' => $id
    ));
    plog('network.network.delete', "删除公告 ID: {$id} 标题: {$notice['title']}");
    message('导航栏删除成功！', $this->createPluginWebUrl('network',array('method'=>'network','op'=>'display')), 'success');
}
load()->func('tpl');
include $this->template('network');
