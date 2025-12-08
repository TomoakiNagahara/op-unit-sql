<?php
/**
 * unit-sql:/DML/Insert.class.php
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

/** Insert
 *
 */
class Insert
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
	static function SQL(array $config, IF_DATABASE $_DB)
	{
		//	...
		if(!isset($config['table']) ){
			throw new Exception("A table name has not been set.");
		};

		//	...
		switch( $_DB->Config()['driver'] ){
			case 'mysql':
				return self::_MySQL($config, $_DB);

			default:
				return self::_Other($config, $_DB);
		};
	}

	static private function _MySQL(array $config, IF_DATABASE $_DB)
	{
		//	...
		if( empty($config['table']) ){
			throw new \Exception('"table" is not set in config.');
		}

		//	...
		if( empty($config['set']) ){
			throw new \Exception('"set" is not set in config.');
		}

		//	Init
		$fields = $values = $set = $update = null;

		//	TABLE
		$table = Common::Table($config['table'] ?? null, $_DB);

		//	SET
		if( isset($config['set']) ){
			$config['set'] = Common::SetUniform($config['set']);
			$set = Common::Set($config['set'], $_DB);
		};

		//	Values
		if( isset($config['values']) ){
			$set = Common::Values($config['set'], $_DB);
		};

		//	...
		if( $config['update'] ?? null ){
			$update = self::_Update($config, $_DB);
		};

		//	...
		return "INSERT INTO {$table} {$fields} {$values} {$set} {$update}";
	}

	static private function _Other(array $config, IF_DATABASE $_DB)
	{
		throw new Exception("Current driver has not been support.");
	}

	static private function _Update(array $config, IF_DATABASE $_DB)
	{
		//	...
		$sets = null;

		/*
		//	...
		foreach( explode(',', $config['update'] ?? null) as $field ){
			//	...
			foreach( $config['set'] as $set ){
				//	...
				$set   = trim($set);
				$field = trim($field);

				//	...
				if( strpos($set, $field) === 0 ){
					$sets[] = $set;
				};
			};
		};
		*/

		//	Correspond to string and array
		if( is_string($config['update']) ){
			$config['update'] = explode(',', $config['update']);
		};

		//	Update value can be set freely.
		foreach( $config['update'] as $field => $value ){
			//	...
			$value = trim($value);

			//	...
			if( $config['set'][$value] ?? null ){
				$field = $value;
				$value = $config['set'][$value];
			}

			//	...
			if( is_string($field) ){
				$sets[$field] = $value;
			}else{
				$sets[] = $value;
			}
		}

		//
		$sets = Common::SetUniform($sets);

		//	...
		return "ON DUPLICATE KEY UPDATE " . substr(Common::Set($sets, $_DB), 4);
	}
}
