<?php
/**
 * Original Author: sam
 * Date: 1/2/12
 * Time: 9:23 AM
 */
namespace dapper;

class EnvMock extends Env
{
    function is_commandline_request()
    {
        return true;
    }
}
