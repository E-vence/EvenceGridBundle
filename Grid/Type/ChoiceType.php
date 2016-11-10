<?php
/*
Copyright (c) 2015 - Ruben Harms <postbus@rubenharms.nl>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:


The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

namespace Evence\Bundle\GridBundle\Grid\Type;


use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Field type class for Boolean
 *
 * @author Ruben Harms <info@rubenharms.nl>
 * @link http://www.rubenharms.nl
 * @link https://www.github.com/RubenHarms
 * @package evence/grid-bundle
 * @subpackage Type
 */
class ChoiceType extends AbstractType
{
    /* (non-PHPdoc)
     * @see \Evence\Bundle\GridBundle\Grid\Type\AbstractType::renderType()
     */
    public function renderType($value, $source)
    {

        $valArray = array();


        $choices = $this->getOption('choices');
        if (!$this->getOption('choices_as_values')) $choices = array_flip($choices);


        foreach ((array)$value as $key => $val) {

            if (array_search($val, $choices) !== false)
                $valArray[] = array_search($val, $choices);
        }


        //  if (empty($valArray) && $this->getOption('empty_data'))
        //    $valArray[] = $this->getOption('empty_data');

        return $valArray;
    }


    /* (non-PHPdoc)
     * @see \Evence\Bundle\GridBundle\Grid\Type\AbstractType::getName()
     */
    public function getName()
    {
        return 'choice';
    }

    public function choiceLabelCallback($source, $key, $index){
        return $key;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('separator' => ', ', 'bootstrap' => ['label_callback' => null], 'choices_as_values' => false, 'choice_label' => [$this, 'choiceLabelCallback'] /* 'empty_data' => ''*/));
        $resolver->setRequired('choices');
    }

    public function getChoiceLabel($source, $key, $val){
        $choiceLabel = call_user_func_array($this->getOption('choice_label'), [$source, (string) $key, $val]);
        return $choiceLabel;
    }

    public function getLabel($key)
    {
        $bootstrap = $this->getOption('bootstrap');
        if ($bootstrap['label_callback']) {
            return call_user_func_array($bootstrap['label_callback'], [$key]);
        }
    }
}

