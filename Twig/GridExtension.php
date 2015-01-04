<?php
/*
 Copyright (c) 2015 - Ruben Harms <info@rubenharms.nl>

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

namespace Evence\Bundle\GridBundle\Twig;

use Evence\Bundle\GridBundle\Grid\Grid;


/**
 *  Twig grid extensin
 *
 * @author Ruben Harms <info@rubenharms.nl>
 * @link http://www.rubenharms.nl
 * @link https://www.github.com/RubenHarms
 * @package package_name
 * @subpackage subpackage 
 */
 
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
    
    /**
     * Get's the available Twig functions
     * 
     * @return multitype:\Twig_SimpleFunction 
     */
    public function getFunctions(){
        return array( new \Twig_SimpleFunction('evenceGrid', array($this, 'evenceGrid'),  array('is_safe' => array('html')) ));
    }      
    
    /**
     * Renders the specified grid
     * 
     * @param Grid $grid
     * @param array $options Array of options
     * @return \Symfony\Component\HttpFoundation\Response
     */
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