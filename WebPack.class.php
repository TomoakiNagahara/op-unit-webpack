<?php
/** op-unit-webpack:/WebPack.class.php
 *
 * @creation  2018-04-12
 * @version   1.0
 * @package   op-unit-webpack
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** namespace
 *
 * @created   2018-04-13
 */
namespace OP\UNIT;

/** Used class
 *
 */
use OP\OP_CORE;
use OP\OP_UNIT;
use OP\OP_SESSION;
use OP\IF_UNIT;
use OP\Env;
use OP\Config;
use OP\Notice;
use function OP\Unit;
use function OP\Template;
use function OP\ConvertPath;
use function OP\CompressPath;

/** WebPack
 *
 * @creation  2018-04-12
 * @version   1.0
 * @package   unit-webpack
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
class WebPack implements IF_UNIT
{
	/** trait
	 *
	 */
	use OP_CORE, OP_UNIT, OP_SESSION;

	/** WebPack directory.
	 *
	 *  For separate each WebPack directory.
	 *
	 * <pre>
	 * //  Instantiate
	 * $webpack1 = Unit::Instantiate('WebPack');
	 * $webpack2 = Unit::Instantiate('WebPack');
	 *
	 * //  Set different directory.
	 * $webpack1->Directory('app:/webpack1/');
	 * $webpack2->Directory('app:/webpack2/');
	 *
	 * //  Output different webpack.
	 * $webpack1->Out();
	 * $webpack2->Out();
	 * </pre>
	 *
	 * @created   2020-02-07
	 * @param     string
	 * @return    string
	 */
	function Directory($path=null)
	{
		//	...
		static $_directory;

		//	...
		if( $path ){
			$_directory = ConvertPath(path);
		}

		//	...
		if(!$_directory ){
			//	...
			$config = Config::Get('webpack');

			//	...
			if( $_directory = $config['directory'] ?? null ){
				$_directory = ConvertPath($_directory);
			}
		}

		//	...
		if(!$_directory ){
			Notice::Set("WebPack directory is not set.\n Env::Set('webpack',['directory'=>\$path])");
		}

		//	...
		return $_directory;
	}

	/** Automatically registration and output.
	 *
	 * @param  string|array  $list
	 */
	function Auto($list)
	{
		//	...
		foreach( is_string($list) ? explode(',', $list): $list as $path ){
			//	...
			$path = trim($path);

			//	...
			if( strpos(" $path", '..') ){
				Notice::Set("Can not specify parent directory. ($path)");
				continue;
			}

			//	...
			if(!file_exists($path) ){
				Notice::Set("This file has not been exists. ($path)");
				continue;
			};

			//	...
			$path = realpath($path);

			//	...
			$pos  = strrpos($path, '.');
			$ext  = substr($path, $pos+1);
			$path = substr($path, 0, $pos);

			//	...
			$this->Set($ext, $path);
		};
	}

	/** Set js file path.
	 *
	 * @param string $path
	 */
	function Js($path)
	{
		$this->Set('js', $path);
	}

	/** Set css file path.
	 *
	 * @param string $path
	 */
	function Css($path)
	{
		$this->Set('css', $path);
	}

	/** Set file path. Store to session.
	 *
	 * @param string       $extension
	 * @param string|array $file_path
	 * @param boolean      $prepend true is head, false is foot.
	 */
	function Set($ext, $path, $prepend=false)
	{
		//	Check extension.
		if( empty($ext) ){
			Notice::Set("Has not been set extension.");
			return;
		}

		//	Get session by extension.
		$session = & $this->Session($ext);

		//	For Eclipse (Undefined error)
		$list = [];

		//	Convert to array.
		if( is_string($path) ){
			//	String to array.
			$list[] = $path;
		}else if( is_array($path) ){
			//	Array to array.
			$list = $path;
		}else{
			//	Empty array.
			$list = [];
		};

		//	Add to head or foot.
		if( empty($list) ){
			//	...
		}else if( empty($session) ){
			$session = $list;
		}else if( $prepend ){
			$session = array_merge( $list, $session );
		}else{
			$session = array_merge( $session, $list );
		}

		/** The array_unique function is remove duplicate value from array.
		 *
		 *  Do not use array_search function.
		 *  1. Because, the search for duplicate values of arrays is only string. (needle is not support array)
		 *  2. And, passed argument value there is a already duplicate possibility.
		 */
		$session = array_unique($session);
	}

	/** Get packed string each extension.
	 *
	 * @param  string $extension
	 * @return string $string
	 */
	function Get($ext)
	{
		//	...
		if(!ob_start()){
			Notice::Set("ob_start was failed.");
			return;
		}

		//	...
		$this->Out($ext);

		//	...
		return ob_get_clean();
	}

	/** Output packed string each extension.
	 *
	 * @param string $extension
	 */
	function Out($ext)
	{
		//	...
		static $_is_admin;

		//	...
		if( $_is_admin === NULL ){
			$_is_admin = Env::isAdmin();
		}

		//	Get target directory.
		$list = [];
		$path = self::Directory();
		$path = "{$path}/{$ext}/action.php";
		if( file_exists($path) ){
			$list = include($path);
		};

		//	...
		foreach( array_merge($list, ($this->Session($ext) ?? [])) as $file_path ){
			//	...
			if( $_is_admin ){
				echo "/* $file_path, $ext */\n";
			}

			//	...
			$file_path .= ".{$ext}";
			$meta_path  = CompressPath($file_path);

			//	...
			if(!$meta_path ){
				Notice::Set("This file is not git root path. ($file_path)");
				continue;
			};

			//	...
			Template($meta_path);

			//	...
			echo "\n";
		}

		//	Set empty array.
		$this->Session($ext, []);
	}

    /** Out by directory.
     *
     */
    private function _OutDir(array $pathes, string $ext):void
    {
        //  ...
        foreach( $pathes as $path ){
            //  ...
            if(!is_dir($path) ){
                Notice("This path is not directory. ($path)");
                continue;
            }

            //  ...
            $path = rtrim($path, '/');

            //  ...
            foreach( glob("{$path}/*.{$ext}") as $file ){
                self::_OutFile($file);
            }
        }
    }

	/** Generate unique hash key by stacked files name.
	 *
	 * @deprecated 2020-05-23
	 * @param   string     $extension
	 * @return  string     hash
	 */
	function FileListHash($ext)
	{
		//	...
		static $_hash;

		//	...
		if( $_hash ?? null ){
			$temp = $this->Session($ext);
			$temp = json_encode($temp);
			$temp = md5($temp);
			$temp = substr($temp, 0, 8);
			$_hash = $temp .'.'. (int)Env::Get('webpack')['serial'];
		};

		//	...
		return $_hash;
	}

	/** Generate unique hash key by stacked files.
	 *
	 * @created  2019-04-06
	 * @param    string      $extension
	 * @return   string      $hash
	 */
	function FileContentHash($ext)
	{
		//	...
		static $_hash;

		//	...
		if( empty($_hash[$ext]) ){

			//	...
			$session = $this->Session($ext);

			//	Generate hash by content.
			$_hash[$ext] = substr(md5($this->Get($ext)), 0, 8);

			//	...
			$this->Session($ext, $session);
		};

		//	...
		return $_hash[$ext];

		/*
		//	...
		$session = $this->Session($ext);

		//	...
		$hash = substr(md5($this->Get($ext)), 0, 8);

		//	...
		$this->Session($ext, $session);

		//	...
		return $hash;
		*/
	}
}
