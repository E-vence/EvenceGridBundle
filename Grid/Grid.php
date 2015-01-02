<?php
namespace Evence\Bundle\GridBundle\Grid;

use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Query\QueryBuilder;
use Evence\Bundle\GridBundle\Grid\Exception\UnknownGridFieldException;
use Evence\Bundle\GridBundle\Pagination\Pagination;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\SecurityContext;

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

    abstract public function configureFields(GridFieldConfigurator $FieldConfigurator);

    abstract public function getEntityName();

    abstract public function getDataSourceType();
    

    CONST DATA_SOURCE_ARRAY = 'array';

    CONST DATA_SOURCE_ENTITY = 'entity';

    CONST SORT_ORDER_ASC = 'ASC';

    CONST SORT_ORDER_DESC = 'DESC';

    /**
     *
     * @var TwigEngine
     */
    private $templating = null;

    /**
     *
     * @var Request
     */
    private $request = null;

    /**
     *
     * @var Router
     */
    private $router = null;

    /**
     *
     * @var Session
     */
    private $session = null;

    /**
     *
     * @var Registry
     */
    private $doctrine = null;

    
    /**
     * @var SecurityContext  
     */
    
    private $securityContext = null;
    
    
    /**
     * Default template
     */
    private $template = 'EvenceGridBundle:Grid:grid.html.twig';

    /**
     *
     * @var Limit
     */
    private $limit = 50;

    private $prefix = 'g';

    private $dataSource = array();

    private $sortBy = null;

    private $sortOrder = 'ASC';

    private $fieldConfigurator = null;

    private $pagination = null;

    public function setTemplating(TwigEngine $templating)
    {
        $this->templating = $templating;
        return $this;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    public function setRouter(Router $router)
    {
        $this->router = $router;
        return $this;
    }

    public function setSession(Session $session)
    {
        $this->session = $session;
        return $this;
    }

    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        return $this;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Get getQueryBuilder
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        /**
         *
         * @var $qb \Doctrine\ORM\QueryBuilder
         *     
         */
        $qb = $this->doctrine->getRepository($this->getEntityName())
            ->createQueryBuilder('e');
        
        return $qb;
    }

    public function countRows()
    {
        if ($this->getDataSourceType() == self::DATA_SOURCE_ENTITY) {
            $qb = $this->getQueryBuilder()->select('count(e.id)');
            return $qb->getQuery()->getSingleScalarResult();
        }
        return count($this->dataSource);
    }

    private function getData()
    {
        $this->getPagination()->setTotalRows($this->countRows());
        
        if ($this->getDataSourceType() == self::DATA_SOURCE_ENTITY) {
            $qb = $this->getQueryBuilder()
                ->setMaxResults($this->getPagination()
                ->getMaxRecords())
                ->setFirstResult($this->getPagination()
                ->getFirstRecord());
            
            if ($this->getSortBy())
                $qb->orderBy($this->getSortBy(), $this->getSortOrder());
            
            $data = $qb->getQuery()->getResult();
        } else {
            $data = $this->getDataSource();
            $data = array_splice($data, $this->getPagination()->getFirstRecord(), $this->getPagination()->getMaxRecords());
        }
        
        return $this->prepareData($data);
    }

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
            }
            $preparedData[] = $prow;
        }
        
        return $preparedData;
    }

    public function createFieldConfigurator()
    {
        return $this->fieldConfigurator = new GridFieldConfigurator($this);
    }

    public function renderView()
    {
        
        $resolver = new OptionsResolver();
        $resolver->setDefaults(array('checkbox' => true, 'numbers' => true));
        $resolver->resolve($this->getOptions());
        
        
        if ($this->fieldConfigurator == null)
            $this->configureFields($this->createFieldConfigurator());
        
        $grid = $this->templating->render($this->template, array(
            'fields' => $this->fieldConfigurator,
            'pagination' => $this->getPagination(),
            'itemsperpage' => $this->getItemsPerPage(),
            'grid' => $this,
            'rows' => $this->getData()
        ));
        
        return $grid;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function getDataSource()
    {
        return $this->dataSource;
    }

    public function setDataSource($dataSource)
    {
        $this->dataSource = $dataSource;
        return $this;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function getDefaultSortBy()
    {
        return $this->sortBy;
    }

    public function getDefaultSortOrder()
    {
        return $this->sortOrder;
    }

    public function getSortBy()
    {
        if ($sortBy = $this->request->get($this->getPrefix() . 's', $this->getDefaultSortBy())) {
            if (! $this->fieldConfigurator->hasField($sortBy)) {
                throw new UnknownGridFieldException('Unknown grid field ' . $sortBy);
            }
        }
        
        return $sortBy;
    }

    public function getSortOrder()
    {
        $sortOrder = $this->request->get($this->getPrefix() . 'o', $this->getDefaultSortOrder());
        
        if ($sortOrder != self::SORT_ORDER_ASC && $sortOrder != self::SORT_ORDER_DESC)
            $sortOrder = self::SORT_ORDER_ASC;
        
        return $sortOrder;
    }

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

    public function generateSortUrl($sortBy, $direction)
    {
        return $this->router->generate($this->request->get('_route'), array_merge(array_merge($this->request->query->all(), $this->request->attributes->get('_route_params')), array(
            $this->getPrefix() . 's' => $sortBy,
            $this->getPrefix() . 'o' => $direction
        )));
    }
    
    public function generateLimitUrl($limit) {        
        return $this->router->generate($this->request->get('_route'), array_merge(array_merge($this->request->query->all(), $this->request->attributes->get('_route_params')), array(
            $this->getPrefix() . 'l' => $limit,           
        )));
    }
    
    public function getItemsPerPage(){
        return array(20,50,100,200,500,1000);
    }
    
    public function getOptions(){
        return array();
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function getSecurityContext()
    {
        return $this->securityContext;
    }

    public function setSecurityContext(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
        return $this;
    }
 
 
}
    