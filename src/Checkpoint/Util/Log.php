<?php
/**
 * This file is part of the Asteriskc utility.
 *
 * (c) Sankar suda <sankar.suda@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Checkpoint\Util;

/**
 * @author sankar <sankar.suda@gmail.com>
 */

class Log
{

	public static $name;

	public static function info($message)
	{
		echo "[INFO] ".self::$name." $message".PHP_EOL;
	}


	public static function warning($message)
	{
		echo "[WARNING] ".self::$name." $message\n";
	}

	public static function error($message)
	{
		echo "[ERROR] ".self::$name." $message\n";
	}

	public static function critical($message)
	{
		echo "[CRITICAL] ".self::$name." $message\n";
		exit;
	}

	public static function format($message,$context = array())
	{
		$data  = date('d/m/Y H:i:s').': ';
		$data .= $message . ($context ? str_replace("\n", '', var_export($context, true)) : '');
		$data .= "\r\n";

		return $data;
	}

	public static function write($type,$message)
	{
		$path = LOG.$type.'-'.date('d-M').'.log';
		file_put_contents($path,self::format($message),FILE_APPEND);
	}


	/**
	 * Rotate log file if size specified in config is reached.
	 * Also if `rotate` count is reached oldest file is removed.
	 *
	 * @param string $filename Log file name
	 * @return mixed True if rotated successfully or false in case of error.
	 *   Void if file doesn't need to be rotated.
	 */
	public static function rotateFile($filepath)
	{

		$size = 500 * 1024 * 1024; //500MB

		clearstatcache(true, $filepath);

		if (!file_exists($filepath) ||
			filesize($filepath) < $size
		) {
			return;
		}

		return rename($filepath, $filepath . '.' . time());
	}

}
