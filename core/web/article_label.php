<?php
global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
    $list = pdo_fetchall("SELECT * FROM " . tablename('pc_article_label') . " ORDER BY id DESC");
} elseif ($operation == 'post') {
    $id = intval($_GPC['id']);
    if (checksubmit('submit')) {
        $data = array(
            'title' => trim($_GPC['title'])
        );
        if (!empty($id)) {
            pdo_update('pc_article_label', $data, array(
                'id' => $id
            ));
            plog('network.article_label.edit', "修改文章标签 ID: {$id}");
        } else {
            pdo_insert('pc_article_label', $data);
            $id = pdo_insertid();
            plog('network.article_label.add', "添加文章标签 ID: {$id}");
        }
        message('更新文章标签成功！', $this->createPluginWebUrl('network',array('method'=>'article_label','op'=>'display')), 'success');
    }
    $notice = pdo_fetch("SELECT * FROM " . tablename('pc_article_label') . " WHERE id = '$id'");
} elseif ($operation == 'delete') {
    $id     = intval($_GPC['id']);
    $notice = pdo_fetch("SELECT id,title  FROM " . tablename('pc_article_label') . " WHERE id = '$id'");
    if (empty($notice)) {
        message('抱歉，文章标签不存在或是已经被删除！', $this->createPluginWebUrl('network',array('method'=>'article_label','op'=>'display')), 'error');
    }
    pdo_delete('pc_article_label', array(
        'id' => $id
    ));
    plog('network.article_label.delete', "删除文章标签 ID: {$id} 标题: {$notice['title']}");
    message('文章标签删除成功！', $this->createPluginWebUrl('network',array('method'=>'article_label','op'=>'display')), 'success');
}
load()->func('tpl');
include $this->template('article_label');
