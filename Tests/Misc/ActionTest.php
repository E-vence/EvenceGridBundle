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

namespace Evence\Bundle\GridBundle\Tests;

use Evence\Bundle\GridBundle\Grid\Misc\Action;
use Evence\Bundle\GridBundle\Grid\Grid;

/**
 *  Test class for Action class
 *
 * @author Ruben Harms <info@rubenharms.nl>
 * @link http://www.rubenharms.nl
 * @link https://www.github.com/RubenHarms
 * @package Evence/grid-bundle
 * @subpackage Tests 
 */
class ActionTest extends \PHPUnit_Framework_TestCase {
    
    /**
     * @var Action
     */
    private $action = null;
        
    /**
     * GridBuilder
     * 
     * @var Grid
     */
    private $grid = null;
    
    /**
     * Get router mock
     * @todo fix Missing depencencies 
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getRouterMock(){
      
        return $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Routing\Router')
        ->disableOriginalConstructor()
        ->getMock();
        
    }
    
    
    /**
     *  Get mock of GridBuilder
     * 
     * @return \Evence\Bundle\GridBundle\Grid\Grid
     */
    public function getGrid(){
        
        if(!$this->grid){
            $this->grid = $this->getMockBuilder('Evence\Bundle\GridBundle\Grid\GridBuilder')  
            ->setConstructorArgs(array(array(), Grid::DATA_SOURCE_ARRAY, array()))         
            ->getMock();
            
           //$this->grid->setRouter($this->getRouterMock());
        }
        
        return $this->grid;
    }
    
    /**
     * Get mock of Symfony's Security Context 
     * 
     * @param string $granted
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getSecurityContext($granted = true){
        $stub = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')->disableOriginalConstructor()->getMock();
        //$obj = $stub->method('isGranted')->will($this->returnValue(false));
     
        
        return $stub;
    }
    
    /**
     * Creates and in initializes the Action class and it's dependencies
     * 
     * @return \Evence\Bundle\GridBundle\Grid\Misc\Action
     */
    public function getAction(){
        
        if(!$this->action){            
            
            $grid = $this->getGrid();
            $grid->setSecurityContext($this->getSecurityContext());
            
            $configurator = $this->getMockBuilder('Evence\Bundle\GridBundle\Grid\GridActionConfigurator')                  
            ->setConstructorArgs(array($grid))
            ->getMock();
                         
            $this->action = new Action($configurator, 'test', 'Label name',array());
        }          
        return $this->action;        
    }
    
    /**
     * Test for Action::isVisible
     * @return void
     */
    public function testIsVisible(){
        $action = $this->getAction();                
        $this->assertEquals(true, $action->isVisible());
    }
    
    /**
     * Test all getters and setters for the action class.
     * @return void 
     */
    public function testGetSet() {   
        
        $action = $this->getAction();
                        
        $this->assertEquals('Label name', $action->getLabel());
        
        $action->setLabel('Second label');
        $this->assertEquals('Second label', $action->getLabel());

        
        $roles = array('ROLE_ADMIN', 'ROLE_USER');
        $action->setRoles($roles);
        $this->assertEquals($roles, $action->getRoles());

        $route = 'testroute';
        $action->setRoute($route);
        $this->assertEquals($route, $action->getRoute());
        
        $params = array('foo' => '1', 'bar' => 'test');
        $action->setRouteParameters($params);
        $this->assertEquals($params, $action->getRouteParameters());
        
    }    
    
    public function generateUrlTest(){
        
    }
    
   
}
