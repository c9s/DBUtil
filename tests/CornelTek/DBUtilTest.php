<?php
use CornelTel\DBUtil;

class DBUtilTest extends PHPUnit_Framework_TestCase
{
    function test()
    {
        $creator = new CornelTek\DBUtil;
        $conn = $creator->create( 'mysql' , array(
            // connection string
            'username' => 'root',
            'password' => '123123',
            'database' => 'mysql_test',
        ));
        ok($conn);
        $creator->dropFromConnection( $conn , 'mysql_test' );
    }
}

