<?php
/** op-unit-webpack:/config.php
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

//	In case of JavaScript.
$js = [
	'cache' => '', // file, apcu, memcache
];

//	In case of style sheet.
$css = [
	'cache' => '', // file, apcu, memcache
];

//	In case of is Admin.
$admin = [

];

//	...
$config = [
	'js'    => $js,
	'css'   => $css,
	'admin' => $admin,
];

//	...
return $config;
