<?php
/** op-unit-webpack:/WebPack2024.class.php
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
namespace OP\UNIT;

/** use
 *
 */
use OP\OP_CORE;
use OP\IF_UNIT;
use OP\IF_WEBPACK;
use OP\UNIT\WEBPACK\WEBPACK_2024;

/** Include
 *
 */
include_once(__DIR__.'/WEBPACK_2024.trait.php');

/** WebPack
 *
 * @created    2024-01-22
 * @version    2.0
 * @package    op-unit-webpack
 * @author     Tomoaki Nagahara
 * @copyright  Tomoaki Nagahara All right reserved.
 */
class WebPack implements IF_UNIT, IF_WEBPACK
{
	/** trait
	 *
	 */
	use OP_CORE;
	use WEBPACK_2024;
}
