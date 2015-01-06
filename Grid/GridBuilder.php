<?php

namespace Evence\Bundle\GridBundle\Grid;

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
