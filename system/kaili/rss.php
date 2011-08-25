<?php  

namespace Kaili;

/**
 * Kaili Rss Class
 *
 * Class to manage rss items and feeds.
 *
 * @package		Kaili
 * @subpackage	Library
 * @category	Library
 * @author		Luigi Marco Simonetti
 */

class Rss
{
    /**
     * Required fields of rss channel
     * @var _rss_fields
     * @access private
     */
    private $_rss_fields = array('title', 'link', 'description');
    
    /**
     * All fields of rss channel
     * @var _rss_channel_fields_opt
     * @access private
     */
    private $_rss_channel_fields_opt = array('title', 'link', 'description', 'language', 'copyright', 'managingEditor',
        'webMaster', 'pubDate', 'lastBuildDate', 'category', 'generator', 'docs', 
        'cloud', 'ttl', 'image', 'rating', 'skipHours', 'skipDays');
        
    /**
     * All fields of rss items
     * @var _rss_item_fields_opt
     * @access private
     */
    private $_rss_item_fields_opt = array('title', 'link', 'description', 'author', 'category', 'comments', 
        'enclosure', 'guid', 'pubDate', 'source');
    
    /**
     * Reference docs for RSS 2.0 Specification
     * @var _docs
     * @access private
     */
    private $_docs = 'http://blogs.law.harvard.edu/tech/rss';
    
    /**
     * Setted fields for rss channel
     * @var _channel
     * @access private
     */
    private $_channel = array();
    
    /**
     * List of items
     * @var _items
     * @access private
     */
    private $_items = array();
    
    /**
     * Map attribute/field for items
     * @var _item_fields_map
     * @access private
     */
    private $_item_fields_map = array();
    
    /**
     * Config object to access to configuration variables
     * @var _config
     * @access private
     */
    private $_config;
    
    
    function __construct()
    {
        $this->_config = Loader::get_instance()->load('config');
    }
    
    /**
     * Set channel attributes
     * @param fields an associative array that contain the channel's attributes (key)
     * and their values
     */
    function set_channel($fields)
    {
        // check required fields
        foreach($this->_rss_fields as $field){
            if(!array_key_exists($field, $fields))
                throw new Exception('Field "'.$field.'" is required in a rss channel.');
        }
        
        // add fields and ignore invalid optional fields
        foreach($fields as $field=>$value){
            if(in_array($field, $this->_rss_channel_fields_opt)){
                $this->_channel[$field] = $value;
            }
            else throw new Exception('Field "'.$field.'" is not valid.');
        }
        // add autogenerated fields
        $this->_channel['managingEditor'] = $this->_config->item('webmaster_mail');
        $this->_channel['webMaster'] = $this->_config->item('webmaster');
        $this->_channel['generator'] = $this->_config->item('environment_name');
        $this->_channel['docs'] = $this->_docs;
    }
    
    /**
     * Set items list and references between rss attributes and real fields of the items
     * @param items an array with items
     * @param fields_map an associative array with attribute-field pairs.
     */
    function set_items($items, $fields_map)
    {
        // set items data
        if(is_object($items)){
            $this->_items = array($items);
        } 
        else{
            $this->_items = $items;
        }
        
        // check required fields to map
        /*
        foreach($this->_rss_fields as $field){
            if(!array_key_exists($field, $fields_map))
                throw new Exception('Field "'.$field.'" is required in a rss item.');
        }*/
        
        // add fields to map and ignore invalid optional fields
        foreach($fields_map as $field=>$mapped){
            if(in_array($field, $this->_rss_channel_fields_opt)){
                $this->_item_fields_map[$field] = $fields_map[$field];
            }
            else throw new Exception('Field "'.$field.'" is not valid.');
        }
    }
    
    /**
     * Generate the feed rss
     * @param file the path of the file in witch save the rss feeds
     * @return if file is null, returns a string with entire rss feed, otherwise
     *     return true if the rss file is saved or false.
     */
    function generate($file = null)
    {
        // create rss root
        $rss = simplexml_load_string("<?xml version=\"1.0\" encoding=\"UTF-8\"?><rss></rss>");
        $rss->addAttribute('version', '2.0');
        
        // create channel
        $channel = $rss->addChild('channel');
        
        if($this->_channel == null) $this->set_channel();
        foreach($this->_channel as $attr=>$value){
            $channel->addChild($attr, $value);
        }
        $channel->addChild('lastBuildDate', date(DATE_RSS));
        
        foreach($this->_items as $item_data){
            $item = $channel->addChild('item');
            
            foreach($this->_item_fields_map as $field=>$mapped){
                $item->addChild($field, htmlentities($item_data->$mapped));
            }
        }
        
        // save rss feed file
        $dom = dom_import_simplexml($rss)->ownerDocument;
        $dom->formatOutput = true;
        
        if($file != null)
            return $dom->save($file) > 0;
        else
            return $dom->saveXML();
    }
    
    /**
     * Set rss feed and generate it
     * @param fields an associative array that contain the channel's attributes (key)
     * and their values
     * @param items an array with items     
     * @param fields_map an associative array with attribute-field pairs.
     * @param file the path of the file in witch save the rss feeds
     * @return if file is null, returns a string with entire rss feed, otherwise
     *     return true if the rss file is saved or false.
     */
    function set_and_generate($channel, $items, $fields_map, $file = null)
    {
        $this->set_channel($channel);
        $this->set_items($items, $fields_map);
        $this->generate($file);
    }
}
