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
namespace Evence\Bundle\GridBundle\Grid\Misc;

use Evence\Bundle\GridBundle\Grid\GridActionConfigurator;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Action
{

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
     *
     */
    protected $value = '';

    protected $uri = '';

    protected $route = null;

    protected $routeParameters = array();

    protected $options;

    public function __construct(GridActionConfigurator $configurator, $identifier, $options = array())
    {
        $this->configurator = $configurator;
        $this->identifier = $identifier;
      
        
        $resolver = new OptionsResolver();
        
        $resolver->setDefaults(array(
            'attr' => array(),
            'multiple' => false,
            'target' => '_self',
            'icon' => false,
            'class' => '',
            'iconType' => 'fontawesome',
            'iconLabel' => false,
            'confirm' => null,
            'isVisible' => function (Action $action, $source) {
                return true;
            },
            'isGranted' => function ($action, $source) {
                return true;
            },
            'jsCallback' => null,
            'mappedParameters' => [],
            'uri' =>  null,
            'label' => $this->humanize($identifier)
        ));
        
        $this->options = $resolver->resolve($options);        
        $this->label = $this->options ['label'];
        if(!empty($options['uri']))
        $this->uri = $options['uri'];
    }
    
    public function humanize($input){
        $input = preg_replace("/([a-z]{1})([A-Z_-]{1})/", "\\1 \\2", $input);
        return ucfirst(strtolower($input));
    }
    
    

    public function isVisible($source = '')
    {
        if ($this->getRoles() != null && count($this->getRoles()) > 0) {
            
            $denied = true;
            
            foreach ($this->getRoles() as $row) {
                if ($this->configurator->getGrid()
                    ->getAuthorizationChecker()
                    ->isGranted($row) !== false)
                    $denied = false;
            }



            if ($denied)
                return false;
        }

        if($source){
            if(!$this->configurator->getGrid()->isGranted($this, $source))
                return false;


            if(!call_user_func_array($this->options['isGranted'], array(

                $this,
                $source
            )))
                return false;
        }

        /**
         *
         * @todo Check conditions
         */
        
        return call_user_func_array($this->options['isVisible'], array(
            $this,
            $source
        ));
    }

    public function getLabel()
    {
        return ($this->options['label'] ?: $this->label);
    }

    public function setLabel($label)
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
        if ($roles == null)
            return;
        if (! is_array($roles))
            $roles = array(
                $roles
            );
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

    public function generateUrl($source = null)
    {
        $router = $this->configurator->getGrid()->getRouter();
        if ($this->getUri())
            return (is_callable($this->getUri()) ? call_user_func_array($this->getUri(), [$source, $this->options['mappedParameters']]) : $this->getUri());
        
        if ($source)
            $parameters = array_merge($this->configurator->getParametersBySource($source, $this->options['mappedParameters']), $this->getRouteParameters());
        else
            $parameters = $this->getRouteParameters();
        
        return $router->generate($this->getRoute(), $parameters);
    }

    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get col value by source
     *
     * @param mixed $source
     *            Source (row)
     * @param string $col
     *            Key name of the desired col
     */
    public function getColBySource($source, $col)
    {
        return $this->configurator->getColBySource($source, $col);
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function setIsVisibleCallback($cb)
    {
        $this->options['isVisible'] = $cb;
    }
}
