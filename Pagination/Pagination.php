<?php
/**
 * Copyright Ruben Harms 2014
 *
 * Do not use, modify, sell and/or duplicate this script
 * without any permissions!
 *
 * This software is written and recorded by Ruben Harms!
 * Ruben Harms took all the necessary actions, juridical and
 * (hidden) technical, to protect her script against any use
 * without permission, any modify and against any unauthorized duplicate.
 *
 * Copied versions shall be recognized and compared with the recorded version.
 * The owner of this softare will take all legal steps against every kind of malpractice!
 */

namespace Evence\Bundle\GridBundle\Pagination;

use Symfony\Component\HttpFoundation\Request;

/**
 * Abstract class for pagination
 *
 * @author Ruben Harms <info@rubenharms.nl>
 * @link http://www.rubenharms.nl
 * @link https://www.github.com/RubenHarms
 * @package Onetoshop
 * @subpackage OnetowebDatabaseBundle
 */
class Pagination implements \Iterator, \Countable
{

    /**
     * Pagination array
     *
     * @var array
     */
    private $array = array(); 

    /**
     * Current offset
     *
     * @var integer
     */
    private $currentOffset = 0;

    /**
     * Current pagination position (for interal array)
     *
     * @var integer
     */
    private $position = 0;

    /**
     * Record limit
     *
     * @var integer
     */
    private $limit = 20;

    /**
     * Symfony's Request service
     *
     * @var Request
     */
    private $request = null;

    /**
     * Symfony's Router service
     *
     * @var Request
     */
    private $router = null;

    /**
     *
     * @var integer
     */
    private $current;

     protected $limitParam = 'l';    
    protected $pageParam = 'p';    
    
    private $init = false;
    
    private $totalRecords = null;

    

    /*
     * (non-PHPdoc)
     * @see Iterator::rewind()
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /*
     * (non-PHPdoc)
     * @see Iterator::current()
     */
    public function current()
    {
        $this->init();
        return $this->array[$this->position];
    }

    /*
     * (non-PHPdoc)
     * @see Iterator::key()
     */
    public function key()
    {
        return $this->position;
    }

    /*
     * (non-PHPdoc)
     * @see Iterator::next()
     */
    public function next()
    {
        $this->init();
        ++ $this->position;
    }

    /*
     * (non-PHPdoc)
     * @see Iterator::valid()
     */
    function valid()
    {
        $this->init();
        return isset($this->array[$this->position]);
    }  



    /**
     *
     * @return number
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     *
     * @param unknown $limit            
     * @return \Onetoweb\Bundle\DatabaseBundle\Utils\Pagination
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     *
     * @param Request $request            
     * @return \Onetoweb\Bundle\DatabaseBundle\Utils\Pagination
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     *
     * @return number
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     *
     * @param unknown $current            
     * @return \Onetoweb\Bundle\DatabaseBundle\Utils\Pagination
     */
    public function setCurrent($current)
    {
        $this->current = $current;
        return $this;
    }

    /**
     *
     * @return multitype:
     */
    public function getUrlCallback()
    {
        return $this->urlCallback;
    }

    /**
     *
     * @param array $urlCallback            
     * @return \Onetoweb\Bundle\DatabaseBundle\Utils\Pagination
     */
    public function setUrlCallback(array $urlCallback)
    {
        $this->urlCallback = $urlCallback;
        return $this;
    }

    /*
     * (non-PHPdoc)
     * @see Countable::count()
     */
    public function count()
    {
        return count($this->array);
    }

    /**
     * Get currentpage
     */
    private function getCurrentPage()
    {
        return ($this->getRequest()->get($this->pageParam) ?  : 0);
    }

    /**
     * Get's current limit
     */
    public function getCurrentLimit()
    {
        return ($this->getRequest()->get($this->limitParam) ?: $this->getLimit());
    }

    /**
     * Get's first record
     *
     * @return number
     */
    public function getFirstRecord()
    {
        return $this->getCurrentLimit() * $this->getCurrentPage();
    }

    /**
     * Get's max records
     */
    public function getMaxRecords()
    {
        return $this->getCurrentLimit();
    }

