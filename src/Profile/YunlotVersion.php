<?php
namespace Waljqiang\Yunlot\Profile;

use Waljqiang\Encode\Encode;
use Waljqiang\Yunlot\Exception\YunlotException;

abstract class YunlotVersion implements ProfileInterface{

	public function __construct(){

	}

	abstract public function getVersion();

	abstract public function getHeader($key = "",$default = NULL);

	abstract public function getBody($key = "",$default = NULL);

	abstract public function setHeader($header);

	abstract public function setBody($body,$nonce = "",$timeStamp = "");

	public function array_get($array,$key,$default = NULL){
		foreach (explode(".",$key) as $segment) {
			if(isset($array[$segment]))
				$array = $array[$segment];
			else
				return $default;
		}
		return $array;
	}
}