<?php
require_once __DIR__ . "/BaseTestCase.php";

class CoreBasicsTest extends BaseTestCase {
	protected function setUp()
	{
		clear\Core::reset();
		parent::setUp();
	}


	function testSingleton()
	{
		$singleton1 = clear\Core::instance();
		$singleton2 = clear\Core::instance();
		$this->assertSame($singleton1, $singleton2, "The 2 calls to instance()"
			." should retunr the same Object");
	}
	
	/**
     * @expectedException clear\Exception\InvalidStateException
     */
    function testDo_workInvalidStateExceptionThrown()
    {
		clear\Core::instance()
			->do_work(function(){});
    }
	
	function testDo_workArrivesOnLastRoute()
	{
		$work = function(){};
		$core = clear\Core::instance()
			->append_route('GET /abc')
			->do_work($work);
		$initial_workload = $core->last_route()->executable_workload()->initial_closure();
		$this->assertSame($work, $initial_workload, 'The provided work to $core->do_work($work)'
			.' should be the same work returned by $core->last_route()->executable_workload()->initial_closure()');
	}

    /**
     * @expectedException clear\Exception\InvalidStateException
     */
    function testBlindCallToExpose()
    {
		clear\Core::instance()
			->expose('bob');
    }
	
	function testExposeArrivesOnLastRoute()
	{
		$work_to_expose = "user";
		$core = clear\Core::instance()
			->append_route('GET /abc')
			->expose($work_to_expose);
		$exposed_work = $core->last_route()->exposed_work();
		$this->assertEquals($exposed_work, array('user'), 'The returned work to expose'
		 .' should have been an array with one element: array("user")');
	}
	
	function testExposeEmptyStringReturnsEmptyArray()
	{
		$work_to_expose = "";
		$core = clear\Core::instance()
			->append_route('GET /abc')
			->expose($work_to_expose);
		$exposed_work = $core->last_route()->exposed_work();
		$this->assertEquals($exposed_work, array(), 'The returned work to expose'
		 .' should have been an empty array:  array()');
	}
	function testExposeNullReturnsEmptyArray()
	{
		$work_to_expose = null;
		$core = clear\Core::instance()
			->append_route('GET /abc')
			->expose($work_to_expose);
		$exposed_work = $core->last_route()->exposed_work();
		$this->assertEquals($exposed_work, array(), 'The returned work to expose'
		 .' should have been an empty array:  array()');
	}
	
	function testExposeCommaDelimListReturnsPoperArray()
	{
		$work_to_expose = "user, message";
		$core = clear\Core::instance()
			->append_route('GET /abc')
			->expose($work_to_expose);
		$exposed_work = $core->last_route()->exposed_work();
		$this->assertEquals($exposed_work, array("user", "message"),
			'The returned work to expose'
			.' should have been an empty array:  array("user", "message")');
	}
	
	function testExposeSpaceDelimListReturnsPoperArray()
	{
		$work_to_expose = "user   message";
		$core = clear\Core::instance()
			->append_route('GET /abc')
			->expose($work_to_expose);
		$exposed_work = $core->last_route()->exposed_work();
		$this->assertEquals($exposed_work, array("user", "message"),
			'The returned work to expose'
			.' should have been an empty array:  array("user", "message")');
	}
	
	function testExposeTooManyCommasReturnsPoperArray()
	{
		$work_to_expose = "user, ,message,";
		$core = clear\Core::instance()
			->append_route('GET /abc')
			->expose($work_to_expose);
		$exposed_work = $core->last_route()->exposed_work();
		$this->assertEquals($exposed_work, array("user", "message"),
			'The returned work to expose'
			.' should have been an empty array:  array("user", "message")');
	}

    /**
     * @todo Implement testRender().
     */
    function testRender()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testInstance().
     */
    function testInstance()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testAutoload().
     */
    function testAutoload()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testRequest_method().
     */
    function testRequest_method()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testConfig().
     */
    function testConfig()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testAppend_route().
     */
    function testAppend_route()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testRender_view().
     */
    function testRender_view()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testRender_route().
     */
    function testRender_route()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
	
}
 
