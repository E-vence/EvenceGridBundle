<?php 

namespace Evence\Bundle\CoreBundle\Grid\Pagination;

/**
 *  Pagination page class, holds the information of a specific page.
 *
 * @author Ruben Harms <info@rubenharms.nl>
 * @link http://www.rubenharms.nl
 * @link https://www.github.com/RubenHarms
 * @package Onetoshop 
 * @subpackage OnetowebDatabaseBundle
 */  
class PaginationPage {
    
    /**
     * Name of the current page
     * 
     * @var string name
     */
    private $name;
    
    /**
     * Number of the current page
     *
     * @var string number
     */
    private $number;
    
    /**
     * Whether or not this is the current page
     * 
     * @var boolean
     */
    private $current;
    
    /**
     * Pagination utility
     * 
     * @var Pagination
     */
    private $pagination =null;

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     * 
     * @param string $name
     * @return PaginationPage
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    
    /**
     * Get whether or not this is the current page.
     * 
     * @return boolean
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * @param unknown $current
     * @return PaginationPage
     */
    public function setCurrent($current)
    {
        $this->current = $current;
        return $this;
    }

    /**
     * Get pagination
     * 
     * @return Pagination
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * Set pagination
     * 
     * @param unknown $pagination
     * @return PaginationPage
     */
    public function setPagination($pagination)
    {
        $this->pagination = $pagination;
        return $this;
    }
    
    /**
     * Get URL
     */
    public function getUrl(){
        return $this->getPagination()->generateUrl($this);   
    }

    /**
     * Get number
     * 
     * @return number
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set number
     * 
     * @param number $number
     * @return \Onetoweb\Bundle\DatabaseBundle\Utils\PaginationPage
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }
          
}