<?php
namespace Evence\Bundle\GridBundle\Grid\Fields;

use Evence\Bundle\GridBundle\Grid\GridFieldConfigurator;

class Field
{
    protected $configurator = '';
    
    protected $identifier = '';

    protected $label = '';

    protected $callback = null;

    protected $value = '';

    public function __construct(GridFieldConfigurator $configurator,$identifier, $label, $options = array())
    {
        $this->configurator = $configurator;
        $this->identifier = $identifier;
        
        if (isset($options['identifier'])) {
            $this->identifier = $options['identifier'];
        }
        $this->label = $label;
    }

    private function getDefaultOptions()
    {
        return array(
            'sortable' => true,
            'callback' => false
        );
    }

    public function getData($source = null)
    {
        
        $value = $this->value;   
        return $value;
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

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
    
    public function getSortUrl(){
        $this->configurator->getGrid()->generateSortUrl( $this->identifier, 'ASC');
    }
}