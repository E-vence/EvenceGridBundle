<?php
namespace Evence\Bundle\GridBundle\Tests;

use Evence\Bundle\GridBundle\Grid\Misc\Action;

class ActionTest extends \PHPUnit_Framework_TestCase {
    
    private $action = null;    
    
    public function getAction(){
        
        if(!$this->action){            
            $configurator = $this->getMockBuilder('Evence\Bundle\GridBundle\Grid\GridActionConfigurator')
            ->disableOriginalConstructor()
            ->getMock();
                         
            $this->action = new Action($configurator, 'test', 'Label name',array());
        }          
        return $this->action;        
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
