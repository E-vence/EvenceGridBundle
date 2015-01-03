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
 * Field type class for Date
 *
 * @author Ruben Harms <info@rubenharms.nl>
 * @link http://www.rubenharms.nl
 * @link https://www.github.com/RubenHarms
 * @package evence/grid-bundle
 * @subpackage Type
 */
class DateTimeType extends AbstractType
{

    /*
     * (non-PHPdoc)
     * @see \Evence\Bundle\GridBundle\Grid\Type\AbstractType::renderType()
     */
    public function renderType($value, $source)
    {
        $date = '';
        $time = '';
        
        if ($value instanceof \DateTime) {
            if ($this->getOption('date_format'))
                $date = $value->format($this->getOption('date_format'));
            if ($this->getOption('time_format'))
                $time = $value->format($this->getOption('time_format'));
        } else {
            if (! is_numeric($value))
                $value = strtotime($value);
            if ($this->getOption('date_format'))
                $date = date($this->getOption('date_format'), $value);
            if ($this->getOption('time_format'))
                $time = date($this->getOption('time_format'), $value);
        }        

        return sprintf($this->getOption('text'), $date, $time);
    }

    /*
     * (non-PHPdoc)
     * @see \Evence\Bundle\GridBundle\Grid\Type\AbstractType::getName()
     */
    public function getName()
    {
        return 'datetime';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'date_format' => 'm/d/Y',
            'time_format' => 'h:i a',
            'text' => '%s %s'
        ));
    }
}

