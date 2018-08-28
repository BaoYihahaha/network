<?php
global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
    $list = pdo_fetchall('select * from ' . tablename('pc_page_plate') . " ORDER BY sort,id DESC");
} elseif ($operation == 'post') {
    $id = intval($_GPC['id']);
    if (checksubmit('submit')) {
        $data = array(
            'title' => trim($_GPC['title']),
            'is_rankings' => intval($_GPC['is_rankings']),
            'remarks' => trim($_GPC['remarks'])
        );
        if (!empty($id)) {
            pdo_update('pc_page_plate', $data, array(
                'id' => $id
            ));
            plog('network.page_plate.edit', "修改页面模块 ID: {$id}");
        } else {
            pdo_insert('pc_page_plate', $data);
            $id = pdo_insertid();
            plog('network.page_plate.add', "添加页面模块 ID: {$id}");
        }
        message('更新页面模块成功！', $this->createPluginWebUrl('network',array('method'=>'page_plate','op'=>'display')), 'success');
    }
    $notice = pdo_fetch("SELECT * FROM " . tablename('pc_page_plate') . " WHERE id = '$id'");
} elseif ($operation == 'delete') {
    $id     = intval($_GPC['id']);
    $notice = pdo_fetch("SELECT id,title  FROM " . tablename('pc_page_plate') . " WHERE id = '$id'");
    if (empty($notice)) {
        message('抱歉，页面模块不存在或是已经被删除！', $this->createPluginWebUrl('network',array('method'=>'page_plate','op'=>'display')), 'error');
    }
    pdo_delete('pc_page_plate', array(
        'id' => $id
    ));
    plog('network.page_plate.delete', "删除页面模块 ID: {$id} 标题: {$notice['title']}");
    message('页面模块删除成功！', $this->createPluginWebUrl('network',array('method'=>'page_plate','op'=>'display')), 'success');
}
load()->func('tpl');
include $this->template('page_plate');
