<?php
/*
 * Copyright (c) 2015 - Ruben Harms <info@rubenharms.nl>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */
namespace Evence\Bundle\GridBundle\Grid;

use Evence\Bundle\GridBundle\Grid\actions\DataField;
use Evence\Bundle\GridBundle\Grid\actions\Field;
use Evence\Bundle\GridBundle\Grid\Type\AbstractType;
use Evence\Bundle\GridBundle\Grid\Type\BooleanType;
use Evence\Bundle\GridBundle\Grid\Type\TextType;
use Evence\Bundle\GridBundle\Grid\Type\ChoiceType;
use Evence\Bundle\GridBundle\Grid\actions\CustomField;
use Evence\Bundle\GridBundle\Grid\Misc\Action;

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
     * The current grid object
     *
     * @var Grid
     */
    private $grid = null;

    /**
     * Array of all configured actions
     *
     * @var multitype:Action
     */
    private $actions = array();

    /**
     * Array of the mapped parameters
     *
     * @var unknown
     */
    private $mappedParameters = array();

    /**
     * Add an action to the grid
     *
     * @param string $identifier
     *            Unique action name
     * @param string $label
     *            Label name to display in the grid
     * @param string $routeName
     *            Symfony route name
     * @param array $routeParameters
     *            (optional) Route parameters for the specified router
     * @param string $roles
     *            (optional) Required roles to do this action (symfony's securityContext)
     * @param array $options
     *            Options for the action
     * @return \Evence\Bundle\GridBundle\Grid\GridActionConfigurator
     */
    public function addAction($identifier, $label, $routeName, $routeParameters = array(), $roles = null, $options = array())
    {
        $action = new Action($this, $identifier, $label, $options);
        $action->setRoute($routeName)
            ->setRouteParameters($routeParameters)
            ->setRoles($roles);
        
        $this->actions[$identifier] = $action;
        
        return $this;
    }
    

    /**
     * Add a multiple action to the grid
     *
     * @param string $identifier
     *            Unique action name
     * @param string $label
     *            Label name to display in the grid
     * @param string $routeName
     *            Symfony route name
     * @param array $routeParameters
     *            (optional) Route parameters for the specified router
     * @param string $roles
     *            (optional) Required roles to do this action (symfony's securityContext)
     * @param array $options
     *            Options for the action
     * @return \Evence\Bundle\GridBundle\Grid\GridActionConfigurator
     */
    public function addMultipleAction($identifier, $label, $routeName, $routeParameters = array(), $roles = null, $options = array())
    {       
        $options['multiple'] = true;
        return $this->addAction($identifier, $label, $routeName, $routeParameters, $roles, $options);
    }
    

    /**
     * Set mapped parameters
     *
     * @param array $parameters
     *            Could be an assocative array or non assocative array: array('paramname1' => 'fieldname1', 'paramname2' => 'fieldname2' )
     */
    public function setMappedParameters($parameters)
    {
        $this->mappedParameters = $parameters;
    }

    /**
     * Get mapped parameters
     *
     * @return \Evence\Bundle\GridBundle\Grid\unknown
     */
    public function getMappedParameters()
    {
        return $this->mappedParameters;
    }

    /**
     * Get route parameters by source
     *
     * @param sting $source            
     * @return multitype Array of parameters with theire values
     */
    public function getParametersBySource($source)
    {
        $pArray = array();
        foreach ($this->mappedParameters as $key => $value) {
            
            $val = $this->grid->getColBySource($source, $value);
            if (! is_numeric($key))
                $pArray[$key] = $val;
            else
                $pArray[$value] = $val;
        }
        return $pArray;
    }

    /**
     * Class constructor: Service injections
     *
     * @param Grid $grid            
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * Get all actions
     *
     * @return multitype:
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Reset the actions pointer (Iterator)
     *
     * @see \Iterator
     * @return void
     */
    public function rewind()
    {
        reset($this->actions);
    }

    /**
     * Get the current action (Iterator)
     *
     * @see \Iterator
     * @return mixed
     */
    public function current()
    {
        return current($this->actions);
    }

    /**
     * Get the current key (Iterator)
     *
     * @see \Iterator
     * @return mixed
     */
    public function key()
    {
        return key($this->actions);
    }

    /**
     * Set the interal pointer to the next action (Iterator)
     *
     * @see \Iterator
     * @return void
     */
    public function next()
    {
        next($this->actions);
    }

    /**
     * Whether or not the current key is valid (Iterator)
     *
     * @see \Iterator
     * @return boolean
     */
    public function valid()
    {
        return key($this->actions) !== null;
    }

    /**
     * Whether or not the current action exists.
     *
     * @param string $action            
     */
    public function hasAction($action)
    {
        if (! empty($this->actions[$action])) {}
    }

    /**
     * Set the specified offset
     *
     * @see \ArrayAccess
     * @param string $offset            
     * @param mixed $value            
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->actions[] = $value;
        } else {
            $this->actions[$offset] = $value;
        }
    }

    /**
     * Whether or not the specified offset exists
     *
     * @see \ArrayAccess
     * @param string $offset            
     */
    public function offsetExists($offset)
    {
        return isset($this->actions[$offset]);
    }

    /**
     * Unset the specified offset
     *
     * @see \ArrayAccess
     * @param string $offset            
     */
    public function offsetUnset($offset)
    {
        unset($this->actions[$offset]);
    }

    /**
     * Get the specified offset
     * 
     * @see \ArrayAccess
     * @param string $offset            
     * @return Action
     */
    public function offsetGet($offset)
    {
        return isset($this->actions[$offset]) ? $this->actions[$offset] : null;
    }

    /**
     * Get the current grid
     *
     * @return \Evence\Bundle\GridBundle\Grid\Grid
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * Counts the number of elements in the array
     *
     * @return integer
     * @see \Countable
     */
    public function count()
    {
        return count($this->actions);
    }
    
    /**
     * Get col value by source
     * 
     * @param mixed $source Source (row) 
     * @param string $col Key name of the desired col
     */
    public function getColBySource($source, $col) {
        return $this->getGrid()->getColBySource($source, $col);
    }
    
}
 