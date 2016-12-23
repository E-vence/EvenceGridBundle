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

    private $forceLimit = null;

    private $forcePage  = null;

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
        return ($this->getRequest()->get($this->limitParam) ?  : $this->getLimit());
    }

    /**
     * Get's first record
     *
     * @return number
     */
    public function getFirstRecord()
    {
        return ($this->forceLimit !== null ? $this->forceLimit : $this->getCurrentLimit()) *  ($this->forcePage !== null ? $this->forcePage : $this->getCurrentPage());
    }

    /**
     * Get's max records
     */
    public function getMaxRecords()
    {
        return ($this->forceLimit !== null ? $this->forceLimit : $this->getCurrentLimit());
    }

    /**
     * Returns total pages
     *
     * @return number
     */
    public function getTotalPages()
    {
        $totalRecords = $this->getTotalRecords();
        
        return ($totalRecords > 0 ? ceil($totalRecords / $this->getCurrentLimit()) : 0);
    }

    public function setTotalRows($rows)
    {
        $this->totalRecords = $rows;
    }

    public function getTotalRecords()
    {
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
    public function getFirstPage()
    {
        return begin($this->array);
    }

    /**
     * Get the last page
     *
     * @return PaginationPage
     */
    public function getLastPage()
    {
        return end($this->array);
    }

    /**
     * Whether or not there is a next page.
     *
     * @return boolean
     */
    public function hasNextPage()
    {
        $p = $this->getCurrentPage() + 1;
        return (! empty($this->array[$p]) ? true : false);
    }

    /**
     * Whether or not there is a previous page.
     *
     * @return boolean
     */
    public function hasPreviousPage()
    {
        $p = $this->getCurrentPage() - 1;
        return (! empty($this->array[$p]) ? true : false);
    }

    /**
     * Get the next page
     *
     * @throws \Exception If there is no next page available
     * @return PaginationPage
     */
    public function getNextPage()
    {
        if (! $this->hasNextPage())
            throw new \Exception('There is no next page');
        $p = $this->getCurrentPage() + 1;
        return $this->array[$p];
    }

    /**
     * Get the previous page
     *
     * @throws \Exception If there is no previous page available
     * @return PaginationPage
     */
    public function getPreviousPage()
    {
        $p = $this->getCurrentPage() - 1;
        
        if (! $this->hasPreviousPage())
            throw new \Exception('There is no previous page');
        return $this->array[$p];
    }

    /**
     * Generate the url for the target page
     *
     * @param PaginationPage $page            
     */
    public function generateUrl(PaginationPage $page)
    {
        return $this->getRouter()->generate($this->getRequest()
            ->get('_route'), array_merge($this->getRouteVars(), array(
            $this->pageParam => $page->getNumber(),
            $this->limitParam => $this->getCurrentLimit()
        )));
    }

    /**
     * Get the Symfony's router service
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Set the Symfony's router service
     *
     * @param unknown $router            
     * @return \Evence\Bundle\GridBundle\Pagination\Pagination
     */
    public function setRouter($router)
    {
        $this->router = $router;
        return $this;
    }

    /**
     * Get pages
     *
     * @param
     *            integer (optional) $maxPages Maximum page numbers to show
     * @return \Evence\Bundle\GridBundle\Pagination\Pagination
     */
    public function getPages($maxPages = null)
    {
        $this->init();
        if (! $maxPages)
            return $this;
        
        $pagesLeft = floor($maxPages / 2);
        $start = $this->getCurrentPage() - $pagesLeft;
        if ($start < 0) {
            $pagesLeft -= abs($start);
            $start = 0;
        }
        
        return array_slice($this->array, $start, $maxPages);
    }

    /**
     * Get all route vars from symfony's router service
     */
    private function getRouteVars()
    {
        return array_merge($this->getRequest()->query->all(), $this->getRequest()->attributes->get('_route_params'));
    }

    /**
     * Get items per page
     *
     * @param string $steps
     *            Square of the numbers
     * @return multitype:\stdClass
     */
    public function getItemsPerPage($steps)
    {
        $pp = $steps;
        $i = 0;
        $limitArray = array();
        for ($i = 0; $i < 5; $i ++) {
            $page = new \stdClass();
            $page->url = $this->getRouter()->generate($this->getRequest()
                ->get('_route'), array_merge($this->getRouteVars(), array(
                $this->limitParam => $pp
            )));
            $page->name = $pp;
            $page->current = ($this->getCurrentLimit() == $pp);
            
            $limitArray[] = $page;
            $pp *= 2;
        }
        return $limitArray;
    }

    /**
     * Set limitParam
     *
     * @param unknown $limitParam            
     * @return \Evence\Bundle\GridBundle\Pagination\Pagination
     */
    public function setLimitParam($limitParam)
    {
        $this->limitParam = $limitParam;
        return $this;
    }

    /**
     * Set pageParam
     *
     * @param unknown $pageParam            
     * @return \Evence\Bundle\GridBundle\Pagination\Pagination
     */
    public function setPageParam($pageParam)
    {
        $this->pageParam = $pageParam;
        return $this;
    }

    /**
     * Get limitParam
     *
     * @return string
     */
    public function getLimitParam()
    {
        return $this->limitParam;
    }

    /**
     * Get pageParam
     *
     * @return string
     */
    public function getPageParam()
    {
        return $this->pageParam;
    }

    /**
     * @return null
     */
    public function getForceLimit()
    {
        return $this->forceLimit;
    }

    /**
     * @param null $forceLimit
     * @return Pagination
     */
    public function setForceLimit($forceLimit)
    {
        $this->forceLimit = $forceLimit;
        return $this;
    }

    /**
     * @return null
     */
    public function getForcePage()
    {
        return $this->forcePage;
    }

    /**
     * @param null $forcePage
     * @return Pagination
     */
    public function setForcePage($forcePage)
    {
        $this->forcePage = $forcePage;
        return $this;
    }


}