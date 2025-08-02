<?php
/** op-unit-webpack:/WEBPACK2024.trait.php
 *
 * @created    2024-01-22
 * @version    2.0
 * @package    op-unit-webpack
 * @author     Tomoaki Nagahara
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
 * @author     Tomoaki Nagahara
 * @copyright  Tomoaki Nagahara All right reserved.
 */
trait WEBPACK_2024
{
	/** trait
	 *
	 */
	use OP_SESSION;

	/** Auto
	 *
	 * @param  array  ...$args
	 */
	static public function Auto(...$args)
	{
		//	...
		if( empty($args) ){
			$extension = null;
			self::Prepare($extension);
			self::Output($extension);
		}else{
			self::Register($args);
		}
	}

	/** Hash
	 *
	 * @param   string  $extension
	 * @return  string
	 */
	static public function Hash(string $extension) : string
	{
		//	Get session by extension.
		$session = & self::Session($extension);

		//	...
		$json = json_encode($session);

		//	...
		$hash = md5($json . $extension);
		$hash = substr($hash, 0, 8);

		//	...
		return $hash;
	}

	/** Register() method is register files landing method.
	 *
	 * <pre>
	 * Changes the current directory to the directory where it was called.
	 * </pre>
	 *
	 * @param  string|array  $paths
	 */
	static public function Register($paths)
	{
		//	Save current directory.
		$save_dir = getcwd();

		//	Change client file directory.
		$traces = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

		//	...
		$asset_root = OP()->Path('asset:/');
		$asset_root = realpath($asset_root);

		//	Search the called directory.
		foreach( $traces as $trace ){
			/*
			//	...
			$class    = $trace['class']    ?? null;
			$function = $trace['function'] ?? null;

			//	...
			if( $class !== __CLASS__ ){
				continue;
			}

			//	...
			if( $function !== __FUNCTION__ or $function !== 'Auto' ){
				continue;
			}
			*/

			//	...
			$file     = $trace['file']     ?? null;

			//	core
			if( 0 === strpos($file, $asset_root.'/core/') ){
				continue;
			}

			//	unit/webpack
			if( 0 === strpos($file, $asset_root.'/unit/webpack/') ){
				continue;
			}

			//	Change current directory.
			chdir( dirname($file) );

			//	...
			break;
		}

		//	unset
		unset($traces, $trace);

		//	Real register files method.
		switch( $type = gettype($paths) ){
			case 'array':
				self::_RegisterFiles($paths);
				break;
			case 'string':
				self::_RegisterFiles([$paths]);
				break;
			default:
				OP()->Error("This argument type is not supported. `{$type}`");
		}

		//	Recovery current directory.
		chdir($save_dir);
	}

	/** Real register files method.
	 *
	 * @param  array  $paths
	 */
	static private function _RegisterFiles(array $paths)
	{
		//	...
		$session = & self::Session();

		//	...
		foreach( $paths as $path ){
			//	...
			if( strstr($path, '../') ){
				OP()->Error("Parent directory cannot be specified: {$path}");
				continue;
			}

			//	...
			if( strstr($path, ':/') ){
				$path = OP()->Path($path);
			};

			//	...
			if( strstr($path, '*') ){
				self::_RegisterFiles( glob($path) );
				continue;
			}

			//	...
			if(!$real_path = realpath($path) ){
				OP()->Error("This path does not exist: {$path}");
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

				//	...
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
			}else{
				//	Init
				$session[$extension] = [];
			}

			//	Add file path.
			if( $extension === 'css' and basename($real_path) === 'import.css' ){
				array_unshift($session[$extension], $real_path);
			}else{
				array_push($session[$extension], $real_path);
			}
		}
	}

	/** Register files from directory.
	 *
	 */
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

	/** Output source code.
	 *
	 * @created    2024-??-??
	 * @changed    2024-08-05  private _OutputSourceCode() --> public Output()
	 */
	static public function Output(string $extension)
	{
		//	...
		$configs = OP()->Config('WebPack');
		$config  = $configs[$extension] ?? [];

		//	...
		if( OP()->isAdmin() ){
			$config = array_merge($config, $configs['admin'] ?? []);
		}

		//	...
		$debug = OP()->isAdmin() ? $config['debug'] ?? null: false;
		$cache = $config['cache'] ?? null;

		//	...
		if( $cache and $hash = OP()->Request('hash') ){
			/* @var $io boolean */
			echo apcu_fetch($hash, $io);
			//	...
			if( $debug ){
				D( $io ? 'Hit cache':'No cache' );
			}
			//	...
			if( $io ){
				return;
			}
		}else{
			if( $debug ){
				D('cache is false');
			}
		}

		//	...
		$hash = self::Hash($extension);

		//	...
		self::_RegisterFilesFromDirectory();

		//	...
		$closure = function($file_path){
			require_once($file_path);
			echo PHP_EOL;
		};

		//	...
		if(!$session = & self::Session($extension) ?? [] ){
			return;
		}

		//	...
		$minify = $config['minify'] ?? null;

		//	...
		ob_start();

		//	...
		while( $file_path = array_shift($session) ){
			//	...
			$file_name = basename($file_path);
			if( '_' === $file_name[0] ){
				continue;
			}

			//	...
			if( $debug ){
				echo "/* $file_path */\n";
			}
			//	...
			$closure($file_path);
		}

		//	...
		$content = ob_get_clean();

		//	...
		if( $minify ){
			//	...
			$minify = 'Minify' . strtoupper($extension);
			require_once(__DIR__."/function/{$minify}.php");
		//	$content = $minify( $content );

			//	...
			if( $minify === 'MinifyJS' ){
				$content = MinifyJS( $content );
			}else
			if( $minify === 'MinifyCSS' ){
				$content = MinifyCSS( $content );
			}else{

			}
		}

		//	...
		apcu_store($hash, $content);

		//	...
		echo $content;
	}

	/** Prepare output
	 *
	 * <pre>
	 * 1. Set MIME from extension.
	 * 1. Set layout execute.
	 * 1. Set layout js and css.
	 * </pre>
	 *
	 * @created    2024-08-06
	 */
	static public function Prepare(?string & $extension)
	{
		//	Layout is change to off.
		OP()->Layout(false);

		//	Get extension from request URL.
		$extension = require(__DIR__.'/include/GetExtensionFromURL.php');

		//	Set MIME
		OP()->MIME($extension);

		//	Get specified layout name.
		if( $layout = OP()->Request('layout') ){
			self::Register("asset:/layout/{$layout}/{$extension}/");
		}
	}

	/** Cache
	 *
	 * @param  string  $extension
	 */
	static private function _Cache(string $extension)
	{
		self::Config('cache');
	}

	/** Get file list from extension.
	 *
	 * @created    2024-11-05
	 * @param      string     $extension
	 * @return    &array      $file_list
	 */
	static function & FileList(string $extension)
	{
		return self::Session($extension);
	}
}
