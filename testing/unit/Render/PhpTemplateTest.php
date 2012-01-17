<?php
namespace dapper\Render;

require_once __DIR__ . "/../../BaseCase.php";

/**
 * Test class for Route.
 * Generated by PHPUnit on 2011-12-13 at 08:29:38.
 */
class TwigRenderTest extends \BaseCase {
	
    function testInstantiateNoErrors()
    {
        new PhpTemplate(array());
        // just a sanity test
        $this->assertTrue(true);
    }
    function testRenderView()
    {
        $php_template = new PhpTemplate(
            array('template_dir' => TOP_DIR.'/testing/mocks/view_templates'));
        ob_start();
        $php_template->render_result('users', array('test_message_var' => 'hello'));
        $rendered_view = ob_get_clean();
        $this->assertEquals(
            "This is a test View\nThe \$test_message_var is [hello]",
            $rendered_view
        );
    }
    
}
