<?php
use Evence\Bundle\GridBundle\Grid\GridActionConfigurator;
use Symfony\Component\OptionsResolver\OptionsResolver;
/**
 * Copyright Ruben Harms 2015
 *
 * Do not use, modify, sell and/or duplicate this script
 * without any permissions!
 *
 * This software is written and recorded by Ruben Harms!
 * Ruben Harms took all the necessary actions, juridical and
 * (hidden) technical, to protect her script against any use
 * without permission, any modify and against any unauthorized duplicate.
 *
 * Copied versions shall be recognized and compared with the recorded version.
 * The owner of this softare will take all legal steps against every kind of malpractice!
 */

class Action {
    
    /**
     * Grid Action configurator
     *
     * @var GridActionConfigurator
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
    protected $roles = '';
    
    
    
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
     * Current value of the class
     *
     * @var
     */
    protected $value = '';
    
    protected $uri = '#';
    protected $route = null; 
    protected $routeParameters = array();
    
    
    
    public function __construct(GridActionConfigurator $configurator,$identifier, $label, $options)
    {
        $this->configurator = $configurator;
        $this->identifier = $identifier;
        $this->label = $label;
        
        $resolver = new OptionsResolver();
        //$resolver->setDefault(alue)
        
        $this->options = $resolver->resolve();        
    }
    
    
    public function isVisible(){
        if($this->configurator->getGrid()){
            
        }
    }
    
}