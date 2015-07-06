<?php
/*
 * Copyright (c) 2015 - Ruben Harms <postbus@rubenharms.nl>
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
class BooleanType extends AbstractType
{

    /*
     * (non-PHPdoc)
     * @see \Evence\Bundle\GridBundle\Grid\Type\AbstractType::renderType()
     */
    public function renderType($value, $source)
    {
        return ($value == $this->getOption('true_value') ? true : false);
    }

    /*
     * (non-PHPdoc)
     * @see \Evence\Bundle\GridBundle\Grid\Type\AbstractType::getName()
     */
    public function getName()
    {
        return 'boolean';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'true_value' => true,
            'false_value' => false,
            'label_yes' => 'Yes',
            'label_no' => 'No'
        ));
    }
}

