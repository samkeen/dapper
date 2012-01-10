<?php
namespace dapper\Render;

require_once __DIR__ . "/../../BaseCase.php";

/**
 * Test class for Route.
 * Generated by PHPUnit on 2011-12-13 at 08:29:38.
 */
class TwigTemplateTest extends \BaseCase {
	
    function testInstantiateNoErrors()
    {
        new TwigTemplate(array(),'');
        // just a sanity test
        $this->assertTrue(true);
    }
    function testRenderView()
    {
        $php_template = new TwigTemplate(
            array(
                'cache' 			=> sys_get_temp_dir(),
                'auto_reload' 		=> true,
                'debug'				=> true,
                'strict_variables'	=> true,
                'autoescape'		=> true,
            ),
            TOP_DIR . '/testing/mocks/view_templates/twig',
            TOP_DIR . '/vendors/twig/lib/Twig/Autoloader.php'
        );
        ob_start();
        $php_template->render_view('users', array('test_message_var' => 'hello'));
        $rendered_view = ob_get_clean();
        $this->assertEquals(
            "This is a test View\nThe \$test_message_var is [hello]",
            $rendered_view
        );
    }
    
}