<?php
global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') { 
    $list = pdo_fetchall('select * from ' . tablename('pc_advertisement') . " ORDER BY id DESC");
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
            'position' => trim($_GPC['position'])
        );
        if (!empty($id)) {
            pdo_update('pc_advertisement', $data, array(
                'id' => $id
            ));
            plog('network.advertisement.edit', "修改型文章类 ID: {$id}");
        } else {
            pdo_insert('pc_advertisement', $data);
            $id = pdo_insertid();
            plog('network.advertisement.add', "添加官网广告 ID: {$id}");
        }
        message('更新官网广告成功！', $this->createPluginWebUrl('network',array('method'=>'advertisement','op'=>'display')), 'success');
    }
    $notice = pdo_fetch("SELECT * FROM " . tablename('pc_advertisement') . " WHERE id = '$id'");
    $styles = pdo_fetchall("SELECT id,title FROM " . tablename('pc_navigation'));
} elseif ($operation == 'delete') {
    $id     = intval($_GPC['id']);
    $notice = pdo_fetch("SELECT id,title  FROM " . tablename('pc_advertisement') . " WHERE id = '$id'");
    if (empty($notice)) {
        message('抱歉，官网广告不存在或是已经被删除！', $this->createPluginWebUrl('network',array('method'=>'advertisement','op'=>'display')), 'error');
    }
    pdo_delete('pc_advertisement', array(
        'id' => $id
    ));
    plog('network.advertisement.delete', "删除官网广告 ID: {$id} 标题: {$notice['title']}");
    message('官网广告删除成功！', $this->createPluginWebUrl('network',array('method'=>'advertisement','op'=>'display')), 'success');
}
load()->func('tpl');
include $this->template('advertisement');
