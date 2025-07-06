<?php
/** op-unit-webpack:/function/MinifyJS.php
 *
 * @created    2024-05-24
 * @version    1.0
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

/** MinifyJS
 *
 * @creation   2023-05-24
 * @version    1.0
 * @package    op-unit-webpack
 * @author     Tomoaki Nagahara
 * @copyright  Tomoaki Nagahara All right reserved.
 */
function MinifyJS($js) {
	//	Remove comment.
	$js = preg_replace('|/\*.*?\*/|s', '', $js);
	//	Remove single line comment.
	$js = preg_replace('|\s//\s.*$|m', '', $js);
	//	Remove empty line.
	$js = preg_replace('|\n\s*\n|', "\n", $js);
	//	Remove white space of line head.
	$js = preg_replace('|^\s*|m', '', $js);
	//	Remove white space of line tail.
	$js = preg_replace('|\s*$|m', '', $js);
	//	Add ";" to "}" for remove LF.
	$js = preg_replace('/}\n/', "};\n", $js);
	//	Remove LF.
	$js = preg_replace('/([,:;{])\n/', '$1', $js);
	//	For method chain.
	$js = preg_replace('/\)\n.(\w)/', ').$1', $js);
	//	For else.
	$js = preg_replace('/else\n{/', 'else{', $js);
	//	For else if.
	$js = preg_replace('/else\nif/', 'else if', $js);
	//	Compress multiple space character.
	$js = preg_replace('/ +/', ' ', $js);
	//	Remove space of human readable.
	$js = preg_replace('|\s?([,:\-\+=\(\)\<\>])\s?|', '$1', $js);
	//	$OP
	$js = preg_replace('|\$OP|', '\$o1', $js);

	//	Label
	$js.= "\n".'console.log("MinifyJS");';

	//	...
	return $js;
}
