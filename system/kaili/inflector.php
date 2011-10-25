<?php

namespace Kaili;

/**
 * Inflector class
 *
 * @package Kaili
 */
class Inflector
{
    /**
     * Underscorize the provided string
     * @param string $str
     * @param bool $lower_case set if the string must be returned in lower case or not
     * @return string
     */
    public static function underscore($str, $lower_case = true)
    {
        return static::stringify($str, '_', $lower_case);
    }
    
    /**
     * Hyphenate the provided string
     * @param string $str
     * @param bool $lower_case set if the string must be returned in lower case or not
     * @return string
     */
    public static function hyphenate($str, $lower_case = true)
    {
        return static::stringify($str, '-', $lower_case);
    }
    
    /**
     * Stringify the provided string with a separator
     * @param string $str
     * @param string $sep the separator
     * @param bool $lower_case set if the string must be returned in lower case or not
     * @return string
     */
    public static function stringify($str, $sep, $lower_case = true)
    {
        $lower_case and $str = strtolower($str);
        var_dump($str);
        return preg_replace('/[\W\s\_]+/', $sep, $str);
    }
    
    /**
     * Camelize the provided string
     * @param string $str
     * @param bool $first_upper set if the first letter will be with upper case or not 
     * @return string 
     */
    public static function camelcase($str, $first_upper = false)
    {
        $str = preg_replace_callback('/[\W\s\_]*([\w]+)/', 
                function($matches){return ucfirst($matches[1]);}, $str);
        !$first_upper and $str = lcfirst($str);
        return $str;
    }
    
    /**
     * Transform a word from plural to singular form
     * @param string $str a word
     * @return string
     */
    public static function singular($str)
    {
        if($str === null)
            throw new \InvalidArgumentException('A string is required');

        // singular rules
        $rules = array(
            '/(\w+)ies/' => '$1y', 
            '/(\w+[f|x|z|ch|sh|s]{1})es/' => '$1', 
            '/(\w+)s/' => '$1'
        );
        
        $str = strtolower($str);
        foreach($rules as $patt=>$repl){
            if(preg_match($patt, $str))
                return preg_replace($patt, $repl, $str);
        }
    }

    /**
     * Transform a word from singular to plural form
     * @param string $str a word
     * @return string
     */
    public static function plural($str)
    {
        if($str === null)
            throw new \InvalidArgumentException('A string is required');
        
        // plural rules
        $rules = array(
            '/(\w+[aeiou])y/' => '$1s', 
            '/(\w+[^aeiou])y/' => '$1ies', 
            '/(\w+[f|x|z|ch|sh|s])/' => '$1es', 
            '/(\w+)/' => '$1s'
        );
        
        $str = strtolower($str);
        foreach($rules as $patt=>$repl){
            if(preg_match($patt, $str))
                return preg_replace($patt, $repl, $str);
        }
    }

}
