<?php

class DBUtilTest extends PHPUnit_Framework_TestCase
{
    function test()
    {
        $creator = new DBCreator\DBCreator;
        $conn = $creator->create( 'mysql' , array(
            'database' => 'mysql_test',
            'username' => 'root',
            'password' => '123123',
        ));
        ok($conn);
        $creator->dropFromConnection( $conn , 'mysql_test' );
    }
}

