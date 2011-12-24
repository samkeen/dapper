<?php
namespace clear;
require_once __DIR__ . "/../BaseUnitTestCase.php";

class CoreRenderTest extends \BaseUnitTestCase {
	
	/**
	 * @var clear\Core
	 */
	private $core;
	
	protected function setUp()
	{
		parent::setUp();
		$this->core = new Core('GET', array());
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
 
