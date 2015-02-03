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

use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Query\QueryBuilder;
use Evence\Bundle\GridBundle\Grid\Exception\UnknownGridFieldException;
use Evence\Bundle\GridBundle\Pagination\Pagination;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\SecurityContext;
use Evence\Bundle\GridBundle\Grid\Misc\Action;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

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
     * Returns the name of the entity for the data source
     * 
     * @return string
     */
    abstract public function getEntityName();

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
     * Data source: Array
     *
     * @var string
     */
    CONST DATA_SOURCE_ENTITY = 'entity';

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
    private $request = null;

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
    private $doctrine = null;

    /**
     * Symfony's sercurityContext service
     *
     * @var SecurityContext
     */
    private $securityContext = null;

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
    private $limit = 20;

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
    private $sortBy = null;

    /**
     * Default sort order
     *
     * @var string
     */
    private $sortOrder = 'ASC';

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
     * Grid pagination
     *
     * @var Pagination
     */
    private $pagination = null;

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
     * Count rows for current data source
     *
     * @return integer
     */
    public function countRows()
    {
        if ($this->getDataSourceType() == self::DATA_SOURCE_ENTITY) {
            $qb = $this->getQueryBuilder()->select('count(e.id)');
            return $qb->getQuery()->getSingleScalarResult();
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
        $this->getPagination()->setTotalRows($this->countRows());
        
        if ($this->getDataSourceType() == self::DATA_SOURCE_ENTITY) {
            $qb = $this->getQueryBuilder()
                ->setMaxResults($this->getPagination()
                ->getMaxRecords())
                ->setFirstResult($this->getPagination()
                ->getFirstRecord());
            
            call_user_func_array($options['querybuilder_callback'], array($qb));
            
            if ($this->getSortBy())
                $qb->orderBy('e.' . $this->getSortBy(), $this->getSortOrder());
            
            $data = $qb->getQuery()->getResult();
        } else {
            $data = $this->getDataSource();
            $data = array_splice($data, $this->getPagination()->getFirstRecord(), $this->getPagination()->getMaxRecords());
        }
        
        return $this->prepareData($data);
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
            $method = 'get' . strtoupper($col);
            if (method_exists($source, $method))
                return $source->$method();
            
            throw new UnknownGridFieldException('Field ' . $col . " doesn't exists in entity.");
        } elseif ($this->getDataSourceType() == self::DATA_SOURCE_ARRAY) {
            if (isset($source[$col]))
                return $source[$col];
            
            throw new UnknownGridFieldException('Field ' . $col . " doesn't exists in array.");
        }
    }

    /**
     * Converts the data to useable data.
     *
     * @param string $data
     *            Raw data
     * @return multitype:\stdClass
     */
    public function prepareData($data)
    {
        $preparedData = array();
        foreach ($data as $rid => $row) {
            $prow = new \stdClass();
            $prow->cols = array();
            foreach ($this->fieldConfigurator as $key => $field) {
                $prow->cols[$key] = new \stdClass();
                $prow->cols[$key]->value = $field->getData($row);
                $prow->cols[$key]->fieldname = $field->getType()->getName();
                $prow->actions = array();
            }
            foreach ($this->actionConfigurator as $key => $action) {
                /**
                 *
                 * @var $action Action
                 */
                if ($action->isVisible($row)){
                    $act = new \stdClass();
                
                    $act->url = $action->generateUrl($row);
                    $act->label = $action->getLabel();
                    $act->options = $action->getOptions();
                    
                    $prow->actions[] = $act;
                }
                
               
            }
            $preparedData[] = $prow;
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
            'checkbox' => true,
            'numbers' => true,
            'title' => 'Unamed grid',
            'tableAttributes' => array(),
            'formAttributes' => array(),
            'trAttributes' => array(),
            'tdAttributes' => array(),
            'actionAttributes' => array(),
            'footer' => true,
            'querybuilder_callback' => array($this, 'qbCallback'),
            'template' => $this->getTemplate()
        ));
        $options = $resolver->resolve(array_merge($this->getOptions(), $options));
        
        if ($this->fieldConfigurator == null)
            $this->configureFields($this->createFieldConfigurator());
        
        if ($this->actionConfigurator == null)
            $this->configureActions($this->createActionConfigurator());
        
        $grid = $this->templating->render($options['template'], array(
            'fields' => $this->fieldConfigurator,
            'pagination' => $this->getPagination(),
            'itemsperpage' => $this->getItemsPerPage(),
            'grid' => $this,
            'gridOptions' => $options,
            'rows' => $this->getData($options)
        ));
        
        return $grid;
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
        if ($sortBy = $this->request->get($this->getPrefix() . 's', $this->getDefaultSortBy())) {
            
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
     * @param string $sortBy Valid fieldname of the current datasource
     * @param string $direction Sort order direction, possible options: 'ASC' or 'DESC'
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
        return $this->securityContext;
    }

    /**
     * Set Symfony's securityContext service
     * 
     * @param SecurityContext $securityContext
     * @return \Evence\Bundle\GridBundle\Grid\Grid
     */
    public function setSecurityContext(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
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
    public function createView($template ='')
    {
        if($template) $this->template = $template;
        return $this;
    }
    
    public function qbCallback(\Doctrine\ORM\QueryBuilder $qb){
        
    }
}
    