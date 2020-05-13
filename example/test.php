<?php
require_once __DIR__ . "/../vendor/autoload.php";
use Waljqiang\Yunlot\Yunlot;
try{
	$token = "yuncore";
	$key = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG";
	$str = '{"header":{"protocol":"v1.0","type":"1","encode":{"type":"2","token":"yuncore","key":"abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG","nonce":"abcdef","timestamp":"1587440903","signature":"4982beefa9947f0b5b24d67953d042c3373c27c5"}},"body":"34654f7a6d6a562f767935664c535539454d4e30757a34316737544232665545794273524c444a6c6b68424c6539655877386b59784b4c6a4c712b2b4a4166797939744851617850315435416969616f69516a686253306e4a797835494c493344777332662f64363079577a5367514e576742467a7a2b787a6f2f47536455614f7765786d786f6a7a784842742b37793155594643414a4b382b7045474656645a576450456363685932527a547736686b59352b54776f4671325047575a61726e766c4b664f534141306c73744e4d574a58496d42466c5432577064674c53457667484d79464a7176573047726d7848734b77524d4775744f4b51766155566c3756746467437674477638597237546d7873736b46672b4d5754746d494a6e2b507148436833476a6a593972424a64756d784d324c6956544e6454486b6478736d2b777561554b596f6237655a49546c6f33673478396f48456567433054415a3249737163716d643346383d","now":"1587440903"}';
	Yunlot::getInstance()->init(['encode' => ['token' => $token,'type' => 'AES','key' => $key]],"v1.0");
	//Yunlot::getInstance()->setEncode(['encode' => ['token' => $token,'type' => 'AES','key' => $key]]);
	$b = Yunlot::getInstance()->parse($str);
	var_dump($b);

	$nonce = "abcdef";
	$timeStamp = "1587440903";
	Yunlot::getInstance()->setHeader(["type" => "2","encode" => ["type" => "2","token" => $token,"key" => $key]]);
	Yunlot::getInstance()->setBody([
		"active" => [
			'name' => 'CPE120M',
	      	'chip' => 'RTL8197F-FR350',
	      	'cpu' => '1GHz',
	      	'flash' => '8',
	      	'ram' => '64',
	      	'mac' => '44:D1:FA:7B:FB:38',
	      	'version' => 'CPE120M-CPE-V2.0-Build20190927134132',
      		'location' => [
          		'lat' => '36.60332',
          		'lng' => '109.50051'
          	]
		]
	],$nonce,$timeStamp);
	Yunlot::getInstance()->setNow($timeStamp);
	echo '<pre>';
	print_r(Yunlot::getInstance()->out());
	echo '</pre>';
}catch(\Exception $e){
	var_dump($e);
}