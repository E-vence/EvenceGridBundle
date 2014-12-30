<?php
namespace Evence\Bundle\CoreBundle\Grid;

/**
 *  E-vence: Grid
 *
 * @author Ruben Harms <info@rubenharms.nl>
 * @link http://www.rubenharms.nl
 * @link https://www.github.com/RubenHarms
 * @package package_name
 * @subpackage subpackage 
 */


class UserGrid extends Grid {
    
    public function getEntityName() {     
        return 'EvenceCoreBundle:AdminUser';
    }
    
    public function configureFields(GridFieldConfigurator $FieldConfigurator){
        $FieldConfigurator  ->addDataField('firstname', 'Firstname')
                            ->addDataField('lastname', 'Lastname')
                            ->addDataField('username', 'Username');        
    }
    
    public function getDataSourceType(){
        return parent::DATA_SOURCE_ENTITY;
    }
    
}
