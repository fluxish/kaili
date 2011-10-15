<?php

namespace Kaili;

/**
 * Response Class
 * The Response class manage the response process
 *
 * @package Kaili
 */
class Response
{

    /**
     * The body of the response
     * @var string
     */
    private $_body;

    /**
     * Array of headers
     * @var array
     */
    private $_headers;
    
    /**
     * Response statuses
     * @var array
     */
    private $_statuses = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Checkpoint',
        122 => 'Request-URI too long',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
        226 => 'IM Used',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
        308 => 'Resume Incomplete',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		507 => 'Insufficient Storage',
		509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended'
	);

    /**
     * Create a new Response object
     */
    public function __construct()
    {
        $this->_headers = array();
        $this->_body = '';
    }

    /**
     * Append content to response body
     * @param string $buff the content to add
     */
    public function append($buff)
    {
        if($buff instanceof Kaili\View) {
            $buff = $buff->render();
        }
        $this->_body .= $buff;
    }

    /**
     * Send the response
     */
    public function send($status_code = 200)
    {
        // Set the http protocol and the status
        $this->set_protocol($status_code);
        
        // TODO: manage various content types
        $this->set_content_type('text/html');

        foreach($this->_headers as $header) {
            header($header, true);
        }
        echo $this->_body;
    }

    /**
     * Add a new header
     * @param string $header 
     */
    public function set_header($header)
    {
        $this->_headers[] = $header;
    }
    
    public function set_protocol($status_code)
    {
        $server_protocol = Request::current()->server('SERVER_PROTOCOL') 
            ? Request::current()->server('SERVER_PROTOCOL') : 'HTTP/1.1';
        header($server_protocol.' '.$status_code.' '.$this->_statuses[$status_code]);
    }
    
    /**
     * Set the content-type of the response
     * @param string $content_type
     */
    public function set_content_type($content_type)
    {
        $this->_headers[] = 'Content-Type: '.$content_type;
    }
    
    /**
     * Redirect the response to specific URL
     * @param string $url 
     */
    public function redirect_to($url)
    {
        $this->set_header('Location: '.Kaili\Url::abs($url));
    }

    /**
     * Redirect the response to the referer URL
     */
    public function redirect_to_referer()
    {
        $this->set_header('Location: '.Request::current()->referer());
    }

}

