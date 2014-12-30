<?php
    
    namespace Evence\Bundle\GridBundle\Grid\Fields;

    class DataField extends Field {
        
        public function getData($source = null){
            if(!$source){
                throw new \Exception('Field must have a source');
            }            
            $value =  $this->getDataFromSource($source);
                
            return $value;
        }              
        
        public function getDataFromSource($source){         
            if(property_exists( $source, $this->identifier)){
                $method = 'get' . ucfirst($this->identifier);                       
            }
            else {
                throw new \Exception('Uknown field '. $this->identifier . ' in datasource ' . $this->configurator->getGrid()->getEntityName());
            }      
                    
            return $source->$method();            
           
        }
    }
    