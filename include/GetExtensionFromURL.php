<?php
/** op-unit-webpack:/include/GetExtensionFromURL.php
 *
 * @created    2024-08-06
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

//	Calculate file type from Request URL.
$uri = $_SERVER['REQUEST_URI'];

//	Remove URL Query : /webpack/index.css?layout=flexbox --> /webpack/index.css
if( $pos = strpos($uri, '?') ){
	$uri = substr($uri, 0, $pos);
}

//	Find the file extension.
if( $pos = strrpos($uri, '.') ){
	$ext = substr($uri, $pos +1);
}else{
	//	Remove slash from tail : /webpack/css/ --> /webpack/css
	$uri = rtrim($uri, '/');
	//	Find the file extension from directory : /webpack/css --> css
	if( $pos = strrpos($uri, '/') ){
		$ext = substr($uri, $pos +1);
	}
}

//	If not found.
if( empty($ext) ){
	/*
	OP()->Notice("The extension could not be found. `{$_SERVER['REQUEST_URI']}`");
	*/
	throw new \Exception("The extension could not be found: {$_SERVER['REQUEST_URI']}");
}

//	Return extension.
return $ext ?? null;
