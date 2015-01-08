<?php
namespace Evence\Bundle\GridBundle\Tests;

use Evence\Bundle\GridBundle\Grid\Misc\Action;
use Evence\Bundle\GridBundle\Grid\Grid;

class ActionTest extends \PHPUnit_Framework_TestCase {
    
    private $action = null;    
    private $grid = null;
    
    public function getGrid(){
        
        if(!$this->grid){
            $this->grid = $this->getMockBuilder('Evence\Bundle\GridBundle\Grid\GridBuilder')  
            ->setConstructorArgs(array(array(), Grid::DATA_SOURCE_ARRAY, array()))         
            ->getMock();
        }
        
        return $this->grid;
    }
    
    public function getSecurityContext($granted = true){
        $stub = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')->disableOriginalConstructor()->getMock();
        //$obj = $stub->method('isGranted')->will($this->returnValue(false));
     
        
        return $stub;
    }
    
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
    
    public function testIsVisible(){
        $action = $this->getAction();        

        
        
        $this->assertEquals(true, $action->isVisible());
    }
    
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
    
   
}
