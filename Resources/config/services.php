<?php
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

$container->setDefinition('evence.grid', new Definition('Evence\Bundle\GridBundle\Grid\GridHelper', array(

)))->addMethodCall('setContainer', [new Reference('service_container')] );


$container->setDefinition('evence.twig.grid_extension', new Definition('Evence\Bundle\GridBundle\Twig\GridExtension'))->addTag('twig.extension');

