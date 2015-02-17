<?php

namespace Evence\Bundle\GridBundle\Grid;

class GridBuilder extends Grid {

    private $fieldConfigurator = null;
    private $actionConfigurator = null;
    private $filterConfigurator = null;
    private $entityName = null;
    private $dataSourceType = null;
    private $options = array();
   
      
    public function getEntityName() {     
        return $this->entityName;
    }
    public function setEntityName() {
        return $this->entityName;
    }
   
    
    public function __construct($source, $dataSourceType = Grid::DATA_SOURCE_ENTITY, $options = array()){
        
        if($dataSourceType ==  Grid::DATA_SOURCE_ENTITY){
            $this->entityName = $source;
        } elseif($dataSourceType ==  Grid::DATA_SOURCE_ARRAY){
            $this->setDataSource($source);  
        }
        $this->options = $options;
        $this->dataSourceType = $dataSourceType;
        $this->fieldConfigurator = $this->createFieldConfigurator();
        $this->actionConfigurator = $this->createActionConfigurator();
        $this->filterConfigurator = $this->createFilterConfigurator();
    }   
    

    /**
     * Add datafield to the grid
     *
     * @param string $alias
     *            Alias or dataname of the datasource
     * @param string $label
     *            Label of the field (for heading in the grid)
     * @param AbstractType|string $type
     *            Desired data type
     * @param array $options
     * @return \Evence\Bundle\GridBundle\Grid\GridFieldConfigurator
     */
    
    public function addDataField($alias, $label, $type = null, $options = array()){
        $this->fieldConfigurator->addDataField($alias, $label, $type, $options);
        return $this;
    }
    
    
    /**
     * Add a custom field to the grid
     *
     * @param string $alias
     *            Alias for the custom fieldname
     * @param string $label
     *            Label of the field (for heading in the grid)
     * @param AbstractType|string $type
     *            Desired data type
     * @param callable $callable
     *            Callback to render your custom field
     * @param array $options
     *            Array of options
     * @return GridBuilder
     */
    public function  addCustomField($alias, $label, $type, $callable, $options = array()){
        $this->fieldConfigurator->addCustomField($alias, $label, $type, $callable, $options);
        
        return $this;
    }
    
    /**
     * Add an action to the grid
     *
     * @param string $identifier
     *            Unique action name
     * @param string $label
     *            Label name to display in the grid
     * @param string $routeName
     *            Symfony route name
     * @param array $routeParameters
     *            (optional) Route parameters for the specified router
     * @param string $roles
     *            (optional) Required roles to do this action (symfony's securityContext)
     * @param array $options
     *            Options for the action
     * @return GridBuilder
     */
    public function addAction($identifier, $label, $routeName, $routeParameters = array(), $roles = null, $options = array())
    {
        $this->actionConfigurator->addAction($identifier, $label, $routeName, $routeParameters, $roles, $options);
        return $this;
    }
    
    /**
     * Set mapped parameters
     *
     * @param array $parameters
     *            Could be an assocative array or non assocative array: array('paramname1' => 'fieldname1', 'paramname2' => 'fieldname2' )
     */
    public function setMappedParameters($parameters)
    {
        $this->actionConfigurator->setMappedParameters($parameters);
        return $this;
    }
    
    

        
    /**
     * Adds a new field to this group. A field must have a unique name within
     * the group. Otherwise the existing field is overwritten.
     *
     * If you add a nested group, this group should also be represented in the
     * object hierarchy.
     *
     * @param string|int|FormBuilderInterface $child
     * @param string|FormTypeInterface        $type
     * @param array                           $options
     *
     * @return GridFilterConfigurator The builder object.
     */
    public function addFilterField($child, $type = null, array $options = array()){
    {
        $this->filterConfigurator->add($child, $type, $options);
        return $this;
    }
    
    
    public function getDataSourceType(){
        return $this->dataSourceType;
    }
    
    public function configureActions(GridActionConfigurator $actionConfigurator){
        //Do nothing
    }
    

    public function configureFields(GridFieldConfigurator $FieldConfigurator){
        //Do nothing
    }
    
    
    public function configureFilter(GridFilterConfigurator $filterConfigurator){
        //Do nothing    
    }
    
    public function getOptions(){
        return $this->options;
    }
    

    public function setOptions($options){
        $this->options = $options;
    }
}
