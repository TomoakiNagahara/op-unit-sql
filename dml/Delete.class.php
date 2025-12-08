<?php
/**
 * unit-sql:/DML/Delete.class.php
 *
 * @created    2019-03-04
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

/** Delete
 *
 */
class Delete
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
				OP()->Error("This value has not been set: {$key}");
				return;
			};
		};

		//	TABLE
		$table = Common::Table($config['table'], $_DB);

		//	WHERE
		$where = Common::Where($config['where'], $_DB);

		//	LIMIT
		$limit = Common::Limit($config['limit'], $_DB);

		//	OFFSET
		if( isset($config['offset']) ){
			throw new Exception("OFFSET can not be used in DELETE.");
		};

		//	...
		if( 'mysql' === ($prod = $_DB->Config()['scheme']) ){
			//	LIMIT
			$limit = Common::Limit($config['limit'], $_DB);

			//	ORDER
			$order = isset($config['order']) ? Common::Order($config['order'], $_DB) : null;
		}else{
			//	...
			if( isset($config['limit']) or isset($config['order']) ){
				throw new Exception("This product has not been support LIMIT and ORDER. ({$prod})");
			};

			//	...
			$order = $limit = null;
		};

		return "DELETE FROM {$table} WHERE {$where} {$order} {$limit}";
	}
}
