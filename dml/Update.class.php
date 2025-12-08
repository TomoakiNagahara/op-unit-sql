<?php
/**	op-unit-sql:/DML/Update.class.php
 *
 * @creation   2019-03-04
 * @license    Apache-2.0
 * @package    op-unit-sql
 * @copyright  (C) 2019 Tomoaki Nagahara
 */

/**	Declare strict type
 *
 */
declare(strict_types=1);

/**	Namespace
 *
 * @creation  2019-03-04
 */
namespace OP\UNIT\SQL\DML;

/**	Use
 *
 * @created   2019-03-04
 */
use Exception;
use OP\OP_CORE;
use OP\IF_DATABASE;

/**	Update
 *
 * @creation  2018-04-20
 */
class Update
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
	static function SQL( $config, IF_DATABASE $_DB )
	{
		//	...
		foreach( ['table','set','where','limit'] as $key ){
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

		//	SET
		$set   = Common::Set(Common::SetUniform($config['set']), $_DB);

		//	OFFSET
		if( isset($config['offset']) ){
			throw new Exception("OFFSET can not be used in UPDATE.");
		};

		//	...
		switch( $driver = $_DB->Config()['driver'] ){
			case 'mysql':
				$limit = Common::Limit($config['limit'], $_DB);
				$order = isset($config['order']) ? Common::Order($config['order'], $_DB) : null;
				break;
			default:
				throw new Exception("This driver has not been support LIMIT and ORDER: {$driver}");
		}

		//	...
		return "UPDATE {$table} {$set} WHERE {$where} {$order} {$limit}";
	}
}
