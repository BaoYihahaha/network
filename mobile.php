<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class NetworkMobile extends Plugin
{
	public function __construct()
	{
		parent::__construct('network');
	}

	public function index()
	{
		$this->_exec_plugin(__FUNCTION__, false);
	}
	public function article_list()
	{
		$this->_exec_plugin(__FUNCTION__, false);
	}
	public function article_info()
	{
		$this->_exec_plugin(__FUNCTION__, false);
	}
}