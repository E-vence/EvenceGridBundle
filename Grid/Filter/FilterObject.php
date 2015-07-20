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
  * Filter object, used to filter fields
  *
  * @author Ruben Harms <info@rubenharms.nl>
  * @link http://www.rubenharms.nl
  * @link https://www.github.com/RubenHarms
  * @package evence/grid-bundle
  * @subpackage filter 
  */ 
 class FilterObject {
     
     private $data = array();
     
     /**
      * Magic function call, to call set or get
      * 
      * @param string $name Method name
      * @param array $arguments Arguments
      * @return \Evence\Bundle\GridBundle\Grid\Filter\FilterObject|multitype:
      */
     public function __call($name, $arguments){
         $func = substr($name,0,3);        
         if($func == 'set' || $func == 'get'){
             $property = substr($name,3);
             $property[0] = strtolower($property[0]);

             if($func == 'set'){
                 return $this->set($property, $arguments[0]);
             }
             elseif($func == 'get'){
                 return $this->get($property);
             }             
         }
     }
     
     /**
      * Sets a property
      * 
      * @param string $property Property name
      * @param mixed $value
      * @return \Evence\Bundle\GridBundle\Grid\Filter\FilterObject
      */
     private function set($property, $value){
        
         $this->data[$property] = $value;
         return $this;
     }
     
     /**
      * Get property
      * 
      * @param string $property Property name
      * @return mixed
      */
     private function get($property){
         if(!empty($this->data[$property]))
         return $this->data[$property];
     }
     
     /**
      * Magic function get
      * 
      * @param string $property
      * @return mixed
      */
     public function __get($property){
         return $this->get($property);
     }
     

     /**
      *  Magic function set
      * 
      * @param mixed $property
      * @param mixed $value
      * @return \Evence\Bundle\GridBundle\Grid\Filter\FilterObject
      */
     public function __set($property, $value){
         return $this->set($property, $value);
     }
 }