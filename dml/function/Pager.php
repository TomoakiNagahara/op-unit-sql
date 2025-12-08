<?php
/** op-unit-sql:/dml/function/Pager.php
 *
 * @creation   2020-06-06
 * @license    Apache-2.0
 * @package    op-unit-sql
 * @copyright  (C) 2020 Tomoaki Nagahara
 */

/** namespace
 *
 */
namespace OP\UNIT\SQL\DML;

/** Pager
 *
 * @created   2020-06-06
 * @param     array        $config
 */
function Pager(&$config){
	//	...
	$pager  = $config['pager'];
	$limit  = $config['limit'];

	//	...
	$config['offset'] = ($limit * $pager) - $limit;
}
