EvenceGridBundle
================

Easy method to generate a grid.

### Installation


1. Add the bundle to your composer:
   ``` bash
   $ composer require evence/grid-bundle:dev-master 
   ```

2. Add the bundle to your AppKernel.php:
   ``` php
    new Evence\Bundle\GridBundle\EvenceGridBundle()
   ```
   
   
### Sample class

``` php

namespace Acme\Bundle\DemoBundle\Grid;
use Evence\Bundle\GridBundle\Grid\Grid;

class UserGrid extends Grid {

    public function getEntityName() {     
        return 'EvenceCoreBundle:AdminUser';
    }

    public function getOptions(){
        return array('numbers' => false, 'checkbox' => false);
    }

    public function configureFields(GridFieldConfigurator $FieldConfigurator){
        $FieldConfigurator  ->addDataField('firstname', 'Firstname')
                            ->addDataField('lastname', 'Lastname')
                            ->addDataField('username', 'Username')
                            ->addCustomField('fullname', 'Volledige naam', 'text', function($source, $field){
                                return $source->getFirstname(). ' ' . $source->getLastname();    
                            }) 
                            ->addDataField('roles', 'Rollen', 'choice', array('choices' => AdminUser::getRoleTypes(), 'mapped' => false));        
    }

    public function getDataSourceType(){
        return parent::DATA_SOURCE_ENTITY;
    }
    
    public function configureActions(GridActionConfigurator $actionConfigurator){
        $actionConfigurator
                            ->addAction('edit', 'Edit', 'admin_user_edit', array(),array('ROLE_ADMIN'), array('icon' => 'pencil', 'iconType' => 'fontawesome'))
                            ->addAction('remove', 'Delete', 'admin_user_delete', array(),array('ROLE_ADMIN'), array('icon' => 'times', 'iconType' => 'fontawesome'));
        
        
        $actionConfigurator->setMappedParameters(array('id'));
        
    }
}
``` 

### Usage

Add the following code to your controller action:

``` php
     $gridHelper =  $this->get('evence.grid');        
        $grid = $gridHelper->createGrid(new UserGrid());       
        
        return  $gridHelper->gridResponse('EvenceCoreBundle:Admin:user_read.html.twig', array('grid' => $grid->createView()));
    
```

Add the following code to your twig file:

``` twig
   {{ evenceGrid(grid, {'formAttributes': {'class': 'form'}}) }}
```


