<?php
namespace Waljqiang\Yunlot\Profile;

use Waljqiang\Encode\Encode;

class YunlotVersionv10 implements ProfileInterface{
	const VERSION = "v1.0";

	private $encodeType = [
		"2" => "AES"
	];

	private $header;
	private $body;
	private $now;

	public function __construct(){

	}

	/**
	 * 加解密类
	 * @var [type]
	 */
	private static $encode;

	public function setEncode($config = []){
		if(isset($config["encode"])){
			self::$encode = new Encode($config["encode"]["token"],$config["encode"]["type"],["key" => $config["encode"]["key"]]);
		}
	}

	public function getVersion(){
		return self::VERSION;
	}

	public function setHeader($protocolType,$encode){
		$this->header = [
			"protocol" => self::VERSION,
			"type" => $protocolType,
			"encode" => $encode
		];
		return $this;
	}

	public function getHeader($key = "",$default = NULL){
		return !empty($key) ? $this->array_get($this->header,$key,$default) : $this->header;
	}

	public function setBody($body,$nonce = "",$timeStamp = ""){
		try{
			switch ($this->getHeader("encode.type")) {
				case "1":
					$this->body = $body;
					break;
				case "2":
					if(isset($this->header["encode"]["token"]) && isset($this->header["encode"]["key"])){
						self::$encode = new Encode($this->getHeader("encode.token"),$this->encodeType[$this->getHeader("encode.type")],['key' => $this->getHeader("encode.key")]);
					}
					self::$encode->setNonce($nonce);
					self::$encode->setTimeStamp($timeStamp);
					$data = self::$encode->encode(json_encode($body,JSON_UNESCAPED_UNICODE));
					$this->header["encode"] = array_merge($this->header["encode"],[
						"nonce" => $data["nonce"],
						"timestamp" => $data["timestamp"],
						"signature" => $data["signature"]
					]);
					$this->body = $data["encrypted"];
					break;
				default:
					$this->body = $body;
					break;
			}
			return $this;
		}catch(\Exception $e){
			throw new \Exception("Set body failure", -1);		
		}
	}

	public function getBody($key = "",$default = NULL){
		return !empty($key) ? $this->array_get($this->body,$key,$default) : $this->body;
	}

	public function setNow($now){
		$this->now = $now;
		return $this;
	}

	public function getNow(){
		return $this->now;
	}

	public function parse($str){
		$data = json_decode($str,true);
		if(!isset($data["header"]) || !isset($data["body"]) || !isset($data["now"])){
			throw new \Exception("data format is error",-1);
		}
		$this->header = $data["header"];
		try{
			switch ($this->getHeader("encode.type","1")) {
				case '1':
					$this->body = $data["body"];
					break;
				case '2':
					$this->body = $this->decryptBody($data["body"]);
					break;
				default:
					$this->body = $data["body"];
					break;
			}
			$this->now = $data["now"];
			return $this;
		}catch(\Exception $e){
			throw new \Exception("decode body failure",-1);
		}
	}

	public function decryptBody($data){
		if(isset($this->header["encode"]["token"]) && isset($this->header["encode"]["key"])){
			self::$encode = new Encode($this->getHeader("encode.token"),$this->encodeType[$this->getHeader("encode.type")],['key' => $this->getHeader("encode.key")]);
		}
		self::$encode->setNonce($this->getHeader("encode.nonce"));
		self::$encode->setTimeStamp($this->getHeader("encode.timestamp"));
		self::$encode->setSignature($this->getHeader("encode.signature"));
		return json_decode(self::$encode->decode($data),true);
	}

	public function out(){
		return json_encode([
			"header" => $this->header,
			"body" => $this->body,
			"now" => $this->now
		],JSON_UNESCAPED_UNICODE);
	}

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