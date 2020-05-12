<?php
namespace Waljqiang\Yunlot;

use Waljqiang\Yunlot\Profile\ProfileInterface;
use Waljqiang\Yunlot\Exception\YunlotException;

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

	public function init($version = 'v1.0',$config = []){
		$this->setVersion($version);
		$className = "Waljqiang\\Yunlot\\Profile\\" . "YunlotVersion" . str_replace(".","",$version);
		if(!class_exists($className)){
			throw new YunlotException("Unsupport yunlot version",YunlotException::UNSUPPORT_YUNLOT_VERSION);
		}
		$this->profile = new $className();
		$this->profile->setEncode($config);
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