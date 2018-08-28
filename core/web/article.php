<?php
global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
    //文章类型
    $article_type = pdo_fetchall("SELECT id,title FROM " . tablename('pc_article_type'));
    //文章所属板块
    $page_plate = pdo_fetchall("SELECT id,title FROM " . tablename('pc_page_plate') . " ORDER BY sort ASC");
    //文章标签
    // $article_label = pdo_fetchall("SELECT id,title FROM " . tablename('pc_article_label'));

    $pindex    = max(1, intval($_GPC['page']));
    $psize     = 20;
    $condition = " and article.uniacid=:uniacid";
    $params    = array(
        ':uniacid' => $_W['uniacid']
    );


    $condition = 'WHERE 1';
     if (!empty($_GPC['title'])) {
        $_GPC['title'] = trim($_GPC['title']);
        $condition .= " AND a.title like '%{$_GPC['title']}%' ";
    }
    if (empty($starttime) || empty($endtime)) {
        $starttime = strtotime('-1 month');
        $endtime   = time();
    }
    if (!empty($_GPC['time'])) {
        $starttime = strtotime($_GPC['time']['start']);
        $endtime   = strtotime($_GPC['time']['end']);
        if ($_GPC['searchtime'] == '1') {
            $condition .= " AND a.addtime >= $starttime AND a.addtime <= $endtime ";
        }
    }
    if (!empty($_GPC['art_id'])) {
        $condition .= ' and a.art_id=' . intval($_GPC['art_id']);
    }
    if (!empty($_GPC['pag_id'])) {
        $condition .= ' and a.pag_id=' . intval($_GPC['pag_id']);
    }
    if (!empty($_GPC['lad_id'])) {
        $condition .= ' and a.lad_id=' . intval($_GPC['lad_id']);
    }
    if (empty($_GPC['export'])) {

        $sql .= " limit " . ($pindex - 1) * $psize . ',' . $psize;

    }
    $list = pdo_fetchall("SELECT a.*, pa.title as pa_title, pp.title as pp_title FROM " . tablename('pc_article') . 
    ' a LEFT JOIN ' . tablename('pc_article_type') . " pa on (pa.id = a.art_id) " . 
    ' LEFT JOIN ' . tablename('pc_page_plate') . " pp on (pp.id = a.pag_id) " . 
    " {$condition} ORDER BY id DESC " . $sql);

    if ($_GPC['export'] == '1') {

        plog('network.article.export', '导出会员数据');

        foreach ($list as &$row) {

            $row['addtime'] = date('Y-m-d H:i', $row['addtime']);

        }

        unset($row);

        m('excel')->export($list, array(

            "title" => "文章数据-" . date('Y-m-d-H-i', time()),

            "columns" => array(

                array(

                    'title' => '所属分类',

                    'field' => 'pa_title',

                    'width' => 12

                ),

                array(

                    'title' => '所属模块',

                    'field' => 'pp_title',

                    'width' => 12

                ),

                array(

                    'title' => '标题',

                    'field' => 'title',

                    'width' => 12

                ),

                array(

                    'title' => '作者',

                    'field' => 'author',

                    'width' => 12

                ),

                array(

                    'title' => '内容',

                    'field' => 'detail',

                    'width' => 12

                ),
            )

        ));

    }
    $total = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('pc_article') . 
    ' a LEFT JOIN ' . tablename('pc_article_type') . " pa on (pa.id = a.art_id) " . 
    ' LEFT JOIN ' . tablename('pc_page_plate') . " pp on (pp.id = a.pag_id) " . 
    " {$condition} ");

    $pager = pagination($total, $pindex, $psize);
} elseif ($operation == 'post') {
    $id = intval($_GPC['id']);
    if (checksubmit('submit')) {
        $data = array(
            'art_id' => intval($_GPC['art_id']),
            'pag_id' => intval($_GPC['pag_id']),
            'title' => trim($_GPC['title']),
            'author' => trim($_GPC['author']),
            'thumb' => save_media($_GPC['thumb']),
            'detail' => htmlspecialchars_decode($_GPC['detail']),
            'is_stick' => intval($_GPC['is_stick']),
            // 'is_hot' => intval($_GPC['is_hot']),
            'module_stick' => intval($_GPC['module_stick']),
            'is_show' => intval($_GPC['is_show']),
            'addtime' => strtotime($_GPC['addtime'])
        );
        if (!empty($id)) {
            pdo_update('pc_article', $data, array(
                'id' => $id
            ));
            plog('network.article.edit', "修改文章 ID: {$id}");
        } else {
            $data['article_addtime'] = time();
            pdo_insert('pc_article', $data);
            $id = pdo_insertid();
            plog('network.article.add', "添加文章 ID: {$id}");
        }
        message('更新文章成功！', $this->createPluginWebUrl('network',array('method'=>'article','op'=>'display')), 'success');
    }
    $notice = pdo_fetch("SELECT * FROM " . tablename('pc_article') . " WHERE id = '$id'");
    $article_type = pdo_fetchall("SELECT id,title FROM " . tablename('pc_article_type'));//文章类型
    //文章所属板块
    $page_plate = pdo_fetchall("SELECT id,title FROM " . tablename('pc_page_plate'));
    //文章标签
    $article_label = pdo_fetchall("SELECT id,title FROM " . tablename('pc_article_label'));
} elseif ($operation == 'delete') {
    $id     = intval($_GPC['id']);
    $notice = pdo_fetch("SELECT id,title  FROM " . tablename('pc_article') . " WHERE id = '$id'");
    if (empty($notice)) {
        message('抱歉，文章不存在或是已经被删除！', $this->createPluginWebUrl('network',array('method'=>'article','op'=>'display')), 'error');
    }
    pdo_delete('pc_article', array(
        'id' => $id
    ));
    plog('network.article.delete', "删除文章 ID: {$id} 标题: {$notice['title']}");
    message('文章删除成功！', $this->createPluginWebUrl('network',array('method'=>'article','op'=>'display')), 'success');
}
load()->func('tpl');
include $this->template('article');
