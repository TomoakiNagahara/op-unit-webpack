<?php
/** op-unit-webpack:/testcase/action.php
 *
 * @creation  2020-05-22
 * @version   1.0
 * @package   op-unit-webpack
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** namespace
 *
 */
namespace OP;

/* @var $webpack \OP\UNIT\WebPack */
$webpack = Unit('WebPack');
$webpack->Auto('testcase.js');
$webpack->Auto('testcase.css');

D($_SESSION);

?>
<div id="testcase">
	<div class="webpack">
		<p>TESTCASE</p>
		<div class="js" >JS  : </div>
		<div class="css">CSS : </div>
	</div>
</div>
