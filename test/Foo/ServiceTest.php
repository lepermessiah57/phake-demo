<?php
namespace Foo;

use \Phake;

class ServiceTest extends \PHPUnit_Framework_TestCase {

	private $service;
	private $dao;

	public function setUp(){
		$this->dao = Phake::mock('Foo\Dao');
		$this->service = new Service($this->dao);
	}


	public function testVerifyingAMethodCall(){
		$this->service->callDaoGet();

		Phake::verify($this->dao)->get();
	}

	public function testStubbingOutAMethod(){
		Phake::when($this->dao)->get()->thenReturn('FOO');
		$data = $this->service->callDaoGet();

		$this->assertEquals('FOO', $data);
	}

	public function testConsecutiveCalls(){
		Phake::when($this->dao)->get()->thenReturn('FOO')->thenReturn('BAR');
		$data = $this->service->callDaoGet();

		$this->assertEquals('FOO', $data);
		
		$data = $this->service->callDaoGet();

		$this->assertEquals('BAR', $data);	
	}

	public function testThrowingExceptions(){
		Phake::when($this->dao)->get()->thenThrow(new \Exception('Should Pass This Exception Threw'));

		try{
			$this->service->callDaoGet();
			$this->fail();
		}catch(\Exception $e){
			$this->assertEquals('Should Pass This Exception Threw', $e->getMessage());
		}
	}

	public function testConsecutiveCallsWithException(){
		Phake::when($this->dao)->get()->thenReturn('FOO')->thenThrow(new \Exception('No more data'));
		$data = $this->service->callDaoGet();

		$this->assertEquals('FOO', $data);
		
		try{
			$this->service->callDaoGet();
			$this->fail();
		}catch(\Exception $e){
			$this->assertEquals('No more data', $e->getMessage());
		}
	}

	public function testVerifyingWithParameters(){
		$sql = 'HERE IS SOME SQL';
		$this->service->runSql($sql);

		Phake::verify($this->dao)->runSql($sql);
	}

	public function testStubbingWithParameters(){
		$sql = 'HERE IS SOME SQL';
		Phake::when($this->dao)->runSql($sql)->thenReturn(10);
		$actual = $this->service->runSql($sql);

		$this->assertEquals(10, $actual);
	}

	public function testStubbingMultipleCalls(){
		$sql = 'FOO';
		$sql2 = 'BAR';

		Phake::when($this->dao)->runSql($sql)->thenReturn(10);
		Phake::when($this->dao)->runSql($sql2)->thenReturn(20);
		$actual = $this->service->runSql($sql);

		$this->assertEquals(10, $actual);
		$actual = $this->service->runSql($sql2);

		$this->assertEquals(20, $actual);
	}

	public function testGetAndInsert(){
		$data = 123;
		Phake::when($this->dao)->get()->thenReturn($data);

		$this->service->addToTable('FOO');

		Phake::verify($this->dao)->insert('FOO', $data);
	}

	public function testDoNotInsertIfDataIsNull(){
		$data = null;
		Phake::when($this->dao)->get()->thenReturn($data);

		$this->service->addToTable('FOO');

		Phake::verify($this->dao, Phake::never())->insert('FOO', $data);
	}

	public function testVerifyingOrder(){
		$data = 123;
		Phake::when($this->dao)->get()->thenReturn($data);

		$this->service->addToTable('FOO');

		Phake::inOrder(
			Phake::verify($this->dao)->get(),
			Phake::verify($this->dao)->insert('FOO', $data)
		);
	}

	public function testCapturingParameters(){
		$this->service->runMySql();
		Phake::verify($this->dao)->runSql(Phake::capture($sql));

		$this->assertEquals('select fail from status', $sql);
	}

	public function testVerifyNoFurtherActions(){
		$this->service->addToTable('FOO');

		Phake::verify($this->dao)->get();

		Phake::verifyNoFurtherInteraction($this->dao);
	}

	public function testVerifyNoInteractionSoFar(){
		Phake::verifyNoInteraction($this->dao);
	}
		
}