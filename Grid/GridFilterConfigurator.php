<?php
/*
 * Copyright (c) 2015 - Ruben Harms <info@rubenharms.nl>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */
namespace Evence\Bundle\GridBundle\Grid;

use Evence\Bundle\GridBundle\Grid\Fields\DataField;
use Evence\Bundle\GridBundle\Grid\Fields\Field;
use Evence\Bundle\GridBundle\Grid\Type\AbstractType;
use Evence\Bundle\GridBundle\Grid\Type\BooleanType;
use Evence\Bundle\GridBundle\Grid\Type\TextType;
use Evence\Bundle\GridBundle\Grid\Type\ChoiceType;
use Evence\Bundle\GridBundle\Grid\Fields\CustomField;
use Evence\Bundle\GridBundle\Grid\Type\DateType;
use Evence\Bundle\GridBundle\Grid\Type\DateTimeType;
use Evence\Bundle\GridBundle\Grid\Type\TimeType;
use Evence\Bundle\GridBundle\Grid\Type\EntityType;
use Evence\Bundle\GridBundle\Grid\Type\MoneyType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Evence\Bundle\GridBundle\Grid\Filter\FilterMapperCollection;
use Evence\Bundle\GridBundle\Grid\Filter\FilterObject;
use Symfony\Component\Form\Extension\Core\Type\FormType;

/**
 * Grid filter configurator
 *
 * @author Ruben Harms <info@rubenharms.nl>
 * @link http://www.rubenharms.nl
 * @link https://www.github.com/RubenHarms
 * @package Cursuswebsitesbouwen.nl
 * @subpackage EvenceCoreBundle
 */
class GridFilterConfigurator
{

    /**
     *
     * @var Grid
     */
    private $grid = null;

    /**
     * Array of the configured fields
     * 
     * @var FormBuilderInterface
     */
    private $formBuilder = null;

    
    /**
     * Symfony's FormFactoryInterface
     *
     * @var FormFactoryInterface
     */
    private $formFactory;
    

    /**
     * Array of filter mappers
     *
     * @var FilterMapperCollection
     */
    private $filterMapper = null;
    
    
    

