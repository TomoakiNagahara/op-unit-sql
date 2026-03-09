<?php
/**	op-unit-sql:/ddl/Show.class.php
 *
 * @created   2019-04-08  Implement to IF.
 * @version   1.0
 * @package   op-unit-sql
 * @author    Tomoaki Nagahara
 * @copyright Tomoaki Nagahara All rights reserved.
 */

/**	Namespace
 *
 * @created   2019-04-08
 */
namespace OP\UNIT\SQL\DDL;

/**	Use
 *
 * @created   2019-04-08
 */
use Exception;
use OP\OP_CORE;
use OP\IF_DATABASE;
use OP\IF_SQL_DDL_SHOW;

/**	Show
 *
 * @created   2019-04-08
 */
class Show implements IF_SQL_DDL_SHOW
{
	/**	trait
	 *
	 */
	use OP_CORE;

	/**	Database
	 *
	 * @creation  2019-04-09
	 * @var      \OP\IF_DATABASE
	 */
	private $_DB;

	/**	Construct.
	 *
	 * @created  2019-04-09
	 * @param    IF_DATABASE $_DB
	 */
	public function __construct( ?IF_DATABASE & $_DB=null )
	{
		$this->_DB = & $_DB;
	}

	/**	Generate Show Database SQL.
	 *
	 * {@inheritDoc}
	 * @see \OP\IF_SQL_DDL_SHOW::Database()
	 * @porting   2019-04-08
	 */
	public function Database( string $label='default' ) : string
	{
		//	...
		$db     = OP()->Unit()->Database();
		$pdo    = $db->PDO($label);
		$driver = $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);

		//	...
		switch( $driver ){
			case 'mysql':
				$sql = 'SHOW DATABASES';
				break;

			case 'pgsql':
				$sql = 'SELECT * FROM "pg_database"';
				break;

			default:
				throw new Exception("This driver is not yet supported: {$driver}");
		};

