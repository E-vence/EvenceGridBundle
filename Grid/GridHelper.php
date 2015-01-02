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

namespace Evence\Bundle\GridBundle\Grid;

use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Route;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
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

    /**
     * Symfony's Twig service
     *
     * @var TwigEngine
     */
    private $templating = null;

    /**
     * Symfony's Request
     *
     * @var Request
     */
    private $request = null;

    /**
     * Symfony's Router service
     *
     * @var Router
     */
    private $router = null;

    /**
     * Symfony's Session service
     *
     * @var Session
     */
    private $session = null;

    
    /**
     * Symfony's Security Context service
     * 
     * @var securityContext
     */
    
    private $securityContext = null;
    
    /**
     * Doctrine Registry service
     *
     * @var Registry
     */
    private $doctrine = null;
    
    /**
     * Grids 
     */
    private $grids = null;

    /**
     * Helper to create a grid
     *
     * @param Grid $grid Grid object
     * @return Grid With services populated Grid object
     */
    public function createGrid(Grid $grid)
    {
        $grid->setTemplating($this->templating)
            ->setDoctrine($this->doctrine)
            ->setRequest($this->request)
            ->setRouter($this->router)
            ->setSession($this->session)
            ->setSecurityContext($this->securityContext);
        
        if(count($this->grids) > 0) $grid->setPrefix('g'.count($this->grids)); 
        $this->grids[] = $grid;
        
        
        
        return $grid;
    }

    /**
     * Inject services
     *
     * @param Registry $doctrine            
     * @param TwigEngine $templating            
     * @param Request $request            
     * @param Router $router            
     * @param Session $session            
     */
    public function __construct(Registry $doctrine, TwigEngine $templating, RequestStack $request, Router $router, Session $session, SecurityContext $securityContext)
    {
        $this->doctrine = $doctrine;
        $this->templating = $templating;
        $this->request = $request->getCurrentRequest();
        $this->router = $router;
        $this->session = $session;
        $this->securityContext = $securityContext;
    }

    /**
     * Set templating service
     *
     * @param TwigEngine $templating            
     * @return \Evence\Bundle\GridBundle\Grid\GridHelper
     */
    public function setTemplating(TwigEngine $templating)
    {
        $this->templating = $templating;
        return $this;
    }

    /**
     * Set request service
     * 
     * @param Request $request
     * @return \Evence\Bundle\GridBundle\Grid\GridHelper
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Set Router service
     * 
     * @param Router $router
     * @return \Evence\Bundle\GridBundle\Grid\GridHelper
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
        return $this;
    }

    /**
     * Set session service
     * 
     * @param Session $session
     * @return \Evence\Bundle\GridBundle\Grid\GridHelper
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
        return $this;
    }

    /**
     * Set Doctrine service
     * 
     * @param Registry $doctrine
     * @return \Evence\Bundle\GridBundle\Grid\GridHelper
     */
    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        return $this;
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
        return $this->templating->renderResponse($view, $parameters, $response);
    }
}
