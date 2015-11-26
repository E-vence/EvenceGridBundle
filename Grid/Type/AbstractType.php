<?php
/*
 Copyright (c) 2015 - Ruben Harms <postbus@rubenharms.nl>

 Permission is hereby granted, free of charge, to any person obtaining a copy
 of this software and associated documentation files (the "Software"), to deal
 in the Software without restriction, including without limitation the rights
 to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the Software is
 furnished to do so, subject to the following conditions:


 The above copyright notice and this permission notice shall be included in
 all copies or substantial portions of the Software.

 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 THE SOFTWARE.
 */

namespace Evence\Bundle\GridBundle\Grid\Type;


use Symfony\Component\OptionsResolver\OptionsResolver;
use Evence\Bundle\GridBundle\Grid\Fields\Field;
use Evence\Bundle\GridBundle\Grid\Misc\Value;
/**
 * Abstract class for type
 *
 * @author Ruben Harms <info@rubenharms.nl>
 * @link http://www.rubenharms.nl
 * @link https://www.github.com/RubenHarms
 * @package project_name
 * @subpackage SUBPACKAGE_NAME
 */ 
abstract class AbstractType implements InterfaceType
{
    
    /**
     * @var Field Field class
     */
    private $field = null;
    
    
    /**
     * @var string Value 
     */
    private $value = null;
    
    /**
     * Renders type
     *
     * @param string $value
     * @param string $source
     * 
     * @return mixed
     */
    abstract public function renderType($value, $source);
    
    
    /**
     * Get name of type
     * 
     * @return string name
     */
    abstract public function getName();

    /**
     * Get field
     * 
     * @return Field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set field
     * 
     * @param Field $field
     * @return AbstractType
     */
    public function setField(Field $field)
    {
        $this->field = $field;
        return $this;
    }
 
    
    public function configureOptions(OptionsResolver $resolver){
       
    }    
    
    public function resolveOptions($options){
        $resolver = new OptionsResolver();
        $resolver->setDefault('objectReference', true);
        $resolver->setDefault('mapped', true);
        $resolver->setDefault('foot', false);
        $resolver->setDefault('align', false);
        $resolver->setDefault('class', '');        
        $this->configureOptions($resolver);
        
        
        $this->options = $resolver->resolve($options);
    }
    
    public function getData($value, $source = null){
        $val = new Value();
        $val->setType($this)->setOriginal($value)->setValue($this->renderType($value, $source));

        return $val;
    }
    
    public function getOptions(){
        return $this->options;
    }
    
    public function getOption($name){
       if (!isset($this->options[$name])){
        throw new \Exception('Non existing option '. $name);    
       }
       return $this->options[$name];
       
    }
    
}

?>