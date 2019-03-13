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

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\DocumentManager;
use Evence\Bundle\GridBundle\Grid\Event\Evence\Bundle\GridBundle\Grid\Event;
use Evence\Bundle\GridBundle\Grid\Event\GridEvent;
use Evence\Bundle\GridBundle\Grid\Event\GridFilterEvent;
use Evence\Bundle\GridBundle\Grid\Exception\UnknownGridFieldException;
use Evence\Bundle\GridBundle\Grid\Filter\FilterMapper;
use Evence\Bundle\GridBundle\Grid\Misc\Action;
use Evence\Bundle\GridBundle\Pagination\Pagination;
use Exporter\Writer\CsvWriter;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\VarDumper\VarDumper;

/**
 * E-vence: Grid
 *
 * @author Ruben Harms <info@rubenharms.nl>
 * @link http://www.rubenharms.nl
 * @link https://www.github.com/RubenHarms
 * @package package_name
 * @subpackage subpackage
 */
abstract class Grid
{

    CONST QUERY_COUNT = 'count';

    CONST QUERY_SELECT = 'select';

    CONST TD_TYPE_CHECKBOX = 'checkbox';
    CONST TD_TYPE_ACTIONS = 'actions';
    CONST TD_TYPE_NUMBER = 'number';
    CONST TD_TYPE_COL = 'col';

    /**
     * Configures actions for the grid
     *
     * @param GridActionConfigurator $actionConfigurator
     */
    abstract public function configureActions(GridActionConfigurator $actionConfigurator);

    /**
     * Configures (data) fields for the grid
     *
     * @param unknown $FieldConfigurator
     * @throws UnknownGridFieldException
     */
    abstract public function configureFields(GridFieldConfigurator $FieldConfigurator);

    /**
     * Configures search filter fields
     *
     * @param GridFilterConfigurator $filterConfigurator
     * @throws Unknown GridFieldException
     */
    abstract public function configureFilter(GridFilterConfigurator $filterConfigurator);

    /**
     * Returns the name of the entity for the data source
     *
     * @return string
     */
    abstract public function getEntityName();

    /**
     * Returns the name of the document for the data source
     *
     * @return string
     */
    public function getDocumentName()
    {
        return $this->getEntityName();
    }

    /**
     * Returns the Data source type
     *
     * @return string Possible strings: 'array' or 'entity'
     */
    abstract public function getDataSourceType();

    /**
     * Data source: Array
     *
     * @var string
     */
    CONST DATA_SOURCE_ARRAY = 'array';

    /**
     * Data source: Entity
     *
     * @var string
     */
    CONST DATA_SOURCE_ENTITY = 'entity';

    /**
     * Data source: Document
     *
     * @var string
     */
    CONST DATA_SOURCE_DOCUMENT = 'document';

    /**
     * Sort order: Ascending
     *
     * @var string
     */
    CONST SORT_ORDER_ASC = 'ASC';

    /**
     * Sort order: Descending
     *
     * @var string
     */
    CONST SORT_ORDER_DESC = 'DESC';

    /**
     * Symfony's twig service
     *
     * @var TwigEngine
     */
    private $templating = null;

    /**
     * Symfony's request service
     *
     * @var Request
     */
    protected $request = null;

    /**
     * Symfony's Router service
     *
     * @var Router
     */
    private $router = null;

    /**
     * Symfony's Session service
     *
     * @var Session
     */
    private $session = null;

    /**
     * Symfony's Registry service
     *
     * @var Registry
     */
    protected $doctrine = null;

    /**
     * Symfony's MongoDb
     *
     * @var ManagerRegistry
     */
    protected $doctrineMongoDb = null;

    /**
     * Symfony's Event Dispatcher
     *
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher = null;

    /**
     * Symfony's sercurityContext service
     *
     * @var SecurityContext
     */
    private $securityContext = null;


    /**
     * Symfony's TokenStorage service
     *
     * @var TokenStorageInterface
     */
    private $tokenStorage = null;


    /**
     * Symfony's AuthorizationChecker service
     *
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker = null;


    /**
     * Default template resource to use
     *
     * @var string
     */
    private $template = 'EvenceGridBundle:Grid:grid_bootstrap.html.twig';

    /**
     * Default limit for the grid
     *
     * @var Limit
     */
    private $limit = 50;

