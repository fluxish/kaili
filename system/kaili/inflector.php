<?php

namespace Kaili;

/**
 * Inflector class
 *
 * @package Kaili
 */
class Inflector
{
    
    public static function underscore($str, $lower_case = true)
    {
        return static::stringify($str, '_', $lower_case);
    }
    
    public static function hyphenate($str, $lower_case = true)
    {
        return static::stringify($str, '-', $lower_case);
    }

    public static function stringify($str, $sep, $lower_case = true)
    {
        $lower_case and $str = strtolower($str);
        var_dump($str);
        return preg_replace('/[\W\s\_]+/', $sep, $str);
    }
    
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
        $str = strtolower($str);
        $last_letter = substr($str, -1);
        switch($last_letter) {
            case 'y':
                $vowels = array('a', 'e', 'i', 'o', 'u');
                if(in_array(substr($str, -2, 1), $vowels))
                    return $str.'s';
                else
                    return substr($str, 0, -1).'ies';
                break;
            case 'h':
                $l = substr($str, -2, 1);
                if($l=='c' || $l=='s') return $str.'es';
                break;
            case 'f':
            case 'x':
            case 'z':
            case 's':
                return $str.'es';
                break;
            default:
                return $str.'s';
        }
        
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
