<?php
namespace Waljqiang\Yunlot\Profile;
interface ProfileInterface{
	/**
	 * return the version of the yunlot
	 */
	public function getVersion();
	/**
	 * return the headers of the yunlot
	 */
	public function getHeader($key = "");

	/**
	 * return the body of the yunlot
	 */
	public function getBody($key = "");
}