    /**
     * Parameter prefix for grid
     *
     * @var unknown
     */
    private $prefix = 'g';

    /**
     * Datasource as array, for array grid.
     *
     * @var array
     */
    private $dataSource = array();

    /**
     * Default sort by
     *
     * @var string
     */
    public $sortBy = null;

    /**
     * Default sort order
     *
     * @var string
     */
    public $sortOrder = 'ASC';

    /**
     * Grid field configurator
     *
     * @var GridFieldConfigurator
     */
    private $fieldConfigurator = null;

    /**
     * Grid action configurator
     *
     * @var GridActionConfigurator
     */
    private $actionConfigurator = null;

    /**
     * Grid filter configurator
     *
     * @var GridFilterConfigurator
     */
    private $filterConfigurator = null;

    /**
     * Grid pagination
     *
     * @var Pagination
     */
    private $pagination = null;

    /**
     * Symfony's FormFactoryInterface
     *
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * Grid identifier
     *
     * @var string
     */
    private $identifier = null;

    /**
     * Grid multiple identifier field
     *
     * @var string
     */
    private $multipleIdentifierField = 'id';

    /**
     * Grid options
     *
     * @var array
     */
    protected $options;

    /**
     *
     * @var PropertyAccessor
     */
    private $accessor = null;

    /**
     *
     * @var MetaFields
     */
    private $metaFields = [];

    /**
     * @var array
     */

    private $rawData = [];



    /**
     *
     * @var array
     */
    public $simpleSearchFields = [];

    /**
     * Set symfony's templating service
     *
     * @param EngineInterface $templating
     * @return \Evence\Bundle\GridBundle\Grid\Grid
     */
    public function setTemplating(EngineInterface $templating)
    {
        $this->templating = $templating;
        return $this;
    }

