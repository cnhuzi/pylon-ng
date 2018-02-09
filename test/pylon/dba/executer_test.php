<?php

use pylon\driver\XFastSQLExecutor ;
use PHPUnit\Framework\TestCase;

class TestLogger
{
    public $_msgs = array();
    public function log($msg)
    {
        $this->_msgs[]=$msg;
    }
}
class  DBExecuterTC extends TestCase
{
	private $_exeManager;
	private $_executer;
    private $_tableName;
    private $_cnStr;

	public function __construct()
	{
        $this->_tableName = "db_executer_test";
        $this->_cnStr='GBKÖÐÎÄ';

        $host     = XSetting::ensureEnv("DB_HOST") ;
        $user     = XSetting::ensureEnv("DB_USER") ;
        $password = XSetting::ensureEnv("DB_PWD") ;
        $name     = XSetting::ensureEnv("DB_NAME") ;
        $this->_executer = new XFastSQLExecutor( $host,$user,$password,$name);
	}

	public function setUp()
	{
        $cmds[] = "drop table if exists {$this->_tableName}";
        $cmds[] = "create table {$this->_tableName}
                (
                   id                             integer(11),
                   obj                            varchar(30),
                   step							  integer(11),
                   mydesc                           varchar(255)
                ) DEFAULT CHARSET=gbk
                ";

        $this->assertFalse($this->_executer == NULL);
        $this->_executer->exeNoQuerys($cmds);
        $this->_cnStr='GBKÖÐÎÄ';

        $cnStr=$this->_cnStr;
        for($i=0; $i<10; $i++)
        {
            $cmd = "insert {$this->_tableName}(id, obj, step,mydesc) values($i, 'test', 10,'$cnStr')";
            $this->_executer->exeNoQuery($cmd);
        }
	}

	public function tearDown()
	{
        $cmd = "drop table if exists {$this->_tableName}";
        $this->_executer->exeNoQuery($cmd);
	}

	public function testQuery()
	{
        $cmd = "select * from {$this->_tableName} where id = 1";
		if(($rs = $this->_executer->query($cmd)) == NULL)
	   	{
			$this->assertTrue(false);
			return;
		}
        $this->assertTrue($rs["id"] == 1);
        $this->assertEquals($rs["mydesc"] , $this->_cnStr);
	}

	public function testQuerys()
	{
        $cmd = "select * from {$this->_tableName}";
		if(($rs = $this->_executer->querys($cmd)) == NULL)
	   	{
			$this->assertTrue(false);
			return;
		}
        $this->assertTrue(count($rs) == 10);
	}
}

?>
