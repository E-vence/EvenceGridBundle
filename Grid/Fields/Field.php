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

use Evence\Bundle\GridBundle\Grid\GridFieldConfigurator;
use Evence\Bundle\GridBundle\Grid\Type\AbstractType;

/**
 * Class for grid field 
 *
 * @author Ruben Harms <info@rubenharms.nl>
 * @link http://www.rubenharms.nl
 * @link https://www.github.com/RubenHarms
 * @package project_name
 * @subpackage SUBPACKAGE_NAME
 */
  
class Field
{
    /**
     * Grid field configurator
     * 
     * @var GridFieldConfigurator
     */
    protected $configurator = '';
    
    /**
     * Identifier name for the field
     * 
     * @var string
     */
    protected $identifier = '';

    /**
     * Label name of the field
     *  
     * @var string
     */
    protected $label = '';
    
    
    /**
     * Set type
     *
     * @var string
     */
    protected $type = '';
    
    

    /**
     * Set mapped
     *
     * @var string
     */
    protected $mapped = true;
    
    

    /**
     * Callback
     * 
     * @var mixed Callback
     */
    protected $callback = null;
    

    /**
     * Callback
     *
     * @var foot Callback
     */
    protected $footCallback = null;

    /**
     * Current value of the class
     * 
     * @var 
     */
    protected $value = '';

    
    protected $currentSort = '';
    
    protected $currentSortOrder = '';
    

    public function __construct(GridFieldConfigurator $configurator,$identifier, $label)
    {
        $this->configurator = $configurator;
        $this->identifier = $identifier;     
        $this->label = $label;       
    }

    private function getDefaultOptions()
    {
        return array(
            'sortable' => true,
            'callback' => false
        );
    }
  


    /**
     * Get Data
     */
    public function getData($source = null) {       
        
        return $this->getType()->getData( $this->getCallbackValue($source), $source);        
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    public function getCallback()
    {
        return $this->callback;
    }

    public function setCallback($callback)
    {
        $this->callback = $callback;
        return $this;
    }

    public function getCallbackValue($source = null){
        
        if($this->callback)      return call_user_func_array($this->callback, array($this->getValue($source), $source, $this));
        return $this->getValue($source);
    }
    
    public function getFooterCallbackValue($rows){
    
        if($this->footCallback)      return call_user_func_array($this->footCallback, array($rows, $this));
        return;
    }
    
    public function getSortUrl(){
        return $this->configurator->getGrid()->generateSortUrl( $this->identifier, $this->getNextSortOrder());
    }
    
    public function getNextSortOrder(){
        if(!$this->getCurrentSort())
            return 'ASC';
        if($this->getCurrentSortOrder() == 'DESC') 
            return 'ASC';
                 
        return 'DESC';
    }

    public function getType()
    {
        return $this->type;
    }
    
    
    public function getValue($source = null){
        return $this->value;
    }

    public function setType(AbstractType $type)
    {
        $this->type = $type;
        return $this;
    }
    
    public function setMapped($mapped){
        $this->mapped = $mapped;
        return $this;
    }
    
    
    public function getMapped(){
        return $this->mapped;
    }
    
    public function getOption($name){
        return $this->getType()->getOption($name);
    }
    
    public function getOptions(){
        return $this->getType()->getOptions();
    }

    public function getCurrentSort()
    {
        return $this->currentSort;
    }

    public function setCurrentSort($currentSort)
    {
        $this->currentSort = $currentSort;
        return $this;
    }

    public function getCurrentSortOrder()
    {
        return $this->currentSortOrder;
    }

    public function setCurrentSortOrder($currentSortOrder)
    {
        $this->currentSortOrder = $currentSortOrder;
        return $this;
    }
 
    
    public function getDataSourceType(){
        return $this->configurator->getGrid()->getDataSourceType();
    }

    public function getFootCallback()
    {
        return $this->footCallback;
    }

    public function setFootCallback($footCallback)
    {
        $this->footCallback = $footCallback;
        return $this;
    }
 
 
    /**
     * Get Data
     */
    public function getFooterData() {
    
        return $this->getType()->getData( $this->getFooterCallbackValue($this->configurator->getGrid()->getRawData()), null);
    }
    
}