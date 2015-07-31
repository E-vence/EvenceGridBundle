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

### Grid withoud class

Add the following code to your controller:

``` php
    
    $gridHelper = $this->get('evence.grid');
    $grid = $gridHelper->createGridBuilder('EvenceOptinBundle:Supplier')
            ->addDataField('name', 'Name')
            -> addAction('edit', 'Edit supplier', 'evence_optin_supplier_edit',  array(), array('ROLE_ADMIN'), array('icon' => 'pencil'))
            ->setMappedParameters(array('id'));
        
  return $gridHelper->gridResponse('EvenceCoreBundle:Admin:simple_grid.html.twig', array(
      'grid' => $grid->createView()));
``` 

   
### Grid within a class

``` php

namespace Acme\Bundle\DemoBundle\Grid;
use Evence\Bundle\GridBundle\Grid\Grid;
use Evence\Bundle\GridBundle\Grid\GridFieldConfigurator;
use Evence\Bundle\GridBundle\Grid\GridActionConfigurator;
use Evence\Bundle\GridBundle\Grid\GridFilterConfigurator;
use Evence\Bundle\GridBundle\Grid\Filter\FilterMapper;

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
    
    
    /*
     * (non-PHPdoc)
     * @see \Evence\Bundle\GridBundle\Grid\Grid::configureFilter()
     */
    public function configureFilter(GridFilterConfigurator $filterConfigurator)
    {
        $filterConfigurator->add('status', 'choice', [
            'choices' => Transaction::getStatusses()
        ])
            ->add('dateFrom', 'datetime', [])

        
            ->add('dateTill', 'datetime', []);
        
        $fm = $filterConfigurator->getFilterMapper();
        
        $fm->add(FilterMapper::gt('transactionAt', 'dateFrom'))->add(FilterMapper::lt('transactionAt', 'dateTill'));
    }
    
}
``` 


Add the following code to your controller action:

``` php
     $gridHelper =  $this->get('evence.grid');        
        $grid = $gridHelper->createGrid(new UserGrid());       
        
        return  $gridHelper->gridResponse('EvenceCoreBundle:Admin:user_read.html.twig', array('grid' => $grid->createView()));
    
```

### Show grid in twig


Add the following code to your twig file:

``` twig
   {{ evenceGrid(grid, {'formAttributes': {'class': 'form'}}) }}
```


