<?php
namespace Waljqiang\Yunlot\Profile;

use Waljqiang\Encode\Encode;
use Waljqiang\Yunlot\Exception\YunlotException;

class YunlotVersionv10 extends YunlotVersion{
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

	public function setHeader($protocolType = 2,$encode = ["type" => "1"]){
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
			throw new YunlotException("Set body with yunlot v1.0 is failure",YunlotException::YUNLOT10_BODY_SET_ERROR);		
		}
	}

	public function getBody($key = "",$default = NULL){
		return !empty($key) ? $this->array_get($this->body,$key,$default) : $this->body;
	}

	public function setNow($now = ""){
		$this->now = !empty($now) ? $now : time();
		return $this;
	}

	public function getNow(){
		return $this->now;
	}

	public function parse($str){
		$data = json_decode($str,true);
		$this->checkData($data);
		$this->header = $data["header"];
		$this->checkHeader();
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
			$this->checkBody();
			$this->now = $data["now"];
			return $this;
		}catch(\Exception $e){
			throw new YunlotException($e->getMessage(),$e->getCode());
		}
	}

	public function decryptBody($data){
		try{
			if(isset($this->header["encode"]["token"]) && isset($this->header["encode"]["key"])){
				self::$encode = new Encode($this->getHeader("encode.token"),$this->encodeType[$this->getHeader("encode.type")],['key' => $this->getHeader("encode.key")]);
			}
			self::$encode->setNonce($this->getHeader("encode.nonce"));
			self::$encode->setTimeStamp($this->getHeader("encode.timestamp"));
			self::$encode->setSignature($this->getHeader("encode.signature"));
			$result = json_decode(self::$encode->decode($data),true);
			if(empty($result)){
				throw new YunlotException("The data of yunlot v1.0 decrypt failure",YunlotException::YUNLOT10_DECODE_ERROR);
			}
			return $result;
		}catch(\Exception $e){
			throw new YunlotException("The data of yunlot v1.0 decrypt failure",YunlotException::YUNLOT10_DECODE_ERROR);
		}
	}

	public function out(){
		return json_encode([
			"header" => $this->header,
			"body" => $this->body,
			"now" => $this->now
		],JSON_UNESCAPED_UNICODE);
	}

	protected function checkData($data){
		if(!isset($data["header"]) || !isset($data["body"]) || !isset($data["now"])){
			throw new YunlotException("The yunlot v1.0 must be include header、body and now",YunlotException::YUNLOT10_FORMAT_ERROR);
		}
	}

	protected function checkHeader(){
		if(!isset($this->header["protocol"]) || !isset($this->header["type"]) || !isset($this->header["encode"])){
			throw new YunlotException("The header of yunlot v1.0 must be protocol 、type and encode",YunlotException::YUNLOT10_HEADER_FORMAT_ERROR);
		}
		if($this->getHeader("protocol") == $this->getVersion()){
			if(!in_array($this->getHeader("type"),["1","2","3"])){
				throw new YunlotException("The type of the header in yunlot v1.0 must be '1'、'2' or '3'",YunlotException::YUNLOT10_HEADER_TYPE_ERROR);
			}
			if(!in_array($this->getHeader("encode.type"),["1","2"])){
				throw new YunlotException("The encode type of the header in yunlot v1.0 must be '1' or '2'",YunlotException::YUNLOT10_HEADER_TYPE_ERROR);
			}
		}
	}

	protected function checkBody(){
		$keys = array_keys($this->body);
		if($var = array_diff($keys,["active","system","network","wifi","repeat","user","time_reboot","child","comm_result"])){
			throw new YunlotException("The keys [" . implode(",",$var) . "] is not defined in the body of the yunlot v1.0",YunlotException::YUNLOT10_BODY_FORMAT_ERROR);
		}
	}
}