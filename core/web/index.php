<?php
global $_W, $_GPC;
//check_shop_auth('http://120.26.212.219/api.php', $this->pluginname);
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
load()->func('tpl');
if ($operation == 'display') {
	// 
	include $this->template('index');
} elseif ($operation == 'post') {
	// 
}
