<?php
namespace Evence\Bundle\GridBundle\Grid;

use Evence\Bundle\GridBundle\Grid\actions\DataField;
use Evence\Bundle\GridBundle\Grid\actions\Field;
use Evence\Bundle\GridBundle\Grid\Type\AbstractType;
use Evence\Bundle\GridBundle\Grid\Type\BooleanType;
use Evence\Bundle\GridBundle\Grid\Type\TextType;
use Evence\Bundle\GridBundle\Grid\Type\ChoiceType;
use Evence\Bundle\GridBundle\Grid\actions\CustomField;

/**
 * Grid field configurator
 *
 * @author Ruben Harms <info@rubenharms.nl>
 * @link http://www.rubenharms.nl
 * @link https://www.github.com/RubenHarms
 * @package Cursuswebsitesbouwen.nl
 * @subpackage EvenceCoreBundle
 */
class GridActionConfigurator implements \Iterator, \ArrayAccess, \Countable
{

    /**
     * @var Grid
     */
    private $grid = null;
    private $actions = array();
    
    private $mappedParameters = array();


    public function addAction($name, $routeName, $routeParameters = array(), $roles = null, $options = array()){
        
    }
    
    
    public function __construct(Grid $grid){
        $this->grid = $grid;
    }  

    /**
     * Get all actions
     *
     * @return multitype:
     */
    public function getactions()
    {
        return $this->actions;
    }
 
    function rewind()
    {
        reset($this->actions);
    }

    function current()
    {
        return current($this->actions);
    }

    function key()
    {
        return key($this->actions);
    }

    function next()
    {
        next($this->actions);
    }

    function valid()
    {
        return key($this->actions) !== null;
    }

    public function hasField($field)
    {
        if (! empty($this->actions[$field])) {}
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->actions[] = $value;
        } else {
            $this->actions[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->actions[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->actions[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->actions[$offset]) ? $this->actions[$offset] : null;
    }
    
    public function getGrid(){
        return $this->grid;
    }
    
    public function count( ){
        return count($this->actions);
    }
   
}
 