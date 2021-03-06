<?php
/*
 Copyright (c) 2015 - Ruben Harms <info@rubenharms.nl>

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

use Doctrine\ORM\PersistentCollection;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Entity Type class
 *
 * @author Ruben Harms <info@rubenharms.nl>
 * @link http://www.rubenharms.nl
 * @link https://www.github.com/RubenHarms
 * @package evence/grid-bundle
 * @subpackage Type
 */
class EntityType extends AbstractType 
{
    /* (non-PHPdoc)
     * @see \Evence\Bundle\GridBundle\Grid\Type\AbstractType::renderType()
     */
    public function renderType($value, $source, $options  ){

        if(!$value) return $this->getOption('empty_value');


        $properyAccessor = PropertyAccess::createPropertyAccessor();

        $values = [];

        if($value instanceof \Traversable){
            foreach($value as $val)
                $values[] = $val;
        }
        elseif(is_object($value)){
            $values[] = $value;
        }
        else
            throw new \Exception('Field is not an entity expected an object.');

        $return =[];

        foreach ($values as $val){
            if(($propertyPath = $this->getOption('property')) !== false)
                $return[] = $properyAccessor->getValue($val, $propertyPath);
            else
                $return[] = (string) $val;
        }

        return implode(", ", $return);



        /*
        if (is_object($value) && ! $value instanceof  PersistentCollection){

            ;

            if(($property = $this->getOption('property')) !== false){
                $getter = 'get'. ucfirst($property);
                if(!method_exists($value, $getter)){
                    throw new \Exception('Non-existing method '. $getter . ' in ' . get_class($value));
                }
                return $value->$getter();
            }
            else {
                return $value;
            }
        }
        elseif (is_object($value)){
            
            $multiple = array();
            
            foreach($value as $val)
            {
                if(($property = $this->getOption('property')) !== false){
                    $getter = 'get'. ucfirst($property);
                    if(!method_exists($value, $getter)){
                        throw new \Exception('Non-existing method '. $getter . ' in ' . get_class($value));
                    }
                    $multiple[] = $val->$getter();
                }
                else {
                    $multiple[] = $val;
                }
            }
            
            return implode(", ", $multiple);
        }
        elseif($value !== null) {
            throw new \Exception('Field is not an entity expected an object.');
        }
        return $value;*/
    }
    
    /* (non-PHPdoc)
     * @see \Evence\Bundle\GridBundle\Grid\Type\AbstractType::getName()
     */
    public function getName(){
        return 'text';
    }   


    /* (non-PHPdoc)
     * @see \Evence\Bundle\GridBundle\Grid\Type\AbstractType::configureOptions()
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'property' => false,
            'empty_value' => ''
        ));
    }
}

