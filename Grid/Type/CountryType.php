<?php
/**
 * Copyright Ruben Harms 2016
 *
 * Do not use, modify, sell and/or duplicate this script without any permissions!
 *
 *  This software is written and recorded by Ruben Harms!  Ruben Harms took all
 *  the necessary actions, juridical and  (hidden) technical, to protect his script
 *  against any use without permission, any modify and against any unauthorized
 *  duplicate.
 *
 *  Copied versions shall be recognized and compared with the recorded version.
 *  The owner of this softare will take all legal steps against every kind of malpractice!
 *
 */

namespace Evence\Bundle\GridBundle\Grid\Type;


use Symfony\Component\Intl\Intl;
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
class CountryType extends ChoiceType
{
    /* (non-PHPdoc)
     * @see \Evence\Bundle\GridBundle\Grid\Type\AbstractType::getName()
     */
    public function getName()
    {
        return 'choice';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('choices', Intl::getRegionBundle()->getCountryNames());
    }
}

