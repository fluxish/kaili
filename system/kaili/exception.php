<?php 

namespace Kaili;

/**
 * Kaili Exception Class
 *
 * Generic exception
 *
 * @package     Kaili
 * @subpackage  Logger
 */

class Exception extends \Exception
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
    public function printMessage()
    {
        $str = '<p><span style="font-weight: bold">'.__CLASS__.'</span>'
            . ' in file '. $this->getFile()
            . ' on line '.$this->getLine().'<br/>'
            . '<span style="font-style: italic">'.$this->getMessage().'</span></p>';
        
        
        $trace = $this->getTrace();
        if(!empty($trace)){
            $str .= '<div><span style="font-weight: bold">Stack trace</span><ul>';
            foreach($trace as $t){
                $str .= '<li>'.$t.'</li>';
            }
        }
        
        $str .= '</ul></div><br/><br/>';
        
        return $str;
    }
}

