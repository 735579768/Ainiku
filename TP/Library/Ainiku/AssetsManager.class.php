<?php
/**
 *资源管理类
 *
 **/
namespace Ainiku;
class AssetsManager {
	static instance=null;
	public getInstance(){
		if(!\AssetsManager::instance){
			\AssetsManager::instance=new \AssetsManager();
		}
		return \AssetsManager::instance;
	}
}