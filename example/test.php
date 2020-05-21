<?php
require_once __DIR__ . "/../vendor/autoload.php";
use Waljqiang\Yunlot\Yunlot;
try{
	$token = "yuncore";
	$key = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG";
	$str = '{"header":{"protocol":"v1.0","type":"2","encode":{"type":"2","token":"yuncore","key":"abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG","nonce":"abcdef","timestamp":"1587440903","signature":"793685c1f46bdd832966695d2d17d82fbaf64a48"}},"body":"385165535141722f7775343550695361654e4f66464e496f5574442b56637a4d762b6349705451757155436d37625877367a7952614b6f536d612f58376468794c725232566750567076797337785344425a6d6b61707043543161514d777935515a553665326e30345a4c4847396c6c543967697a4b564174735652712b617442626c686f6f642f6f704f67594f335468386277722f6836302b687550332b5263613476654b6b7362744f3973466d442f693654347362642f536930532f524d745a5a323966445156746f6f564b306a653774474662316c384c6a52422f41485a344e38695575453462526c6256344e4952505a466d59526776626565356f75624e535a5339485542726b65326d5842464f7456414d7a5850776d7874764f79345835417747336b2b584a6b49346841764472775a43454550304d473244582f44306e476f746f6e33737164435778522f6a6475325235366161706434333842306d733943656d444355673d","now":"1587440903"}';
	Yunlot::getInstance()->init(['encode' => ['token' => $token,'type' => '2','key' => $key]],"v1.0");

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
	$c = Yunlot::getInstance()->out();
	echo '<pre>';
	print_r($c);
	echo '</pre>';


	$str = '{"header":{"protocol":"v1.0","type":"1","encode":{"type":"1"}},"body":{"active":{"name":"CPE120M","chip":"RTL8197F-FR350","cpu":"1GHz","flash":"8","ram":"64","mac":"44:D1:FA:08:B9:F5","version":"CPE120M-CPE-V2.0-Build20190927134132","location":{"lat":"36.60332","lng":"109.50051"}}},"now":"1589162933"}';
	$d = Yunlot::getInstance()->parse($str);
	var_dump($d);

	$nonce = "abcdef";
	$timeStamp = "1587440903";
	Yunlot::getInstance()->setHeader(["type" => 2,"encode" => ["type" => 1,"token" => $token,"key" => $key]]);
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
	$e = Yunlot::getInstance()->out();
	echo '<pre>';
	print_r($e);
	echo '</pre>';
}catch(\Exception $e){
	var_dump($e);
}