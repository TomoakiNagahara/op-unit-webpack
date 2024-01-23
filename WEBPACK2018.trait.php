<?php
/** op-unit-webpack:/WEBPACK2018.trait.php
 *
 * @created    2024-01-23
 * @version    2.0
 * @package    op-unit-webpack
 * @author     Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright  Tomoaki Nagahara All right reserved.
 */

/** Declare strict
 *
 */
declare(strict_types=1);

/** namespace
 *
 */
namespace OP\UNIT\WEBPACK;

/** use
 *
 */

/** WebPack
 *
 * @created    2024-01-23
 * @version    2.0
 * @package    op-unit-webpack
 * @author     Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright  Tomoaki Nagahara All right reserved.
 */
trait OP_WEBPACK_2018
{
	/** Wrapper method
	 *
	 * @deprecated 2024-01-23
	 * @param      string     $extension
	 * @param      string     $files
	 */
	public static function Set(string $extension, string $files)
	{
		//	Add extension.
		foreach( $files as & $file ){
			$file .= ".{$extension}";
		}

		//	Register files.
		self::_RegisterFiles($files);

		//	For Eclipse notice error.
		if( 0 ){
			D($file);
		}
	}

	/** Wrapper method
	 *
	 * @deprecated 2024-01-23
	 * @param      string     $file_path
	 */
	public static function Js(string $file_path)
	{
		//	Remove extension if added.
		$file_path  = rtrim($file_path, '.js');

		//	Add extension.
		$file_path .= '.js';

		//	Register.
		self::Auto($file_path);
	}

	/** Wrapper method.
	 *
	 * @deprecated 2024-01-23
	 * @param      string     $file_path
	 */
	public static function Css(string $file_path)
	{
		//	Remove extension if added.
		$file_path  = rtrim($file_path, '.css');

		//	Add extension.
		$file_path .= '.css';

		//	Register.
		self::Auto($file_path);
	}
}