    /**
     * Symfony's FormFactoryInterface
     *
     * @var FormFactoryInterface
     */
    private $formData = array();
    
    
    /**
     * Class constructor: Inject services
     *
     * @param Grid $grid            
     */
    public function __construct(Grid $grid, FormFactoryInterface $formFactory, $options = array())
    {
        $this->grid = $grid;
        $this->formFactory = $formFactory;
        
        if($grid->getDataSourceType() == Grid::DATA_SOURCE_ENTITY || $grid->getDataSourceType() == Grid::DATA_SOURCE_DOCUMENT ){
            
            //$this->formData = new $entityInfo->name();
            $this->formData = new FilterObject();
        }
        
        if($grid->getDataSourceType() == Grid::DATA_SOURCE_ENTITY)
            $entityInfo =  $grid->getEntityClassMeta();
        
    
        
        $options = array_merge(array('mapped' => true, 'csrf_protection' => false), $options);
        
        $this->formBuilder = $formFactory->createNamedBuilder($this->grid->getPrefix(),FormType::class, $this->formData, $options)->setMethod('GET');        
        $this->filterMapper = new FilterMapperCollection();
    } 
    
    
    /**
     * @return boolean
     */
    public function hasFields(){
        return (count($this->formBuilder) ? true : false);
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
     * @return EditFormBuilder The builder object.
     */
    public function add($child, $type = null, array $options = array()){
        
        $options = array_merge($options, array('required' => false));
        
        $this->getFormBuilder()->add($child, $type, $options );
        return $this;
    }
    
    /**
     * Creates a form builder.
     *
     * @param string                   $name    The name of the form or the name of the property
     * @param string|FormTypeInterface $type    The type of the form or null if name is a property
     * @param array                    $options The options
     *
     * @return FormBuilderInterface The created builder.
     */
    public function create($name, $type = null, array $options = array()){
        return $this->getFormBuilder()->create($name,$type, $options);
    }
    
    /**
     * Returns a child by name.
     *
     * @param string $name The name of the child
     *
     * @return FormBuilderInterface The builder for the child
     *
     * @throws Exception\InvalidArgumentException if the given child does not exist
     */
    public function get($name){
        return $this->getFormBuilder()->get($name);
    }
    
    /**
     * Removes the field with the given name.
     *
     * @param string $name
     *
     * @return EditFormBuilder The builder object.
     */
    public function remove($name){
        $this->getFormBuilder()->remove($name);
        return $this;
    }
    
    /**
     * Returns whether a field with the given name exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name){
        return $this->getFormBuilder()->has($name);
    }
    
    /**
     * Returns the children.
     *
     * @return array
     */
    public function all(){
        return $this->getFormBuilder()->all();
    }
    
    /**
     * Creates the form.
     *
     * @return Form The form
     */
    public function getForm(){
        return $this->getFormBuilder()->getForm();
    }
    
    
    
    /**
     * Adds an event listener to an event on this form.
     *
     * @param string   $eventName The name of the event to listen to.
     * @param callable $listener  The listener to execute.
     * @param int      $priority  The priority of the listener. Listeners
     *                            with a higher priority are called before
     *                            listeners with a lower priority.
     *
     * @return self The configuration object.
     */
    public function addEventListener($eventName, $listener, $priority = 0){
        return $this->getFormBuilder()->addEventListener($eventName, $listener, $priority);
    }
    
    /**
     * Adds an event subscriber for events on this form.
     *
     * @param EventSubscriberInterface $subscriber The subscriber to attach.
     *
     * @return self The configuration object.
     */
    public function addEventSubscriber(EventSubscriberInterface $subscriber){
        return $this->getFormBuilder()->addEventSubscriber($subscriber);
    }
    
    /**
     * Appends / prepends a transformer to the view transformer chain.
     *
     * The transform method of the transformer is used to convert data from the
     * normalized to the view format.
     * The reverseTransform method of the transformer is used to convert from the
     * view to the normalized format.
     *
     * @param DataTransformerInterface $viewTransformer
     * @param bool                     $forcePrepend    if set to true, prepend instead of appending
     *
     * @return self The configuration object.
     */
    public function addViewTransformer(DataTransformerInterface $viewTransformer, $forcePrepend = false){
        return $this->getFormBuilder()->addViewTransformer($viewTransformer, $forcePrepend);
    }
    
    /**
     * Clears the view transformers.
     *
     * @return self The configuration object.
     */
    public function resetViewTransformers(){
        return $this->getFormBuilder()->resetViewTransformers();
    }
    
    /**
     * Prepends / appends a transformer to the normalization transformer chain.
     *
     * The transform method of the transformer is used to convert data from the
     * model to the normalized format.
     * The reverseTransform method of the transformer is used to convert from the
     * normalized to the model format.
     *
     * @param DataTransformerInterface $modelTransformer
     * @param bool                     $forceAppend      if set to true, append instead of prepending
     *
     * @return self The configuration object.
     */
    public function addModelTransformer(DataTransformerInterface $modelTransformer, $forceAppend = false){
        return $this->getFormBuilder()->addModelTransformer($modelTransformer,$forceAppend);
    }
    
    /**
     * Clears the normalization transformers.
     *
     * @return self The configuration object.
     */
    public function resetModelTransformers(){
        return $this->getFormBuilder()->resetModelTransformers();
    }
    
    /**
     * Sets the value for an attribute.
     *
     * @param string $name  The name of the attribute
     * @param mixed  $value The value of the attribute
     *
     * @return self The configuration object.
     */
    public function setAttribute($name, $value){
        return $this->getFormBuilder()->setAttribute($name, $value);
    }
    
    /**
     * Sets the attributes.
     *
     * @param array $attributes The attributes.
     *
     * @return self The configuration object.
     */
    public function setAttributes(array $attributes){
        return $this->getFormBuilder()->setAttributes($attributes);
    }
    
    /**
     * Sets the data mapper used by the form.
     *
     * @param DataMapperInterface $dataMapper
     *
     * @return self The configuration object.
     */
    public function setDataMapper(DataMapperInterface $dataMapper = null){
        return $this->getFormBuilder()->setDataMapper($dataMapper);
    }
    
    /**
     * Set whether the form is disabled.
     *
     * @param bool $disabled Whether the form is disabled
     *
     * @return self The configuration object.
     */
    public function setDisabled($disabled){
        return $this->getFormBuilder()->setDisabled($disabled);
    }
    
    /**
     * Sets the data used for the client data when no value is submitted.
     *
     * @param mixed $emptyData The empty data.
     *
     * @return self The configuration object.
     */
    public function setEmptyData($emptyData){
        return $this->getFormBuilder()->setEmptyData($emptyData);
    }
    
    /**
     * Sets whether errors bubble up to the parent.
     *
     * @param bool $errorBubbling
     *
     * @return self The configuration object.
     */
    public function setErrorBubbling($errorBubbling){
        return $this->getFormBuilder()->setErrorBubbling($errorBubbling);
    }
    
    /**
     * Sets whether this field is required to be filled out when submitted.
     *
     * @param bool $required
     *
     * @return self The configuration object.
     */
    public function setRequired($required){
        return $this->getFormBuilder()->setRequired($required);
    }
    
    /**
     * Sets the property path that the form should be mapped to.
     *
     * @param null|string|PropertyPathInterface $propertyPath
     *                                                        The property path or null if the path should be set
     *                                                        automatically based on the form's name.
     *
     * @return self The configuration object.
     */
    public function setPropertyPath($propertyPath){
        return $this->getFormBuilder()->setPropertyPath($propertyPath);
    }
    
    /**
     * Sets whether the form should be mapped to an element of its
     * parent's data.
     *
     * @param bool $mapped Whether the form should be mapped.
     *
     * @return self The configuration object.
     */
    public function setMapped($mapped){
        return $this->getFormBuilder()->setMapped($mapped);
    }
    
    /**
     * Sets whether the form's data should be modified by reference.
     *
     * @param bool $byReference Whether the data should be
     *                          modified by reference.
     *
     * @return self The configuration object.
     */
    public function setByReference($byReference){
        return $this->getFormBuilder()->setByReference($byReference);
    }
    
    /**
     * Sets whether the form should read and write the data of its parent.
     *
     * @param bool $inheritData Whether the form should inherit its parent's data.
     *
     * @return self The configuration object.
     */
    public function setInheritData($inheritData){
        return $this->getFormBuilder()->setInheritData($inheritData);
    }
    
    /**
     * Sets whether the form should be compound.
     *
     * @param bool $compound Whether the form should be compound.
     *
     * @return self The configuration object.
     *
     * @see FormConfigInterface::getCompound()
     */
    public function setCompound($compound){
        return $this->getFormBuilder()->setCompound($compound);
    }
    
    /**
     * Set the types.
     *
     * @param ResolvedFormTypeInterface $type The type of the form.
     *
     * @return self The configuration object.
     */
    public function setType(ResolvedFormTypeInterface $type){
        return $this->getFormBuilder()->setType($type);
    }
    
    /**
     * Sets the initial data of the form.
     *
     * @param mixed $data The data of the form in application format.
     *
     * @return self The configuration object.
     */
    public function setData($data){
        return $this->getFormBuilder()->setData($data);
    }
    
    /**
     * Locks the form's data to the data passed in the configuration.
     *
     * A form with locked data is restricted to the data passed in
     * this configuration. The data can only be modified then by
     * submitting the form.
     *
     * @param bool $locked Whether to lock the default data.
     *
     * @return self The configuration object.
     */
    public function setDataLocked($locked){
        return $this->getFormBuilder()->setDataLocked($locked);
    }
    
    /**
     * Sets the form factory used for creating new forms.
     *
     * @param FormFactoryInterface $formFactory The form factory.
     */
    public function setFormFactory(FormFactoryInterface $formFactory){
        return $this->getFormBuilder()->setFormFactory($formFactory);
    }
    
    /**
     * Sets the target URL of the form.
     *
     * @param string $action The target URL of the form.
     *
     * @return self The configuration object.
     */
    public function setAction($action){
        return $this->getFormBuilder()->setAction($action);
    }
    
    /**
     * Sets the HTTP method used by the form.
     *
     * @param string $method The HTTP method of the form.
     *
     * @return self The configuration object.
     */
    public function setMethod($method){
        return $this->getFormBuilder()->setMethod($method);
    }
    
    /**
     * Sets the request handler used by the form.
     *
     * @param RequestHandlerInterface $requestHandler
     *
     * @return self The configuration object.
     */
    public function setRequestHandler(RequestHandlerInterface $requestHandler){
        return $this->getFormBuilder()->setRequestHandler($requestHandler);
    }
    
    /**
     * Sets whether the form should be initialized automatically.
     *
     * Should be set to true only for root forms.
     *
     * @param bool $initialize True to initialize the form automatically,
     *                         false to suppress automatic initialization.
     *                         In the second case, you need to call
     *                         {@link FormInterface::initialize()} manually.
     *
     * @return self The configuration object.
     */
    public function setAutoInitialize($initialize){
        return $this->getFormBuilder()->setAutoInitialize($initialize);
    }
    
    /**
     * Builds and returns the form configuration.
     *
     * @return FormConfigInterface
     */
    public function getFormConfig(){
        return $this->getFormBuilder()->getFormConfig();
    }
    
    public function getFormBuilder(){
        return $this->formBuilder;
    }

    public function getFilterMapper()
    {
        return $this->filterMapper;
    }
 
}
 