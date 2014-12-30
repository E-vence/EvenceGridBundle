<?php

namespace Evence\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('EvenceCoreBundle:Default:index.html.twig', array('name' => $name));
    }
}
