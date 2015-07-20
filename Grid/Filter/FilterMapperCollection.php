<?php 
/**
 * Copyright Ruben Harms 2015
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
 
namespace Evence\Bundle\GridBundle\Grid\Filter;

 /**
  * Filter mapper collection class
  *
  * @author Ruben Harms <info@rubenharms.nl>
  * @link http://www.rubenharms.nl
  * @link https://www.github.com/RubenHarms
  * @package evence/grid-bundle
  * @subpackage Filter 
  */
 class FilterMapperCollection implements  \IteratorAggregate, \Countable {

     /**
      * Collection of all filter mappers
      * 
      * @var multitype:FilterMapper
      */
     private $mappers = array();
     
     public function __construct() {
         
     }
          
     /* (non-PHPdoc)
      * @see IteratorAggregate::getIterator()
      */
     public function getIterator() {
         return new \ArrayIterator($this->mappers);
     }     
     
     /* (non-PHPdoc)
      * @see Countable::count()
      */
     public function count(){
        return count($this->mappers);   
     }     
     
     /** 
      * Add new filter mapper
      * 
      * @param FilterMapper $fm
      * @return FilterMapperCollection
      */
     public function add(FilterMapper $fm){
         $this->mappers[] = $fm;         
         return $this;
     }  
     
     /**
      * Returns whether it has the specified Id or not
      * 
      * @param string $needle Id to search for
      * @return boolean
      */
     public function hasId($needle){
        foreach($this->mappers as $mapper){
            if($mapper->getId() == $needle) return true;
        } 
        
     }
     /**
      * Returns whether it has the specified Id or not
      * 
      * @param unknown $needle
      * @return boolean
      */
     public function hasField($needle){
         foreach($this->mappers as $mapper){
             if($mapper->getField() == $needle) return true;
         }
     
     }
 }