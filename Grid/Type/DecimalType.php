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
namespace Evence\Bundle\GridBundle\Grid\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Decimal Type
 *
 * @author Ruben Harms <info@rubenharms.nl>
 * @link http://www.rubenharms.nl
 * @link https://www.github.com/RubenHarms
 * @package evence/grid-bundle
 * @subpackage Type
 */
class DecimalType extends AbstractType
{

    /*
     * (non-PHPdoc)
     * @see \Evence\Bundle\GridBundle\Grid\Type\AbstractType::renderType()
     */
    public function renderType($value, $source)
    {
        if ($this->getOption('format')) {
            
            $fmt = new \NumberFormatter($this->getOption('locale'), \NumberFormatter::DECIMAL);
            if ($this->getOption('thousand_separator'))
                $fmt->setSymbol(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL, $this->getOption('thousand_separator'));
            if ($this->getOption('decimal_point'))
                $fmt->setSymbol(\NumberFormatter::DECIMAL_SEPARATOR_SYMBOL, $this->getOption('decimal_point'));  

            if ($this->getOption('min_decimal') !== false)
                $fmt->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $this->getOption('min_decimal'));
            
            if ($this->getOption('max_decimal') !== false)
                $fmt->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $this->getOption('max_decimal'));
                
            
            $value = $fmt->format($value);
        }
        
        return $this->getOption('prepend') . $value . $this->getOption('append');
    }

    /*
     * (non-PHPdoc)
     * @see \Evence\Bundle\GridBundle\Grid\Type\AbstractType::getName()
     */
    public function getName()
    {
        return 'decimal';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'prepend' => '',
            'append' => '',
            'format' => false,
            'min_decimal' => false,
            'max_decimal' => false,
            'locale' => locale_get_default(),
            'decimal_point' => false,
            'thousand_separator' => false
        ));
    }
}

