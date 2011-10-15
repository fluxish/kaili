<?php

namespace Kaili;

/**
 * Kaili Controller Class
 *
 * The Controller Class for Kaili MVC Pattern
 *
 * @package		Kaili
 */
class Controller
{

    /**
     * The Request object
     * @var Kaili\Request
     */
    protected $request;
    
    /**
     * The Response object
     * @var Kaili\Response
     */
    protected $response;
    
    /**
     * Create a new Controller object
     */
    function __construct()
    {
        $this->request = Request::current();
        $this->response = new \Kaili\Response();
    }

    function __destruct()
    {
        $this->response->send();
    }

}

