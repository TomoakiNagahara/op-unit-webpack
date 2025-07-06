<?php
/** op-unit-webpack:/config.php
 *
 * @created    2024-01-23
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

//	In case of JavaScript.
$js = [
	'debug' =>  false,	 // for debug flag
	'cache' =>  true,	 // file, apcu, memcache
	'minify'=>  true,	 // File compression. Remove space character and comment.
];

//	In case of style sheet.
$css = [
	'debug' =>  false,	 // for debug flag
	'cache' =>  true,	 // file, apcu, memcache
	'minify'=>  true,	 // File compression. Remove space character and comment.
];

//	In case of is Admin.
$admin = [
//	'debug'  => false,
	'cache'  => false,
//	'minify' => true,
];

//	...
$config = [
	'js'    => $js,
	'css'   => $css,
	'admin' => $admin,
];

//	...
return $config;
