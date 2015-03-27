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

use Evence\Bundle\GridBundle\Grid\Fields\DataField;
use Evence\Bundle\GridBundle\Grid\Fields\Field;
use Evence\Bundle\GridBundle\Grid\Type\AbstractType;
use Evence\Bundle\GridBundle\Grid\Type\BooleanType;
use Evence\Bundle\GridBundle\Grid\Type\TextType;
use Evence\Bundle\GridBundle\Grid\Type\ChoiceType;
use Evence\Bundle\GridBundle\Grid\Fields\CustomField;
use Evence\Bundle\GridBundle\Grid\Type\DateType;
use Evence\Bundle\GridBundle\Grid\Type\DateTimeType;
use Evence\Bundle\GridBundle\Grid\Type\TimeType;
use Evence\Bundle\GridBundle\Grid\Type\EntityType;
use Evence\Bundle\GridBundle\Grid\Type\MoneyType;
use Evence\Bundle\GridBundle\Grid\Type\LinkType;
use Evence\Bundle\GridBundle\Grid\Type\HtmlType;

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
     *
     * @var Grid
     */
    private $grid = null;

    /**
     * Array of the configured fields
     * 
     * @var array
     */
    private $fields = array();

    /**
     * Add datafield to the grid
     *
     * @param string $alias
     *            Alias or dataname of the datasource
     * @param string $label
     *            Label of the field (for heading in the grid)
     * @param AbstractType|string $type
     *            Desired data type
     * @param array $options            
     * @return \Evence\Bundle\GridBundle\Grid\GridFieldConfigurator
     */
    public function addDataField($alias, $label, $type = null, $options = array())
    {
        $this->fields[$alias] = new DataField($this, $alias, $label);
        if ($this->grid->getSortBy() == $alias) {
            $this->fields[$alias]->setCurrentSort(true);
            $this->fields[$alias]->setCurrentSortOrder($this->grid->getSortOrder());
        }
        if (! empty($options['mapped']))
            $this->fields[$alias]->setMapped($options['mapped']);
        $this->fields[$alias]->setType($this->detectType($type))
            ->getType()
            ->setField($this->fields[$alias])
            ->resolveOptions($options);
        
        return $this;
    }

    /**
     * Add a custom field to the grid
     *
     * @param string $alias
     *            Alias for the custom fieldname
     * @param string $label
     *            Label of the field (for heading in the grid)
     * @param AbstractType|string $type
     *            Desired data type
     * @param callable $callable
     *            Callback to render your custom field
     * @param array $options
     *            Array of options
     * @return \Evence\Bundle\GridBundle\Grid\GridFieldConfigurator
     */
    public function addCustomField($alias, $label, $type, $callable, $options = array())
    {
        $options['mapped'] = false;
        
        $this->fields[$alias] = new CustomField($this, $alias, $label);
        $this->fields[$alias]->setMapped($options['mapped'])
            ->setCallback($callable)
            ->setType($this->detectType($type))
            ->getType()
            ->setField($this->fields[$alias])
            ->resolveOptions($options);
        
        return $this;
    }

    /**
     * Class constructor: Inject services
     *
     * @param Grid $grid            
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * Try to detect the given (data) type and return an object
     *
     * @param string $type            
     * @throws \Exception
     * @return \Evence\Bundle\GridBundle\Grid\Type\AbstractType|\Evence\Bundle\GridBundle\Grid\Type\BooleanType|\Evence\Bundle\GridBundle\Grid\Type\TextType|\Evence\Bundle\GridBundle\Grid\Type\ChoiceType|\Evence\Bundle\GridBundle\Grid\Type\DateType|\Evence\Bundle\GridBundle\Grid\Type\DateTimeType|\Evence\Bundle\GridBundle\Grid\Type\TimeType
     */
    public function detectType($type)
    {
        if (is_object($type)) {
            if (! $type instanceof AbstractType) {
                throw new \Exception('Object is not an instance of Evence\Bundle\GridBundle\Grid\Type\AbstractType');
            }
            return $type;
        }
        
        switch ($type) {
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
            case "date":
                return new DateType();
                break;
            case "datetime":
                return new DateTimeType();
                break;
            case "time":
                return new TimeType();
                break;
            case "entity":
                return new EntityType();
            break;
            case "money":
                return new MoneyType();
            break;
            case "link":
                return new LinkType();
            break;
            case "html":
                return new HtmlType();
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

    /**
     * Reset the actions pointer (Iterator)
     *
     * @see \Iterator
     * @return void
     */
    public function rewind()
    {
        reset($this->fields);
    }

    /**
     * Get the current action (Iterator)
     *
     * @see \Iterator
     * @return mixed
     */
    public function current()
    {
        return current($this->fields);
    }

    /**
     * Get the current key (Iterator)
     *
     * @see \Iterator
     * @return mixed
     */
    public function key()
    {
        return key($this->fields);
    }

    /**
     * Set the interal pointer to the next field (Iterator)
     *
     * @see \Iterator
     * @return void
     */
    public function next()
    {
        next($this->fields);
    }

    /**
     * Whether or not the current key is valid (Iterator)
     *
     * @see \Iterator
     * @return boolean
     */
    public function valid()
    {
        return key($this->fields) !== null;
    }

    /**
     * Whether or not the current field exists.
     *
     * @param string $field            
     */
    public function hasField($field)
    {
        if (! empty($this->fields[$field])) {}
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
            $this->fields[] = $value;
        } else {
            $this->fields[$offset] = $value;
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
        return isset($this->fields[$offset]);
    }

    /**
     * Unset the specified offset
     *
     * @see \ArrayAccess
     * @param string $offset            
     */
    public function offsetUnset($offset)
    {
        unset($this->fields[$offset]);
    }

    /**
     * Get the specified offset
     *
     * @see \ArrayAccess
     * @param string $offset            
     * @return Field
     */
    public function offsetGet($offset)
    {
        return isset($this->fields[$offset]) ? $this->fields[$offset] : null;
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
        return count($this->fields);
    }
}
 