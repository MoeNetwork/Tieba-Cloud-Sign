<?php
/**
 * 密码和加密类
 * @copyright (c) Kenvix
 */
class P {
	const PWDMODE  = true; //允许从数据库获得密码加密方式？
	public $pwdmode; 
	public $mcrypt = false;
	public $salt   = '';

	/**
	 * @param string $salt 盐，留空将从config.php读取
	 */
	public function __construct($salt = '') {
		if (self::PWDMODE && class_exists('option')) {
			$this->pwdmode = option::get('pwdmode');
		} else {
			$this->pwdmode = 'md5(md5(md5($pwd)))';
		}
		if (function_exists('mcrypt_decrypt')) {
			$this->mcrypt  = true;
		}
		/*
		if (!empty($salt)) {
			$this->salt    = $salt;
		} else {
			$this->salt    = SYSTEM_SALT;
		}
		*/
	}

	/**
	 * 对数据（通常是密码）进行不可逆加密
	 * @param string $pwd 密码
	 * @return string 加密的密码
	 */
	public function pwd($pwd) {
		return eval('return '.option::get('pwdmode').';');
	}

	/**
	 * 对数据进行可逆加密
	 * @param string $str 原文
	 * @param int $cipher 加密算法，留空为默认
	 * @param string $mode  加密模式，留空为默认
	 * @return string 密文或者false表示失败
	 */
	public function encode($str , $cipher = MCRYPT_RIJNDAEL_256, $mode = MCRYPT_MODE_CFB) {
		if (!empty($this->salt)) {
			return base64_encode(mcrypt_encrypt($cipher , $this->salt , $str , $mode));
		} else {
			return $str;
		}
	}

	/**
	 * 解密密文
	 * @param string $str 密文
	 * @param int $cipher 加密算法，留空为默认
	 * @param string $mode  加密模式，留空为默认
	 * @return string|bool 原文或者false表示失败
	 */
	public function decode($str , $cipher = MCRYPT_RIJNDAEL_256, $mode = MCRYPT_MODE_CFB) {
		if (!empty($this->salt)) {
			return mcrypt_decrypt($cipher , $this->salt , base64_decode($str) , $mode);
		} else {
			return $str;
		}
	}
}
