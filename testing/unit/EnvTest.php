<?php
namespace dapper;

require_once __DIR__ . "/../BaseCase.php";

/**
 * Test class for Route.
 * Generated by PHPUnit on 2011-12-13 at 08:29:38.
 */
class EnvTest extends \BaseCase {
	
    function testInstantiateNoErrors()
    {
        new Env();
        // just a sanity test
        $this->assertTrue(true);
    }
    function testAutoloadGetFalseForNonExistentClass()
    {
        $this->assertFalse(Env::autoload('not_a_real_class'));
    }
    function testAutoloadGetNotFalseForNonExistentClass()
    {
        $this->assertTrue(Env::autoload('dapper\Router')!==false);
    }

    function testRequestMethodReturnsNullIfUnknown()
    {
        $env = new Env();
        $this->assertNull($env->request_method());
    }
    function testRequestMethodReturnsProperValueFromSeverContext()
    {
        $_SERVER['REQUEST_METHOD'] = 'boo';
        $env = new Env();
        $this->assertEquals('boo', $env->request_method());
    }
    function testRequestPathReturnsNullIfUnknown()
    {
        $env = new Env();
        $this->assertNull($env->request_path());
    }
    function testRequestPathReturnsProperValueFromRequestContext()
    {
        $_GET[Env::REQUEST_PATH_KEY] = 'boo';
        $env = new Env();
        $this->assertEquals('boo', $env->request_path());
    }

    /**
     * @todo Implement testLog().
     */
    function testLog()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
    
    function testIsDevReturnsFalseByDefault()
    {
        $env = new Env();
        $this->assertFalse($env->is_dev());
    }
    
    function testIsDevReturnsTrueViaOptionalConstructorParam()
    {
        $env = new Env($is_dev = true);
        $this->assertTrue($env->is_dev());
    }

    function testIsCliRequestReturnsTrueForAUnitTest()
    {
        $env = new Env($is_dev = true);
        $this->assertTrue($env->is_cli_request());
    }

    public function testIsCgiRequestReturnsFalseForAUnitTest()
    {
        $env = new Env($is_dev = true);
        $this->assertFalse($env->is_cgi_request());
    }
    
    function testIsCommandLineRequestReturnsFalseForAUnitTest()
    {
        $env = new Env($is_dev = true);
        $this->assertFalse($env->is_commandline_request());
    }
    
    function testCommandLineSendUsageError()
    {
        /*
         * EnvMock just overrides is_commandline_request() to return 
         * true.  Otherwise it is identical
         */
        require_once TOP_DIR.'/testing/mocks/EnvMock.php';
        $GLOBALS['argv']=array('index.php');
        $GLOBALS['argc']=count($GLOBALS['argv']);
        ob_start();
        new EnvMock();
        $output = ob_get_clean();
        $this->assertRegExp('/USAGE:/', $output);
    }
    function testCommandLineSetsExpectedMethodAndPath()
    {
        /*
         * EnvMock just overrides is_commandline_request() to return 
         * true.  Otherwise it is identical
         */
        require_once TOP_DIR.'/testing/mocks/EnvMock.php';
        $GLOBALS['argv']=array('index.php', 'get', '/user');
        $GLOBALS['argc']=count($GLOBALS['argv']);
        $env = new EnvMock();
        
        $this->assertEquals('get', $env->request_method());
        $this->assertEquals('/user', $env->request_path());
    }

}
