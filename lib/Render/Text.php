<?php
/**
 * Original Author: sam
 * Date: 12/30/11
 * Time: 4:36 PM
 * 
 * @package clear
 * @subpackage Render
 * 
 */
namespace clear\Render;

/**
 * Just text output.  No attempts to add markup of any kind
 * 
 * @package clear
 * @subpackage Render 
 */
class Text extends BaseRender
{
    /**
     * @param string $view_name
     * @param array $payload
     */
    function render_view($view_name, array $payload = array())
    {
        // TODO: Implement render_view() method.
    }

    /**
     * @param int $error_code
     * @param string $error_message
     */
    function render_error($error_code, $error_message)
    {
        // TODO: Implement render_error() method.
    }

}
