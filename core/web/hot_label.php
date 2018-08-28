<?php
global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
    $list = pdo_fetchall("SELECT * FROM " . tablename('pc_hot_label') . " ORDER BY id DESC");
} elseif ($operation == 'post') {
    $id = intval($_GPC['id']);
    if (checksubmit('submit')) {
        $data = array(
            'title' => trim($_GPC['title']),
            'link' => save_media($_GPC['link']),
            'is_show' => intval($_GPC['is_show'])
        );
        if (!empty($id)) {
            pdo_update('pc_hot_label', $data, array(
                'id' => $id
            ));
            plog('network.hot_label.edit', "修改热门标签 ID: {$id}");
        } else {
            pdo_insert('pc_hot_label', $data);
            $id = pdo_insertid();
            plog('network.hot_label.add', "添加热门标签 ID: {$id}");
        }
        message('更新热门标签成功！', $this->createPluginWebUrl('network',array('method'=>'hot_label','op'=>'display')), 'success');
    }
    $notice = pdo_fetch("SELECT * FROM " . tablename('pc_hot_label') . " WHERE id = '$id'");
} elseif ($operation == 'delete') {
    $id     = intval($_GPC['id']);
    $notice = pdo_fetch("SELECT id,title  FROM " . tablename('pc_hot_label') . " WHERE id = '$id'");
    if (empty($notice)) {
        message('抱歉，热门标签不存在或是已经被删除！', $this->createPluginWebUrl('network',array('method'=>'hot_label','op'=>'display')), 'error');
    }
    pdo_delete('pc_hot_label', array(
        'id' => $id
    ));
    plog('network.hot_label.delete', "删除热门标签 ID: {$id} 标题: {$notice['title']}");
    message('热门标签删除成功！', $this->createPluginWebUrl('network',array('method'=>'hot_label','op'=>'display')), 'success');
}
load()->func('tpl');
include $this->template('hot_label');
