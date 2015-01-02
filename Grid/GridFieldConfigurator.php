<?php
namespace Evence\Bundle\GridBundle\Grid;

use Evence\Bundle\GridBundle\Grid\Fields\DataField;
use Evence\Bundle\GridBundle\Grid\Fields\Field;
use Evence\Bundle\GridBundle\Grid\Type\AbstractType;
use Evence\Bundle\GridBundle\Grid\Type\BooleanType;
use Evence\Bundle\GridBundle\Grid\Type\TextType;
use Evence\Bundle\GridBundle\Grid\Type\ChoiceType;
use Evence\Bundle\GridBundle\Grid\Fields\CustomField;

/**
 * Grid field configurator
 *
 * @author Ruben Harms <info@rubenharms.nl>
 * @link http://www.rubenharms.nl
 * @link https://www.github.com/RubenHarms
 * @package Cursuswebsitesbouwen.nl
 * @subpackage EvenceCoreBundle
 */
class GridFieldConfigurator implements \Iterator, \ArrayAccess, \Countable
{

    /**
     * @var Grid
     */
    private $grid = null;
    private $fields = array();

    public function addDataField($alias, $label, $type = null, $options = array())
    {
        $this->fields[$alias] = new DataField($this, $alias, $label);
        if($this->grid->getSortBy() == $alias){
            $this->fields[$alias]->setCurrentSort();
            $this->fields[$alias]->setCurrentSortOrder($this->grid->getCurrentSortOrder());
          
        }
        if (!empty($options['mapped'])) $this->fields[$alias]->setMapped($options['mapped']);
        $this->fields[$alias]->setType($this->detectType($type))->getType()->setField($this->fields[$alias])->resolveOptions($options);
        
        return $this;
    }

    public function addCustomField($alias, $label, $type, $callable, $options = array())   {
      
        
        $options['mapped'] = false;
        
        $this->fields[$alias] = new CustomField($this, $alias, $label);
        $this->fields[$alias]->setMapped($options['mapped'])->setCallback($callable)->setType($this->detectType($type))->getType()->setField($this->fields[$alias])->resolveOptions($options);
        
       return $this;
    }
    
    
    public function __construct(Grid $grid){
        $this->grid = $grid;
    }
    
    public function detectType($type){
        
        if(is_object($type)){
            if(! $type instanceof AbstractType){
                throw new \Exception('Object is not an instance of Evence\Bundle\GridBundle\Grid\Type\AbstractType');
            }            
            return $type;
        }
        
        switch($type){
            case "boolean":
                return new BooleanType();                
            break;
            case "":            
            case "text":
                return new TextType();
            break;
            case "choice":
                return new ChoiceType();
            break;
            
        }
        
        throw new \Exception('Non existing type ' . $type);
        
    }
    

    /**
     * Get all fields
     *
     * @return multitype:
     */
    public function getFields()
    {
        return $this->fields;
    }
 
    function rewind()
    {
        reset($this->fields);
    }

    function current()
    {
        return current($this->fields);
    }

    function key()
    {
        return key($this->fields);
    }

    function next()
    {
        next($this->fields);
    }

    function valid()
    {
        return key($this->fields) !== null;
    }

    public function hasField($field)
    {
        if (! empty($this->fields[$field])) {}
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->fields[] = $value;
        } else {
            $this->fields[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->fields[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->fields[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->fields[$offset]) ? $this->fields[$offset] : null;
    }
    
    public function getGrid(){
        return $this->grid;
    }
    
    public function count( ){
        return count($this->fields);
    }
   
}
 