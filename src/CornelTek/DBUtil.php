<?php
namespace CornelTek;
use PDO;
use Exception;


/**
 * Util functions for creating database:
 *
 * $util = new DBUtil;
 *
 */
class DBUtil
{

    public function create( $driverType , $options ) {
        switch( $driverType ) {
            case 'sqlite':
                return $this->createSqliteDb( $options );
                break;
            case 'mysql':
                return $this->createMysqlDb( $options );
                break;
            case 'pgsql':
                return $this->createPgsqlDb( $options );
                break;
            default:
                throw new Exception("Unknwon driver type");
        }
    }


    /**
     *
     * @param string $type driver type
     * @param array $options 
     *       database:
     *       username:
     *       password:
     *
     * @return PDO
     */
    public function createConnection($type, array $options = array()) {
        if (!isset($options['host'])) {
            $options['host'] = 'localhost';
        }
        switch($type) {
        case 'sqlite':
            $db = isset($options['database']) ? $options['database'] : ':memory:';
            $pdo = new PDO("sqlite:$db");
            $pdo->setAttribute( PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION );
            return $pdo;
            break;
        case 'mysql':
            $pdo = new PDO("mysql:host=".@$options['host'], @$options['username'] , @$options['password'] , @$options['attributes'] );
            $pdo->setAttribute( PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION );
            return $pdo;
            break;
        case 'pgsql':
            $pdo = new PDO("pgsql:host=".@$options['host'], @$options['username'] , @$options['password'] , @$options['attributes'] );
            $pdo->setAttribute( PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION );
            return $pdo;
            break;
        default:
            throw new Exception("Unsupported driver type");
        }
    }

    public function createSqliteDb( $options ) {
        return $this->createConnection('sqlite',$options);
    }

    public function createMysqlDb( $options ) {
        $pdo = $this->createConnection( 'mysql', $options );

        $db      = $options['database']; // database name is required
        $charset = @$options['charset'] ?: 'utf8'; // database name is required
        $sql = sprintf('CREATE DATABASE %s ', $db );
        if( isset( $options['charset'] ) )
            $sql .= ' CHARSET ' . $options['charset'];
        else
            $sql .= ' CHARSET utf8';
        $result = $pdo->query($sql);
        return $pdo;
    }

    public function createPgsqlDb( $options ) {
        $db      = $options['database']; // database name is required
        $owner   = @$options['owner'];
        $template = @$options['template'];
        $pdo = $this->createConnection( 'pgsql' , $options );

        $sql = 'CREATE DATABASE ' . $db;

        if( isset($options['owner']) )
            $sql .= ' OWNER ' . $options['owner'];
        if( isset($options['template']) )
            $sql .= ' TEMPLATE ' . $options['template'];
        if( isset($options['encoding']) )
            $sql .= ' ENCODING ' . $options['encoding'];
        if( isset($options['connection_limit']))
            $sql .= ' CONNECTION LIMIT ' . $options['connection_limit'];

        $result = $pdo->query($sql);
        return $pdo;
    }

    public function drop( $type , $options ) {
        $pdo = $this->createConnection($type ,$options);
        $dbname = $options['database'];
        $this->dropFromConnection( $pdo, $dbname );
    }

    public function dropFromConnection($pdo,$dbname)
    {
        $driverName = $pdo->getAttribute( PDO::ATTR_DRIVER_NAME );
        switch( $driverName ) {
            case 'sqlite':
                if( $dbname != ':memory' && file_exists($dbname) )
                    unlink($dbname);
            break;
            case 'mysql':
            case 'pgsql':
                $pdo->query( "DROP DATABASE $dbname;" );
            break;
            default:
                throw new Exception("Unsupported driver $driverName");
                break;
        }
    }

}

