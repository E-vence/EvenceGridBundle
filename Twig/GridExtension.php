<?php

namespace Evence\Bundle\GridBundle\Twig;




use Evence\Bundle\GridBundle\Grid\Grid;
class GridExtension extends \Twig_Extension
{
   
    /* (non-PHPdoc)
     * @see Twig_Extension::getFilters()
     */
    public function getFilters()
    {
        return array(
          
        );
    }
    
    public function getFunctions(){
        return array( new \Twig_SimpleFunction('evenceGrid', array($this, 'evenceGrid'),  array('is_safe' => array('html')) ));
    }      
    
    public function evenceGrid(Grid $grid, $options){
        return $grid->renderView($options);
    }

    /* (non-PHPdoc)
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName()
    {
        return 'evence_grid_extension';
    }
   
}