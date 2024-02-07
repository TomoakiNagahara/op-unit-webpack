<?php
/** op-unit-webpack:/WEBPACK2024.trait.php
 *
 * @created    2024-01-22
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
use OP\OP_SESSION;

/** WebPack
 *
 * @created    2024-01-22
 * @version    2.0
 * @package    op-unit-webpack
 * @author     Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright  Tomoaki Nagahara All right reserved.
 */
trait OP_WEBPACK_2024
{
	/** trait
	 *
	 */
	use OP_SESSION;

	static public function Auto(...$args)
	{
		//	...
		if( empty($args) ){
			self::_OutputSourceCode();
		}else{
			self::_RegisterFiles($args);
		}
	}

	static public function Hash(string $extension) : string
	{
		//	Get session by extension.
		$session = & self::Session($extension);

		//	...
		$json = json_encode($session);

		//	...
		$hash = md5($json);
		$hash = substr($hash, 0, 8);

		//	...
		return $hash;
	}

	static private function _RegisterFiles(array $paths)
	{
		//	Save current directory.
		$save_dir = getcwd();

		//	Change client file directory.
		$traces = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
		$file   = $traces[1]['file'];
		chdir( dirname($file) );
		unset($traces, $file);

		//	...
		$session = & self::Session();

		//	...
		foreach( $paths as $path ){
			//	...
			if( strstr($path, '../') ){
				OP()->Notice("Parent directory cannot be specified. ($path)");
				continue;
			}

			//	...
			if( strstr($path, ':/') ){
				$path = OP()->MetaPath($path);
			}

			//	...
			if(!$real_path = realpath($path) ){
				OP()->Notice("This path does not exist. ($path)");
				continue;
			}

			//	Check if hidden file.
			$file_name = basename($real_path);
			$first_cha = $file_name[0];
			if( $first_cha === '.' or $first_cha === '_' ){
				continue;
			}

			//	Directory
			if( is_dir($real_path) ){
				//	...
				if(!empty($session['dir']) ){
					//	...
					if( in_array($real_path, $session['dir']) ){
						continue;
					}
				}

				//	...
				$session['dir'][] = $real_path;
				continue;
			}

			//	Get extension.
			if(!$extension = substr((string)strrchr($real_path, '.'), 1) ){
				continue;
			}

			//	Check if permit extension.
			if(!in_array($extension, ['js','css','md']) ){
				continue;
			}

			//	Remove duplicate files.
			if( $session[$extension] ?? null ){
				if( in_array($real_path, $session[$extension]) ){
					continue;
				}
			}

			//	Add file path.
			$session[$extension][] = $real_path;
		}

		//	Recovery current directory.
		chdir($save_dir);
	}

	static private function _RegisterFilesFromDirectory()
	{
		//	...
		$session = & self::Session();

		//	...
		if( empty($session['dir']) ){
			return;
		}

		//	...
		while( $dir = array_shift($session['dir']) ){
			//	...
			$paths = glob("{$dir}/*");
			self::_RegisterFiles($paths);
		}
	}

	static private function _OutputSourceCode()
	{
		//	...
		switch( $mime = OP()->MIME() ){
			case 'text/css':
				$extension = 'css';
				break;
			case 'text/javascript':
				$extension = 'js';
				break;
			case 'text/markdown':
				$extension = 'md';
				break;
			default:
				OP()->Notice("This MIME is not support. ($mime)");
				return;
		}

		//	...
		self::_RegisterFilesFromDirectory();

		//	...
		$closure = function($file_path){
			include($file_path);
		};

		//	...
		$session = & self::Session($extension) ?? [];

		//	...
		while( $file_path = array_shift($session) ){
			$closure($file_path);
		}
	}

	static private function _Cache(string $extension)
	{
		self::Config('cache');
	}
}
