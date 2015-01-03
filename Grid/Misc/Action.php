<?php

namespace Evence\Bundle\GridBundle\Grid\Misc;

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

use Evence\Bundle\GridBundle\Grid\GridActionConfigurator;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
     * @var multitype
     */
    protected $roles = array();
    
    
    
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
    
    protected $uri = '';
    protected $route = null; 
    protected $routeParameters = array();
    
    
    protected $options;
    
    
    
    public function __construct(GridActionConfigurator $configurator,$identifier, $label, $options)
    {
        $this->configurator = $configurator;
        $this->identifier = $identifier;
        $this->label = $label;
        
        $resolver = new OptionsResolver();
        $resolver->setDefaults(array('target' => '_self', 'icon' => false, 'class' => '','iconType' => 'glyphicons'));
        
        $this->options = $resolver->resolve($options);        
    }
    
    
    public function isVisible($source){        
        foreach($this->getRoles() as $row){
            if($this->configurator->getGrid()->getSecurityContext()->isGranted($row) === false)
            return false;
        }
        
        /**
         * @todo Check conditions
         */
        
      
        return true;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel(string $label)
    {
        $this->label = $label;
        return $this;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles($roles)
    {
        if(!is_array($roles)) $roles = array($roles);
        $this->roles = $roles;
        return $this;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }

    public function getRouteParameters()
    {
        return $this->routeParameters;
    }

    public function setRouteParameters($routeParameters)
    {
        $this->routeParameters = $routeParameters;
        return $this;
    }
    
    public function generateUrl($source){
        $router = $this->configurator->getGrid()->getRouter();
        if($this->getUri()) return $this->getUri();
         
        $parameters = array_merge($this->getRouteParameters(),$this->configurator->getParametersBySource($source));
        
       
        
        return $router->generate($this->getRoute(), $parameters );
    }

    public function getOptions()
    {
        return $this->options;
    }
 
 
    
}