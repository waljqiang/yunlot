<?php
namespace Waljqiang\Yunlot;

use Waljqiang\Yunlot\Profile\ProfileInterface;

class Yunlot{
	private $version = "v1.0";
	private $profile;

	private static $instance;

	public static function getInstance(){
		$className = get_called_class();
		if(!isset(self::$instance[$className])){
			self::$instance[$className] = new $className();
		}
        return self::$instance[$className];
	}

	public function init($version,$token = "waljqiang",$type = Waljqiang\Encode\Encode::AES,$config = ["key" => "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG"]){
		$this->setVersion($version);
		$className = "Waljqiang\\Yunlot\\Profile\\" . "YunlotVersion" . str_replace(".","",$version);
		if(!class_exists($className)){
			throw new \Exception("Unsupport yunlot version",-1);
		}
		$this->profile = new $className();
		$this->profile->setEncode($token,$type,$config);
		return $this;
	}

	public function getProfile(){
		return $this->profile;
	}

	public function setVersion($version){
		$this->version = $version;
	}

	public function __call($method,$args){
		return call_user_func_array([$this->profile,$method],$args);
	}
}