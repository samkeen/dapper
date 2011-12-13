<?php
require_once __DIR__ . "/BaseTestCase.php";

class CoreBasicsTest extends BaseTestCase {
	
	function testSingleton()
	{
		$singleton1 = clear\Core::instance();
		$singleton2 = clear\Core::instance();
		$this->assertSame($singleton1, $singleton2, "The 2 calls to instance()"
			." should retunr the same Object");
	}
	
	/**
     * @todo Implement testDo_work().
     */
    function testDo_work()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testExpose().
     */
    function testExpose()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
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
 
