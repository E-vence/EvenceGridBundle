<?php
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Parameter;

$container->setDefinition('evence.grid', new Definition('Evence\Bundle\CoreBundle\Grid\GridHelper', array(
    new Reference('doctrine'),
    new Reference('templating'),
    new Reference('request_stack'),
    new Reference('router'),
    new Reference('session'),
)));