		//	...
		return $sql;
	}

	/**	Generate Show Table SQL.
	 *
	 * {@inheritDoc}
	 * @see \OP\IF_SQL_DDL_SHOW::Table()
	 * @porting   2019-04-09
	 */
	public function Table( string $database='', string $label='default' ) : string
	{
		//	...
		$db     = OP()->Unit()->Database();
		$pdo    = $db->PDO($label);
		$driver = $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);

		//	...
		switch( $driver  ){
			case 'mysql':
				$database = $this->_DB->Quote($database);
				$sql = "SHOW TABLES FROM {$database}";
				break;

			case 'pgsql':
				$sql = 'SELECT * FROM "pg_stat_user_tables"';
				break;

			case 'sqlite':
				$sql = "SELECT * FROM 'sqlite_master' WHERE type='table'";
				break;

			default:
				throw new Exception("This driver is not yet supported: {$driver}");
		};

		//	...
		return $sql;
	}

	/**	Generate Show Column SQL.
	 *
	 * {@inheritDoc}
	 * @see \OP\IF_SQL_DDL_SHOW::Column()
	 * @porting   2019-04-09
	 */
	public function Column( string $table='', string $database='', string $label='default' ) : string
	{
		//	...
		$db     = OP()->Unit()->Database();
		$pdo    = $db->PDO($label);
		$driver = $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);

		//	Loop at each key string.
		foreach(['database','table'] as $key){
			//	Trim white space.
			${$key} = trim(${$key});

			//	Check if first character is quote.
			if( preg_match('/^[^_a-z0-9]/i', ${$key}) ){
				//	Trim quote string. `t_table` --> t_table
				${$key} = substr(${$key}, 1, strlen(${$key})-2);
			};
		};

		//	Branch to each database.
		switch( $driver ){
			case 'mysql':
				$database = $db->Quote($database, $label);
				$table    = $db->Quote($table   , $label);
				$sql = "SHOW FULL COLUMNS FROM {$database}.{$table}";
				break;

			case 'pgsql':
				$table    = $pdo->quote($table);
				$sql = "SELECT * FROM information_schema.columns WHERE table_name = {$table}";
				break;

			case 'sqlite':
				$table    = $db->Quote($table, $label);
				$sql = "PRAGMA TABLE_INFO({$table})";
				break;

			default:
				throw new Exception("This driver is not yet supported: {$driver}");
		};

		//	...
		return $sql;
	}

	/**	Generate Show Index SQL.
	 *
	 * @created  ????
	 * @copied   2019-04-09
	 * @param    array      $config
	 * @return   string     $sql
	 */
	public function Index(array $config=[])
	{
		if( 1 ){
			$database = $this->_DB->Quote($config['database']);
			$table    = $this->_DB->Quote($config['table']   );
			return "SHOW INDEX FROM {$database}.{$table}";
		}else{
			/*
			//	...
			if( $database ){
				$database = 'table_schema='.$db->PDO()->Quote($database);
			};

			//	...
			if( $table ){
				$database.= ' AND ';
				$database.= 'table_name='  .$db->PDO()->Quote($table);
			};

			//	...
			return "SELECT * FROM information_schema.statistics WHERE {$database}";
			*/
		};
	}

	/**	Generate Show Variables SQL.
	 *
	 * @creation 2019-01-08
	 * @param    array      $config
	 * @return   string     $sql
	 */
	public function Variables(array $config=[])
	{

	}

	/**	Generate Show Status SQL.
	 *
	 * @creation 2019-01-08
	 * @param    array      $config
	 * @return   string     $sql
	 */
	public function Status(array $config=[])
	{

	}

	/**	Generate Show Grants SQL.
	 *
	 * @creation 2019-01-08
	 * @param    array      $config
	 * @return   string     $sql
	 */
	public function Grants(array $config=[])
	{
		$user = $this->_DB->PDO()->Quote($config['user']);
		$host = $this->_DB->PDO()->Quote($config['host']);
		return "SHOW GRANTS FOR {$user}@{$host}";
	}

	/**	Generate Show User SQL.
	 *
	 * @creation  ????
	 * @updation  2019-04-09
	 * @param     array       $config
	 * @return    string      $sql
	 */
	public function User(array $config=[])
	{
		//	...
		switch( $prod = $this->_DB->Config()['prod'] ){
			case 'mysql':
				//	...
				$version = $this->_DB->Version();

				//	...
				if( $this->_DB->isMariaDB() ){
					$version = 1;
				}

				//	...
				if( version_compare($version, '5.7.0') >= 0) {
					//	MySQL 5.7.0 higher
					$sql = "SELECT `host`, `user`, `authentication_string` as 'password' FROM `mysql`.`user`";
				}else{
					//	MySQL 5.7.0 under
					$sql = "SELECT `host`, `user`, `password` FROM `mysql`.`user`";
				}

				/*
				//	...
				$sql = "SELECT `host`, `user`, `password`, `authentication_string` FROM `mysql`.`user`";
				*/
				break;

			case 'pgsql':
				$sql = 'SELECT * FROM "pg_shadow"';
				break;

			default:
				$sql = false;
				throw new Exception("This product has not been support yet. ($prod)");
		}

		//	...
		return $sql;
	}

	/**	Generate Show Password SQL.
	 *
	 * @created   ????-??-??  OP\UNIT\SQL\Select
	 * @updated   2019-04-09  OP\UNIT\SQL\DDL\Show
	 * @param     array       $config
	 * @return    string      $sql
	 */
	public function Password(array $config=[])
	{
		//	...
		$password = $this->_DB->PDO()->Quote($config['password']);

		//	...
		$version = $this->_DB->Version();

		//	...
		if( $this->_DB->isMariaDB() ){
			$version = 1;
		}

		//	...
		if( version_compare($version, '5.7.0') >= 0) {
			$sql = "SELECT CONCAT('*', UPPER(SHA1(UNHEX(SHA1({$password})))))";
		}else{
			$sql = "SELECT PASSWORD({$password})";
		}

		//	...
		return $sql;
	}
}
