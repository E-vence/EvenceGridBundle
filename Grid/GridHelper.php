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

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Grid helper: Helps creating a Grid
 *
 * @author Ruben Harms <info@rubenharms.nl>
 * @link http://www.rubenharms.nl
 * @link https://www.github.com/RubenHarms
 * @package evence/grid-bundle
 * @subpackage Grid
 */
class GridHelper
{

    use ContainerAwareTrait;
    
    
    /**
     * Array of grids
     * 
     * @var multitype:Grid
     */
    private $grids = null;

    /**
     * Helper to create a grid
     *
     * @param Grid $grid
     *            Grid object
     * @return Grid With services populated Grid object
     */
    public function createGrid(Grid $grid)
    {
        $prefix = 'g' . count($this->grids);
        
        $grid->setTemplating($this->container->get('templating'))
            ->setDoctrine($this->container->get('doctrine'))
            ->setRequest($this->container->get('request_stack')->getMasterRequest())
            ->setRouter($this->container->get('router'))
            ->setSession($this->container->get('session'))
            ->setTokenStorage($this->container->get('security.token_storage'))
            ->setAuthorizationChecker($this->container->get('security.authorization_checker'))
          
            ->setFormFactory($this->container->get('form.factory'))
            ->setEventDispatcher($this->container->get('event_dispatcher'));
        
        if ($this->container->has('doctrine_mongodb')) {
            $grid->setDoctrineMongoDb($this->container->get('doctrine_mongodb'));
        }
        
        $grid->setPrefix($prefix);
        
        $this->grids[$prefix] = $grid;
        
        return $grid;
    }

    /**
     * Creates gridBuilder
     *
     * @return GridBuilder With services populated Grid object
     */
    public function createGridBuilder($source, $dataSourceType = Grid::DATA_SOURCE_ENTITY, $options = array())
    {
        $grid = new GridBuilder($source, $dataSourceType, $options);
        
        $prefix = 'g' . count($this->grids);
        
        $grid->setTemplating($this->container->get('templating'))
            ->setDoctrine($this->container->get('doctrine'))
            ->setRequest($this->container->get('request_stack')->getMasterRequest())
            ->setRouter($this->container->get('router'))
            ->setSession($this->container->get('session'))
       
            ->setTokenStorage($this->container->get('security.token_storage'))
            ->setAuthorizationChecker($this->container->get('security.authorization_checker'))
            ->setFormFactory($this->container->get('form.factory'))
            ->setEventDispatcher($this->container->get('event_dispatcher'));
        
        if ($this->container->has('doctrine_mongodb')) {
            $grid->setDoctrineMongoDb($this->container->get('doctrine_mongodb'));
        }
        
        $grid->setPrefix($prefix);
        $this->grids[$prefix] = $grid;
        
        return $grid;
    }

    public function hasGrid($gridId)
    {
        if (! empty($this->grids[$gridId])) {
            return true;
        }
    }

    /**
     * Renders a view.
     *
     * @param string $view
     *            The view name
     * @param array $parameters
     *            An array of parameters to pass to the view
     * @param Response $response
     *            A response instance
     *            
     * @return Response A Response instance
     */
    public function gridResponse($view, array $parameters = array(), Response $response = null)
    {
        $request = $this->container->get('request_stack')->getMasterRequest();
        
        if ($gmode = $request->get('grid_mode')) {
            if ($gid = $request->get('grid_id')) {
                if ($this->hasGrid($gid)) {
                    $grid = $this->grids[$gid];
                    $options = $request->get('grid_options', array());
                    $options = array_merge($options, array(
                        'mode' => $gmode
                    ));

                    $res = $grid->renderView($options);
                    if($res instanceof Response) return $res;
                    else   return new Response($res);
                }
            }
        }
        
        return $this->container->get('templating')->renderResponse($view, $parameters, $response);
    }
}
