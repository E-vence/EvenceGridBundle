<?php
/*
Copyright (c) 2015

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

    /* (non-PHPdoc)
     * @see \Evence\Bundle\GridBundle\Grid\Fields\Field::getData()
     */
    public function getValue($source = null)
    {
        if ($source === null) {
            throw new \Exception('Field must have a source');
        }
        $value = $this->getDataFromSource($source);
        
        return $value;
    }

    /**
     * Get data from it source
     * 
     * @param mixed $source Entity or array
     * @throws \Exception If the property doesn't exists on the datasource.
     * @return mixed value
     */
    public function getDataFromSource($source)
    {        
        
      $id = $this->identifier;
        
      if ($this->isAssociation($id)){
         return $this->getAssociation($id, $source);
      }
      return $this->getValueFromSource($source,$id);
    }
    
    public function getAssociation($id, $source){
        $path = explode(".", $id);        
        while( count($path) > 0){
            $id = array_shift($path);
            $source = $this->getValueFromSource($source,$id);
        }
        
        return $source;
    }
    
    public function getValueFromSource($source,$id){
        $method = 'get' . ucfirst($id);
        
        
        if($this->getDataSourceType() == Grid::DATA_SOURCE_ENTITY){
            if (!method_exists($source, $method)) {
                throw new \Exception('Uknown field ' . $id . ' in datasource ' . $this->configurator->getGrid()->getEntityName());
            }
            if (!property_exists($source, $id)){
                $this->setMapped(false);
            }
            return $source->$method();
        }
        elseif($this->getDataSourceType() == Grid::DATA_SOURCE_ARRAY){
            if(!isset($source[$this->identifier]) )
                throw new \Exception('Uknown field ' . $id . ' in datasource array: ' . print_r($source,true));
        
            return $source[$id];
        }
        
    }
    
    public function isAssociation($id){
        if(stristr($id,".")){                
            return true;    
        }
        return false;
    }
}
    