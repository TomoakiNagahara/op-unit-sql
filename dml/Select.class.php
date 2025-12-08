<?php
/**
 * unit-sql:/DML/Select.class.php
 *
 * @creation   2019-03-04
 * @license    Apache-2.0
 * @package    op-unit-sql
 * @copyright  (C) 2019 Tomoaki Nagahara
 */

/** namespace
 *
 * @creation  2019-03-04
 */
namespace OP\UNIT\SQL\DML;

/** Used class
 *
 * @created   2019-03-04
 */
use Exception;
use OP\OP_CORE;
use OP\IF_DATABASE;

/** Select
 *
 * @porting  2018-04-20
 */
class Select
{
	/** trait
	 *
	 */
	use OP_CORE;

	/** Generate SQL.
	 *
	 * @param array       $config
	 * @param IF_DATABASE $_DB
	 */
	static function SQL($config, IF_DATABASE $_DB)
	{
		//	...
		foreach( ['table','where','limit'] as $key ){
			if(!isset($config[$key]) ){
				throw new Exception("Has not been set \"{$key}\".");
			};
		};

		//	Pager
		if( isset($config['pager']) ){
			require_once(__DIR__.'/function/Pager.php');
			Pager($config);
		}

		//	TABLE
		$table = Common::Table($config['table'], $_DB);

		//	Field
		$field = Common::Field($config['field'] ?? null, $_DB);

		//	WHERE
		$where = Common::Where($config['where'], $_DB);

		//	LIMIT
		$limit = Common::Limit($config['limit'], $_DB);

		//	GROUP
		$group  = isset($config['group'] ) ? Common::Group ($config['group'] , $_DB) : null;

		//	ORDER
		$order  = isset($config['order'] ) ? Common::Order ($config['order'] , $_DB) : null;

		//	OFFSET
		$offset = isset($config['offset']) ? Common::Offset($config['offset'], $_DB) : null;

		//	...
		return "SELECT {$field} FROM {$table} WHERE {$where} {$group} {$order} {$limit} {$offset}";
	}
}
