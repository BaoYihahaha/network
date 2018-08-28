<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class NetworkWeb extends Plugin
{
	public function __construct()
	{
		parent::__construct('network');
	}

	public function index()
	{
		$this->_exec_plugin(__FUNCTION__);
	}

	public function api()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
	//导航栏设置
	public function network()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
	//文章类型
	public function article_type()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
	//轮播图
	public function sowing()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
	//文章标签
	public function article_label()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
    //官网模块添加
    public function page_plate()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
	//官网广告管理
	public function advertisement()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
	//官网文章管理
	public function article()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
	//热门标签
	public function hot_label()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
}

//测试