    /**
     * Returns total pages
     *
     * @return number
     */
    private function getTotalPages()
    {
        $totalRecords = $this->getTotalRecords();
        
        return ($totalRecords > 0 ? ceil($totalRecords / $this->getCurrentLimit()) : 0 );
    }

  
    public function setTotalRows($rows){
        $this->totalRecords = $rows;
    }
    
    
    public function getTotalRecords(){
                
        return $this->totalRecords;        
    }
    

    /**
     * Initalizes the pages inside the iterator
     * 
     * @return boolean
     */
    public function init()
    {
        if ($this->init)
            return false;
        
        for ($i = 0; $i < $this->getTotalPages(); $i ++) {           
            $page = new PaginationPage();
            $page->setPagination($this)
                ->setCurrent($i == $this->getCurrentPage())
                ->setName($i + 1)         
                ->setNumber($i);
            $this->array[] = $page;
        }
        
        return $this->init = true;
    }
    
    /**
     * Get the first page
     * 
     * @return PaginationPage
     */
    public function getFirstPage(){
       return begin($this->array); 
    }
    
    /**
     * Get the last page
     * 
     * @return PaginationPage
     */
    public function getLastPage(){
       return end($this->array); 
    }
    
    
    /**
     * Whether or not there is a next page.
     * 
     * @return boolean
     */
    public function hasNextPage(){
        $p = $this->getCurrentPage()+1;
        return (!empty($this->array[$p]) ? true : false);
    }    
    

    /**
     * Whether or not there is a previous page.
     *
     * @return boolean
     */
    public function hasPreviousPage(){
        $p = $this->getCurrentPage()-1;
        return (!empty($this->array[$p]) ? true : false);
    }
    
    
    /**
     * Get the next page
     *
     * @throws \Exception If there is no next page available
     * @return PaginationPage
     */
    public function getNextPage(){  

       if(!$this->hasNextPage()) throw new \Exception('There is no next page');
       $p = $this->getCurrentPage()+1;  
       return $this->array[$p];
    }
    
     /**
     * Get the previous page
     *
     * @throws \Exception If there is no previous page available
     * @return PaginationPage
     */
    public function getPreviousPage(){
        $p = $this->getCurrentPage()-1;         
        
       if(!$this->hasPreviousPage()) throw new \Exception('There is no previous page');
        return $this->array[$p];
    }
    
    public function generateUrl(PaginationPage $page){     
        return $this->getRouter()->generate($this->getRequest()->get('_route'), array_merge($this->getRouteVars(), array($this->pageParam => $page->getNumber(), $this->limitParam => $this->getCurrentLimit())));
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function setRouter($router)
    {
        $this->router = $router;
        return $this;
    }
    
    public function getPages($maxPages = null){
        
        if(!$maxPages) return $this;
        
        $this->init();
            
        $pagesLeft = floor($maxPages/2);
        $start = $this->getCurrentPage()-$pagesLeft;
        if($start < 0){
            $pagesLeft-= abs($start);
            $start = 0;
        }   
        
        return array_slice($this->array, $start, $maxPages);
    }
    
    private function getRouteVars (){
        return array_merge($this->getRequest()->query->all(), $this->getRequest()->attributes->get('_route_params'));        
    }
    
    public function getItemsPerPage($steps){
        $pp = $steps;
        $i = 0;
        $limitArray = array();
        for ($i = 0; $i < 5; $i ++) {
            $page = new \stdClass();
            $page->url =  $this->getRouter()->generate($this->getRequest()->get('_route'), array_merge($this->getRouteVars(), array($this->limitParam => $pp)));
            $page->name = $pp;
            $page->current = ($this->getCurrentLimit() == $pp);
        
            $limitArray[] = $page;
            $pp *= 2;
        }
        return $limitArray;
    }

    public function setLimitParam($limitParam)
    {
        $this->limitParam = $limitParam;
        return $this;
    }

    public function setPageParam($pageParam)
    {
        $this->pageParam = $pageParam;
        return $this;
    }
 
 
}