    /**
     * Set symfony's Request service
     *
     * @param Request $request
     * @return \Evence\Bundle\GridBundle\Grid\Grid
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Set symfony's router service
     *
     * @param Router $router
     * @return \Evence\Bundle\GridBundle\Grid\Grid
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
        return $this;
    }

    /**
     * Set symfony's session
     *
     * @param Session $session
     * @return \Evence\Bundle\GridBundle\Grid\Grid
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
        return $this;
    }

    /**
     * Set symfony's doctrine service
     *
     * @param Registry $doctrine
     * @return \Evence\Bundle\GridBundle\Grid\Grid
     */
    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        return $this;
    }

    /**
     * Get current template resource
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Get getQueryBuilder for the current entity
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        /**
         *
         * @var $qb \Doctrine\ORM\QueryBuilder
         */
        $qb = $this->doctrine->getRepository($this->getEntityName())
            ->createQueryBuilder('e');

        return $qb;
    }

    /**
     * Get getQueryBuilder for the current entity
     *
     * @return Builder
     */
    public function getDocumentBuilder()
    {
        /**
         *
         * @var DocumentManager
         */
        $dm = $this->doctrineMongoDb->getManager();


        return $dm->createQueryBuilder($this->getDocumentName());
    }

    /**
     * Count rows for current data source
     *
     * @return integer0
     */
    public function countRows($options)
    {
        if ($this->getDataSourceType() == self::DATA_SOURCE_ENTITY) {
            /** @var $qb \Doctrine\ORM\QueryBuilder */
            $qb = $this->getQueryBuilder();

            call_user_func_array($options['querybuilder_callback'], array(
                $qb,
                self::QUERY_COUNT
            ));

            $qb->select('count(e.id)');

            $event = new GridEvent();
            $event->setGrid($this)->setQuerybuilder($qb);
            $this->eventDispatcher->dispatch(GridEvent::POST_SET_COUNT_QUERY, $event);

            $this->filterQuery($qb);


            $qb->resetDQLPart('orderBy');



            $val = $qb->getQuery()->getSingleScalarResult();

            return $val;
        } else if ($this->getDataSourceType() == self::DATA_SOURCE_DOCUMENT) {
            $qb = $this->getDocumentBuilder();

            call_user_func_array($options['documentbuilder_callback'], array(
                $qb,
                self::QUERY_COUNT
            ));

            $event = new GridEvent();
            $event->setGrid($this)->setQuerybuilder($qb);
            $this->eventDispatcher->dispatch(GridEvent::POST_SET_COUNT_QUERY, $event);


            $this->filterQuery($qb);

            $count = $qb->getQuery()->execute()->count();


            return $count;
        }

        return count($this->dataSource);
    }

    /**
     * Get the data from the current data source (limited and sorted)
     *
     * @return array|object
     */
    private function getData($options)
    {
        $this->getPagination()->setTotalRows($this->countRows($options));

        if ($this->getDataSourceType() == self::DATA_SOURCE_ENTITY) {
            $qb = $this->getQueryBuilder()
                ->setMaxResults($this->getPagination()
                    ->getMaxRecords())
                ->setFirstResult($this->getPagination()
                    ->getFirstRecord());

            $event = new GridEvent();
            $event->setGrid($this)->setQuerybuilder($qb);

            //   $this->eventDispatcher->dispatch(GridEvent::PRE_SET_QUERY, $event);


            call_user_func_array($options['querybuilder_callback'], array(
                $qb,
                self::QUERY_SELECT
            ));


            $event->setGrid($this)->setQuerybuilder($qb);
            $this->eventDispatcher->dispatch(GridEvent::POST_SET_QUERY, $event);

            $this->filterQuery($qb);


            if ($this->getSortBy()) {

                $by = $this->getSortBy();
                $fc = $this->getFieldConfigurator();


                if (!empty($fc[$by])) {

                    $dataField = $fc[$by];

                    if ($dataField->getObjectReference())
                        $qb->orderBy('e.' . $this->getSortBy(), $this->getSortOrder());
                    else
                        $qb->orderBy($this->getSortBy(), $this->getSortOrder());
                }
                else {
                    $qb->orderBy($this->getSortBy(), $this->getSortOrder());
                }
            }

            $data = $qb->getQuery()->getResult();
        } else if ($this->getDataSourceType() == self::DATA_SOURCE_DOCUMENT) {
            $qb = $this->getDocumentBuilder()->limit($this->getPagination()
                ->getMaxRecords())->skip($this->getPagination()
                ->getFirstRecord());

            call_user_func_array($options['documentbuilder_callback'], array(
                $qb,
                self::QUERY_SELECT
            ));


            $event = new GridEvent();
            $event->setGrid($this)->setQuerybuilder($qb);
            $this->eventDispatcher->dispatch(GridEvent::POST_SET_QUERY, $event);

            $this->filterQuery($qb);

            if ($this->getSortBy()) {

                $by = $this->getSortBy();
                $fc = $this->getFieldConfigurator();


                if (empty($fc[$by])) {
                    throw new \Exception('There is no field called ' . $by);
                }

                $dataField = $fc[$by];

                if ($qb instanceof Builder) {
                    $qb->sort($this->getSortBy(), $this->getSortOrder());
                }
            }


            $data = $qb->getQuery()->execute();


        } else {

            $data = $this->getDataSource();

            $event = new GridEvent();
            $event->setGrid($this)->setData($data);
            $this->eventDispatcher->dispatch(GridEvent::PRE_MODIFY_ARRAY, $event);


            $data = $event->getData();

            if ($this->getSortBy() && !empty($data)) {
                foreach ($data as $row) {
                    $sortBy[] = $row[$this->getSortBy()];
                }
                array_multisort($sortBy, ($this->getSortOrder() == 'ASC' ? SORT_ASC : SORT_DESC), $data);
            }
            $data = array_splice($data, $this->getPagination()->getFirstRecord(), $this->getPagination()->getMaxRecords());

        }

        $this->rawData = $data;

        return $this->prepareData($data, $options);
    }

    public function clear()
    {
        foreach ($this->rawData as $rid => $row) {

            if ($this->getDataSourceType() == self::DATA_SOURCE_ENTITY) {

                if (is_object($row)) {
                    $this->doctrine->getManager()->detach($row);
                } else {
                    $id = $rid;
                    foreach ($row as $key => $value) {
                        if (is_numeric($key) && is_object($value)) {
                            $this->doctrine->getManager()->detach($value);
                            unset($row[$key]);
                        }
                    }
                }

            }
            unset($this->rawData[$rid]);
        }

        $this->rawData = [];
    }


    public function filterQuery($qb)
    {

        /* Filters here */

        $identifier = null;

        if ($this->filterConfigurator->hasFields()) {
            $form = $this->filterConfigurator->getFormBuilder()->getForm();
            $form->handleRequest($this->request);
            $identifier = $form->get('_identifier')->getData();
        }


        if ($identifier == $this->getIdentifier() && $form->isValid()) {

            foreach ($form->all() as $item) {

                $name = $item->getName();

                if ($name != '_identifier' && $name != '_search') {

                    if (!$this->filterConfigurator->getFilterMapper()->hasField($name)) {

                        $data = $item->getData();

                        if ($data) {
                            if ($qb instanceof Builder) {
                                if ($item->getNormData() && method_exists($data, 'getId')) $data = $data->getId();
                                $qb->field($name)->equals($data);
                            } elseif ($qb instanceof \Doctrine\ORM\QueryBuilder) {
                                $qb->andWhere('e.' . $name . ' = :' . $name);
                                $qb->setParameter($name, $data);
                            }
                        }
                    }
                }
            }




            foreach ($this->filterConfigurator->getFilterMapper() as $mapper) {
                /**
                 *
                 * @var $mapper FilterMapper
                 */
                $mapper->filterQuery($qb, $form);
            }
        }

        if(($searchQuery = $this->request->get($this->getPrefix().'ss') ) && !empty($this->simpleSearchFields)){

            $args = [];
            foreach ($this->simpleSearchFields as $field){
                $args[] = $qb->expr()->like('e.'.$field, ':sq');
            }

            $expr = $qb->expr();
            $qb->andWhere(call_user_func_array([$expr, 'orX'], $args));
            $qb->setParameter('sq', '%'.$searchQuery.'%');
        }
    }

    /**
     * Get col value by the given source
     *
     * @param mixed $source
     * @param string $col
     *            Fieldname of the source
     * @throws UnknownGridFieldException Whether the field name doens't exists.
     * @return mixed Value of the col
     */
    public function getColBySource($source, $col)
    {
        if ($this->getDataSourceType() == self::DATA_SOURCE_ENTITY) {

            if ($this->isAssociation($col)) {
                return $this->getAssociation($col, $source);
            }
            return $this->getValueFromSource($source, $col);
        } else if ($this->getDataSourceType() == self::DATA_SOURCE_DOCUMENT) {

            if ($this->isAssociation($col)) {
                return $this->getAssociation($col, $source);
            }
            return $this->getValueFromSource($source, $col);
        } elseif ($this->getDataSourceType() == self::DATA_SOURCE_ARRAY) {
            if (isset($source[$col]))
                return $source[$col];

            throw new UnknownGridFieldException('Field ' . $col . " doesn't exists in array.");
        }
    }

    public function renderColAttributes($attributes, $row, $col = null, $tdType =  self::TD_TYPE_COL){

        if(is_callable($attributes))
            return call_user_func_array($attributes, [$tdType, $row, $col]);

        return $attributes;
    }

    public function renderRowAttributes($attributes, $row){


        if(is_callable($attributes))
            return call_user_func_array($attributes, [$row]);

        return $attributes;
    }

    /**
     * Get Association for col
     *
     * @param integer $id
     * @param mixed $source
     * @return mixed new Source
     */
    public function getAssociation($id, $source)
    {
        $path = explode(".", $id);
        while (count($path) > 0) {
            $id = array_shift($path);
            $source = $this->getValueFromSource($source, $id);
        }

        return $source;
    }

    /**
     * Gets the value from a source
     *
     * @param mixed $source
     *            Data source
     * @param string $id
     *            Field identifier
     * @throws \Exception
     * @return mixed
     */
    public function getValueFromSource($source, $id)
    {
        if ($this->getDataSourceType() == Grid::DATA_SOURCE_ENTITY) {

            $dataField = null;
            $fc = $this->getFieldConfigurator();
            if (!empty($fc[$id]))
                $dataField = $fc[$id];

            if ($dataField && !$dataField->getObjectReference())
                return $this->getAccessor()->getValue($this->metaFields[$source->getId()], '[' . $id . ']');
            else
                return $this->getAccessor()->getValue($source, $id);
        } else if ($this->getDataSourceType() == Grid::DATA_SOURCE_DOCUMENT) {
            $dataField = null;
            $fc = $this->getFieldConfigurator();
            if (!empty($fc[$id]))
                $dataField = $fc[$id];
            return $this->getAccessor()->getValue($source, $id);
        } elseif ($this->getDataSourceType() == Grid::DATA_SOURCE_ARRAY) {
            if (!array_key_exists($id, $source))
                throw new \Exception('Uknown field ' . $id . ' in datasource array: ' . print_r($source, true));

            return $source[$id];
        }

        /*
         * $method = 'get' . str_replace("_", "", ucfirst($id));
         *
         * if ($this->getDataSourceType() == Grid::DATA_SOURCE_ENTITY) {
         * if (! method_exists($source, $method)) {
         * throw new \Exception('Uknown field ' . $id . ' in datasource ' . $this->getEntityName());
         * }
         * return $source->$method();
         * } elseif ($this->getDataSourceType() == Grid::DATA_SOURCE_ARRAY) {
         * if (! array_key_exists($id, $source))
         * throw new \Exception('Uknown field ' . $id . ' in datasource array: ' . print_r($source, true));
         *
         * return $source[$id];
         * }
         */
    }

    /**
     * Checks whether identifier is an association or not.
     *
     * @param string $id
     *            Field identifier
     * @return boolean
     */
    public function isAssociation($id)
    {
        if (stristr($id, ".")) {
            return true;
        }
        return false;
    }

    /**
     * Converts the data to useable data.
     *
     * @param string $data
     *            Raw data
     * @return multitype:\stdClass
     */
    public function prepareData($data, $options = [])
    {

        $preparedData = new \stdClass();
        $preparedData->rows = array();
        $preparedData->multipleActions = array();

        $sData = [];


        if ($this->getDataSourceType() == Grid::DATA_SOURCE_ENTITY) {
            foreach ($data as $rid => $row) {

                if (is_object($row)) {
                    $sData[] = $row;
                } else {

                    $id = $rid;
                    foreach ($row as $key => $value) {

                        if (is_numeric($key) && is_object($value)) {

                            if ($this->getAccessor()->isReadable($value, 'id'))
                                $id = $this->getAccessor()->getValue($value, 'id');

                            $sData[] = $value;
                        } elseif (!is_numeric($key)) {
                            $this->metaFields[$id][$key] = $value;
                        }
                    }
                }
            }
        } elseif ($this->getDataSourceType() == Grid::DATA_SOURCE_DOCUMENT) {


            foreach ($data as $rid => $row) {
                $sData[] = $row;
            }

        } else {
            $sData =& $data;
        }

        foreach ($sData as $rid => $row) {
            $prow = new \stdClass();
            $prow->orginal = $row;
            $prow->cols = array();
            foreach ($this->fieldConfigurator as $key => $field) {
                $prow->cols[$key] = new \stdClass();
                $prow->cols[$key]->value = $field->getData($row, $options);
                $prow->cols[$key]->fieldname = $field->getType()->getName();

                $prow->multipleIdentifier = null;

                try {
                    if ($this->getMultipleIdentifierField() != null)
                        $prow->multipleIdentifier = $this->getColBySource($row, $this->getMultipleIdentifierField());
                } catch (\Exception $e) {

                }

                $prow->actions = array();
                $prow->mappedParams = $this->actionConfigurator->getParametersBySource($row);
            }
            foreach ($this->actionConfigurator as $key => $action) {

                $opt = $action->getOptions();

                if (!$opt['multiple']) {

                    /**
                     *
                     * @var $action Action
                     */
                    if ($action->isVisible($row)) {
                        $act = new \stdClass();

                        $act->url = $action->generateUrl($row);
                        $act->label = $action->getLabel();
                        $act->options = $action->getOptions();

                        $prow->actions[] = $act;
                    }
                }
            }
            $preparedData->rows[] = $prow;
        }

        foreach ($this->actionConfigurator as $key => $action) {

            $opt = $action->getOptions();

            if ($opt['multiple']) {

                /**
                 *
                 * @var $action Action
                 */
                if ($action->isVisible()) {
                    $act = new \stdClass();

                    $act->url = $action->generateUrl();
                    $act->label = $action->getLabel();
                    $act->options = $action->getOptions();

                    $preparedData->multipleActions[] = $act;
                }
            }
        }

        return $preparedData;
    }

    /**
     * Create and return the Field configurator
     *
     * @return \Evence\Bundle\GridBundle\Grid\GridFieldConfigurator
     */
    public function createFieldConfigurator()
    {
        return $this->fieldConfigurator = new GridFieldConfigurator($this);
    }

    /**
     * Create and return the Field configurator
     *
     * @return \Evence\Bundle\GridBundle\Grid\GridFilterConfigurator
     */
    public function createFilterConfigurator()
    {

        return $this->filterConfigurator = new GridFilterConfigurator($this, $this->formFactory);


    }

    /**
     * Create and return the Action configurator
     *
     * @return \Evence\Bundle\GridBundle\Grid\GridActionConfigurator
     */
    public function createActionConfigurator()
    {
        return $this->actionConfigurator = new GridActionConfigurator($this);
    }

    /**
     * Renders the view
     *
     * @param array $options
     * @return Response
     */
    public function renderView($options)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(array(
            'mode' => 'view',
            'checkbox' => true,
            'numbers' => true,
            'pagination' => true,
            'bulkActions' => true,
            'selectLimit' => true,
            'paginationInfo' => true,
            'title' => 'Unamed grid',
            'tableAttributes' => array(),
            'formAttributes' => array(),
            'trAttributes' => array(),
            'tdAttributes' => array(),
            'actionAttributes' => array(),
            'footer' => true,
            'full' => false,
            'querybuilder_callback' => array(
                $this,
                'qbCallback'
            ),
            'documentbuilder_callback' => array(
                $this,
                'dbCallback'
            ),
            'template' => $this->getTemplate()
        ));
        $options = $resolver->resolve(array_merge($this->getOptions(), $options));


        $event = new GridEvent();
        $event->setGrid($this);

        $this->eventDispatcher->dispatch(GridEvent::PRE_CONFIGURE, $event);


        if ($this->fieldConfigurator == null)
            $this->configureFields($this->createFieldConfigurator());

        if ($this->actionConfigurator == null)
            $this->configureActions($this->createActionConfigurator());

        if ($this->filterConfigurator == null)
            $this->configureFilter($this->createFilterConfigurator());

        $this->eventDispatcher->dispatch(GridEvent::POST_CONFIGURE, $event);

        $filter = $this->filterConfigurator;

        if ($filter->hasFields()) {
            if (!$filter->getFormBuilder()->has('_search'))
                $filter->getFormBuilder()->add('_search', SubmitType::class);
            $filter->getFormBuilder()->add('_identifier', HiddenType::class, array(
                'data' => $this->getIdentifier(),
                'mapped' => false
            ));
        }


        if ($options['mode'] == 'csv') {
            $this->getPagination()->setForceLimit(500);
        }


        $data = $this->getData($options);

        $args = array(
            'fields' => $this->fieldConfigurator,
            'filter' => $this->filterConfigurator,
            'pagination' => $this->getPagination(),
            'itemsperpage' => $this->getItemsPerPage(),
            'grid' => $this,
            'gridOptions' => $options,
            'rows' => $data->rows,
            'multipleActions' => $data->multipleActions,
            'form' => $this->filterConfigurator->getForm()
                ->createView()
        );

        if ($options['mode'] == 'csv') {
            return $this->renderCsv($options, $args);
        }


        $grid = $this->templating->render($options['template'], $args);

        return $grid;
    }


    public function renderCsv($options, $args)
    {

        $self = $this;
        $stream = function () use ($options, $args, $self) {

            /**
             * @var Pagination $pagination
             */
            $pagination = $args['pagination'];

            $cols = array();
            foreach ($args['fields'] as $field) {
                $cols[] = $this->strToCsv($field->getLabel());
            }
            print implode(";", $cols) . "\n";


            if ($options['full'])
                $totalPages = $pagination->getTotalPages();
            else
                $totalPages = 1;


            $i = 0;

            do {

                $self->csvPage($i, $args, $options);


                $i++;
                $pagination->setForcePage($i);


            } while ($i < $totalPages);
        };


        $res = new StreamedResponse($stream, 200, []);
        $res->headers->set('Content-Disposition', 'attachment; filename="grid-' . str_replace(array(" ", "_", "'"), "-", $options['title']) . ($options['full'] ? '-full' : '') . '.csv";');
        $res->headers->set('Content-Type', 'application/csv');


        return $res;
    }

    public function csvPage($i, $args, $options)
    {

        if ($i > 0) {
            $data = $this->getData($options);
        } else {
            $data = new \stdClass();
            $data->rows = $args['rows'];
        }

        foreach ($data->rows as $row) {


            $rowArray = array();
            foreach ($row->cols as $col) {
                $rowArray[] = $this->strToCsv($col->value->getValue());
            }
            print implode(";", $rowArray) . "\n";
        }

        unset($data->rows);
        unset($data);

        $this->clear();
    }

    public function strToCsv($str, $delimter = ';', $enclose = '"')
    {


        if (is_array($str)) {
            $str = implode(",", $str);
        }

        $str = str_replace($enclose, $enclose . $enclose, $str);


        $testStr = trim($str);
        if ($testStr != $str || stristr($str, $delimter) || stristr($str, $enclose)) ;
        return $enclose . $str . $enclose;

        return $str;
    }

    /**
     * Get default limit
     *
     * @return \Evence\Bundle\GridBundle\Grid\Limit
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set default limit
     *
     * @param integer $limit
     * @return \Evence\Bundle\GridBundle\Grid\Grid
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Get current datasource
     *
     * @return array
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

    /**
     * Set current datasource
     *
     * @param array $dataSource
     * @return \Evence\Bundle\GridBundle\Grid\Grid
     */
    public function setDataSource($dataSource)
    {
        $this->dataSource = $dataSource;
        return $this;
    }

    /**
     * Get parameter prefix
     *
     * @return \Evence\Bundle\GridBundle\Grid\unknown
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Get the defaultSortBy
     *
     * @return string
     */
    public function getDefaultSortBy()
    {
        return $this->sortBy;
    }

    /**
     * Get the defaultSortOrder
     *
     * @return string
     */
    public function getDefaultSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * Get current sortBy
     *
     * @return string
     */
    public function getSortBy()
    {
        if (!$sortBy = $this->request->get($this->getPrefix() . 's', $this->getDefaultSortBy())) {

            return $this->sortBy;

            /*
             * if (! $this->fieldConfigurator->hasField($sortBy)) {
             * throw new UnknownGridFieldException('Unknown grid field ' . $sortBy);
             * }
             */
        }

        return $sortBy;
    }

    /**
     * Get current sort order
     *
     * @return string
     */
    public function getSortOrder()
    {
        $sortOrder = $this->request->get($this->getPrefix() . 'o', $this->getDefaultSortOrder());

        if ($sortOrder != self::SORT_ORDER_ASC && $sortOrder != self::SORT_ORDER_DESC)
            $sortOrder = self::SORT_ORDER_ASC;

        return $sortOrder;
    }

    /**
     * Creates, initializes and returns the paignation object
     *
     * @return \Evence\Bundle\GridBundle\Pagination\Pagination
     */
    private function getPagination()
    {
        if ($this->pagination == null) {
            $this->pagination = new Pagination();
            $this->pagination->setRequest($this->request)
                ->setRouter($this->router)
                ->setLimit($this->limit)
                ->setLimitParam($this->getPrefix() . 'l')
                ->setPageParam($this->getPrefix() . 'p');
        }
        return $this->pagination;
    }

    /**
     * Generates the sort URL
     *
     * @param string $sortBy
     *            Valid fieldname of the current datasource
     * @param string $direction
     *            Sort order direction, possible options: 'ASC' or 'DESC'
     */
    public function generateSortUrl($sortBy, $direction)
    {
        return $this->router->generate($this->request->get('_route'), array_merge(array_merge($this->request->query->all(), $this->request->attributes->get('_route_params')), array(
            $this->getPrefix() . 's' => $sortBy,
            $this->getPrefix() . 'o' => $direction
        )));
    }

    /**
     * Generates the limit URL
     *
     * @param unknown $limit
     */
    public function generateLimitUrl($limit)
    {
        return $this->router->generate($this->request->get('_route'), array_merge(array_merge($this->request->query->all(), $this->request->attributes->get('_route_params')), array(
            $this->getPrefix() . 'l' => $limit
        )));
    }


    public function generateDownloadUrl($mode, $full = false)
    {
        return $this->router->generate($this->request->get('_route'), array_merge(array_merge($this->request->query->all(), $this->request->attributes->get('_route_params')), array(
            'grid_id' => $this->getPrefix(),
            'grid_mode' => $mode,
            'grid_options' => ['full' => $full]
        )));
    }


    /**
     * Returns an array for items per page
     *
     * @return multitype:number
     */
    public function getItemsPerPage()
    {
        return array(
            20,
            50,
            100,
            200,
            500,
            1000
        );
    }

    /**
     * Get current options
     *
     * @return multitype:
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set parameter prefix
     *
     * @param string $prefix
     * @return \Evence\Bundle\GridBundle\Grid\Grid
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * Get Symfony's securityContext service
     *
     * @return \Symfony\Component\Security\Core\SecurityContext
     */
    public function getSecurityContext()
    {
        @trigger_error("getSecurityContext() is deprecated and will be removed", E_USER_DEPRECATED);
        return $this->securityContext;
    }

    /**
     * Set Symfony's securityContext service
     *
     * @param SecurityContext $securityContext
     * @deprecated
     * @return \Evence\Bundle\GridBundle\Grid\Grid
     */
    public function setSecurityContext(SecurityContext $securityContext)
    {
        @trigger_error("setSecurityContext() is deprecated and will be removed", E_USER_DEPRECATED);
        $this->securityContext = $securityContext;
        return $this;
    }


    /**
     * Get Symfony's tokenStorage service
     *
     * @return TokenStorageInterface
     */
    public function getTokenStorage()
    {

        return $this->tokenStorage;
    }


    /**
     * Set Symfony's securityContext service
     *
     * @param TokenStorageInterface $tokenStorage
     * @return \Evence\Bundle\GridBundle\Grid\Grid
     */
    public function setTokenStorage(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
        return $this;
    }

    /**
     * Get symfony's router service
     *
     * @return \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Creates the view
     *
     * @todo Use for different views like: CSV, PDF and Excel
     * @return \Evence\Bundle\GridBundle\Grid\Grid
     */
    public function createView($template = '')
    {
        if ($template)
            $this->template = $template;
        return $this;
    }

    public function qbCallback(\Doctrine\ORM\QueryBuilder $qb)
    {
    }

    public function dbCallback(Builder $qb)
    {
    }


    /**
     * Get formFactory
     *
     * @return \Symfony\Component\Form\FormFactoryInterface
     */
    public function getFormFactory()
    {
        return $this->formFactory;
    }

    /**
     * Set formFactory
     *
     * @param FormFactoryInterface $formFactory
     * @return \Evence\Bundle\GridBundle\Grid\Grid
     */
    public function setFormFactory(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
        return $this;
    }

    public function getEntityClassMeta()
    {
        return $this->doctrine->getManager()->getClassMetadata($this->getEntityName());
    }

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getIdentifier()
    {
        return ($this->identifier ?: $this->getPrefix());
    }

    public function getFieldConfigurator()
    {
        return $this->fieldConfigurator;
    }

    public function getActionConfigurator()
    {
        return $this->actionConfigurator;
    }

    public function setSortBy($sortBy)
    {
        $this->sortBy = $sortBy;
        return $this;
    }

    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }

    public function getMultipleIdentifierField()
    {
        return $this->multipleIdentifierField;
    }

    public function setMultipleIdentifierField($multipleIdentifierField)
    {
        $this->multipleIdentifierField = $multipleIdentifierField;
        return $this;
    }

    /**
     *
     * @return PropertyAccessor
     */
    public function getAccessor()
    {
        if (!$this->accessor) {
            $this->accessor = PropertyAccess::createPropertyAccessor();
        }
        return $this->accessor;
    }

    public function getRawData()
    {
        return $this->rawData;
    }

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
        return $this;
    }

    public function getDoctrineMongoDb()
    {
        return $this->doctrineMongoDb;
    }

    public function setDoctrineMongoDb(ManagerRegistry $doctrineMongoDb)
    {
        $this->doctrineMongoDb = $doctrineMongoDb;
        return $this;
    }

    public function getDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * @return \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    public function getAuthorizationChecker()
    {
        return $this->authorizationChecker;
    }

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @return \Evence\Bundle\GridBundle\Grid\Grid
     */
    public function setAuthorizationChecker(AuthorizationCheckerInterface $authorizationChecker)
    {

        $this->authorizationChecker = $authorizationChecker;
        return $this;
    }


    public function getFilterConfigurator()
    {
        if (!$this->filterConfigurator)
            $this->filterConfigurator = $this->createFilterConfigurator();
        return $this->filterConfigurator;
    }

    public function hasSimpleSearch(){
        return !empty($this->simpleSearchFields);
    }
}
    