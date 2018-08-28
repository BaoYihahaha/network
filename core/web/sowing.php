<?php
global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
    if (!empty($_GPC["sort"])) { foreach ($_GPC["sort"] as $id => $sort) { pdo_update("pc_sowing", array("sort" => $sort), array("id" => $id)); } plog("network.sowing.edit","批量修改轮播图排序"); message("轮播图更新成功！", $this->createPluginWebUrl('network',array('method'=>'sowing','op'=>'display')), "success"); 
    } 
    $list = pdo_fetchall("SELECT * FROM " . tablename('pc_sowing') . " ORDER BY id DESC");
    foreach ($list as $key => $value) {
        $list[$key]['thumb']='../attachment/'.$value['thumb'];
    }
} elseif ($operation == 'post') {
    $id = intval($_GPC['id']);
    if (checksubmit('submit')) {
        $data = array(
            'title' => trim($_GPC['title']),
            'thumb' => save_media($_GPC['thumb']),
            'link' => save_media($_GPC['link']),
            'sort' => trim($_GPC['sort']),
            'is_show' => intval($_GPC['is_show'])
        );
        if (!empty($id)) {
            pdo_update('pc_sowing', $data, array(
                'id' => $id
            ));
            plog('network.sowing.edit', "修改轮播图 ID: {$id}");
        } else {
            pdo_insert('pc_sowing', $data);
            $id = pdo_insertid();
            plog('network.sowing.add', "添加轮播图 ID: {$id}");
        }
        message('更新轮播图成功！', $this->createPluginWebUrl('network',array('method'=>'sowing','op'=>'display')), 'success');
    }
    $notice = pdo_fetch("SELECT * FROM " . tablename('pc_sowing') . " WHERE id = '$id'");
} elseif ($operation == 'delete') {
    $id     = intval($_GPC['id']);
    $notice = pdo_fetch("SELECT id,title  FROM " . tablename('pc_sowing') . " WHERE id = '$id'");
    if (empty($notice)) {
        message('抱歉，轮播图不存在或是已经被删除！', $this->createPluginWebUrl('network',array('method'=>'sowing','op'=>'display')), 'error');
    }
    pdo_delete('pc_sowing', array(
        'id' => $id
    ));
    plog('network.sowing.delete', "删除轮播图 ID: {$id} 标题: {$notice['title']}");
    message('轮播图删除成功！', $this->createPluginWebUrl('network',array('method'=>'sowing','op'=>'display')), 'success');
}
load()->func('tpl');
include $this->template('sowing');
