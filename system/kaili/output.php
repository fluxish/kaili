<?php

namespace Kaili;

/**
 * Kaili Output Class
 *
 * Manage output processes
 *
 * @package		Kaili
 */
class Output
{

    /**
     * @var string
     */
    private $_output;

    /**
     * @var array
     */
    private $_headers;

    /**
     * @var Loader
     */
    private $_load;

    public function __construct()
    {
        $this->_load = Loader::get_instance();
        $this->_headers = array();

        $this->_load->helper('url');
    }

    /**
     * Append a buffer to final output
     * @param string a buffer
     */
    public function append($buff)
    {
        if(empty($this->_output)) {
            $this->_output = $buff;
        }
        else {
            $this->_output .= $buff;
        }
    }

    /**
     * Display the final output
     */
    public function display()
    {
        foreach($this->_headers as $header) {
            header($header, true);
        }
        echo $this->_output;
    }

    public function set_header($header)
    {
        $this->_headers[] = $header;
    }

    public function redirect_to($url)
    {
        $this->set_header('Location: '.abs($url));
    }

    public function redirect_to_referer()
    {
        $this->set_header('Location: '.Loader::get_instance()->load('input')->referer());
    }

}

