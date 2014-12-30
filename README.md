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
    
    public function configureFields(GridFieldConfigurator $FieldConfigurator){
        $FieldConfigurator  ->addDataField('firstname', 'Firstname')
                            ->addDataField('lastname', 'Lastname')
                            ->addDataField('username', 'Username');        
    }
    
    public function getDataSourceType(){
        return parent::DATA_SOURCE_ENTITY;
    }
    
}

``` 

### Usage

Add the following code to your controller action:

``` php
  $gridHelper =  $this->get('evence.grid');        
  $grid = $gridHelper->createGrid(new UserGrid());       
  return  $gridHelper->gridRespose('EvenceCoreBundle:Admin:user_read.html.twig', array('grid' =>        $grid->renderView()));
    
```

Add the following code to your twig file:

``` twig
   {{ grid|raw }}
```


