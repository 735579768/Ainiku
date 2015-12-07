<?php
namespace Common\Api;
defined("ACCESS_ROOT") || die("Invalid access");
class ModelApi{
		/**
		 *留言模型
		 ***/	
		 public static function commentsModel(){
			return include('Model/CommentsModel.php');
			 }		 
		/**
		 *配置数据模型
		 ***/
		public static function configModel(){
			return include('Model/ConfigModel.php');
		}

		 public static function memberattrModel(){
			return include('Model/MemberattrModel.php');
			 }

		public static function MemberModel($type='add'){
			return include('Model/MemberModel.php');
		}
		public static function ModeattrlModel($type='add'){
			return include('Model/ModelattrModel.php');
		}
//		public static function ModelModel($type='add'){
//			return include('Model/ModelModel.php');
//		}
}