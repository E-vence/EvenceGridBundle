<?php
/*
 * Copyright (c) 2015
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
namespace Evence\Bundle\GridBundle\Grid\Fields;

use Evence\Bundle\GridBundle\Grid\Grid;

/**
 * Class for DataField (array or entity)
 *
 * @author Ruben Harms <info@rubenharms.nl>
 * @link http://www.rubenharms.nl
 * @link https://www.github.com/RubenHarms
 * @package evence/grid-bundle
 * @subpackage Grid
 */
class DataField extends Field
{


    /**
     * Set object reference
     *
     * @var string
     */
    protected $objectReference = true;
    
    
    
    /*
     * (non-PHPdoc)
     * @see \Evence\Bundle\GridBundle\Grid\Fields\Field::getData()
     */
    public function getValue($source = null)
    {
        return $this->configurator->getGrid()
            ->getValueFromSource($source, $this->identifier);
    }

    public function getObjectReference()
    {
        return $this->objectReference;
    }

    public function setObjectReference($objectReference)
    {
        $this->objectReference = $objectReference;
        return $this;
    }
 
}
    