<?php
namespace Evence\Bundle\GridBundle\Grid;

use Evence\Bundle\GridBundle\Grid\Fields\DataField;
use Evence\Bundle\GridBundle\Grid\Fields\Field;

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

    public function addDataField($alias, $label, $options = array())
    {
        $this->fields[$alias] = new DataField($this, $alias, $label);
        if($this->grid->getSortBy() == $alias){
            $this->fields[$alias]->setCurrentSort();
            $this->fields[$alias]->setCurrentSortOrder($this->grid->getCurrentSortOrder());
        }
        
        return $this;
    }

    public function addCustomField($alias, $label, $callable, $options = array())
    {
        $options['callback'] = $options;
        $this->fields[$alias] = new Field($this, $alias, $label, $options);
    }
    
    
    public function __construct(Grid $grid){
        $this->grid = $grid;
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
 