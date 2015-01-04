<?php
namespace Evence\Bundle\GridBundle\Grid\GridBuilder;

use Evence\Bundle\GridBundle\Grid\Grid;
use Evence\Bundle\GridBundle\Grid\GridFieldConfigurator;
use Evence\Bundle\CoreBundle\Entity\AdminUser;
use Evence\Bundle\GridBundle\Grid\GridActionConfigurator;


class GridBuilder extends Grid {

    public function getEntityName() {     
        return 'EvenceSubscriberBundle:Subscriber';
    }
    
    public function configureFields(GridFieldConfigurator $FieldConfigurator){
    
    }
    
    public function getDataSourceType(){
        return parent::DATA_SOURCE_ENTITY;
    }
    
    public function configureActions(GridActionConfigurator $actionConfigurator){
  
    }
}
