<?php

namespace Evence\Bundle\CoreBundle\Grid;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Route;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class GridHelper {
    
    /**
     *
     * @var TwigEngine
     */
    private $templating = null;
    
    /**
     *
     * @var Request
     */
    private $request = null;
    
    /**
     *
     * @var Router
     */
    private $router = null;
    
    /**
     *
     * @var Session
     */
    private $session = null;
    
    /**
     * @var Registry
     */
    private $doctrine = null;
    
    
    public function createGrid(Grid $grid) {        
        $grid->setTemplating($this->templating)->setDoctrine($this->doctrine)->setRequest($this->request)->setRouter($this->router)->setSession($this->session);
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
    public function __construct(Registry $doctrine, TwigEngine $templating, RequestStack $request, Router $router, Session $session){
        $this->doctrine = $doctrine;
        $this->templating = $templating;
        $this->request = $request->getCurrentRequest();
        $this->router = $router;
        $this->session = $session;
    }

    public function setTemplating(TwigEngine $templating)
    {
        $this->templating = $templating;
        return $this;
    }
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    

    public function setRouter(Router $router)
    {
        $this->router = $router;
        return $this;
    }

 

    public function setSession(Session $session)
    {
        $this->session = $session;
        return $this;
    }

 
    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        return $this;
    }
 
    

}
