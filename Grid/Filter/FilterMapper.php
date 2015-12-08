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

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;
use Doctrine\MongoDB\Query\Builder;

/**
 * Filter mapper class
 *
 * @author Ruben Harms <info@rubenharms.nl>
 * @link http://www.rubenharms.nl
 * @link https://www.github.com/RubenHarms
 * @package evence/grid-bundle
 * @subpackage Filter
 */
class FilterMapper
{

    /**
     * Constant: Filter type "equal"
     *
     * @var string
     */
    CONST FILTER_TYPE_EQ = 'eq';

    /**
     * Constant: Filter type "lesser than"
     *
     * @var string
     */
    CONST FILTER_TYPE_LT = 'lt';

    /**
     * Constant: Filter type "greater than"
     *
     * @var string
     */
    CONST FILTER_TYPE_GT = 'gt';

    /**
     * Constant: Filter type "like"
     *
     * @var string
     */
    CONST FILTER_TYPE_LIKE = 'like';

    /**
     * Constant: Filter type "not like"
     *
     * @var string
     */
    CONST FILTER_TYPE_NOTLIKE = 'notlike';

    /**
     * Constant: Filter type "is null"
     *
     * @var string
     */
    CONST FILTER_TYPE_ISNULL = 'null';

    /**
     * Constant: Filter type "is not null"
     *
     * @var string
     */
    CONST FILTER_TYPE_NOTNULL = 'notnull';

    /**
     * Constant: Filter type "between"
     *
     * @var string
     */
    CONST FILTER_TYPE_BETWEEN = 'between';

    /**
     * ORM identifier
     *
     * @var string
     */
    private $id;

    /**
     * FormInterface name
     *
     * @var string
     */
    private $field;

    /**
     * Type of the expression
     *
     * @var string
     */
    private $type;

    /**
     * Array of arguments
     *
     * @var array
     */
    private $args;

    /**
     * Class constructor
     *
     * @param string $id
     *            Doctrine ORM column identifier
     * @param string $field
     *            FormInterface name
     * @param string $type
     *            Type of the expression
     * @param unknown $args
     *            Array of arguments
     */
    public function __construct($id, $field, $type, $args = [])
    {
        $this->id = $id;
        $this->field = $field;
        $this->type = $type;
        $this->args = $args;
    }

    /**
     * Creates an 'equal' filter mapper
     *
     * @param string $id
     *            Doctrine ORM column identifier
     * @param string $field
     *            FormInterface name
     * @return \Evence\Bundle\GridBundle\Grid\Filter\FilterMapper
     */
    static function eq($id, $field)
    {
        return new FilterMapper($id, $field, self::FILTER_TYPE_EQ);
    }

    /**
     * Creates a 'greater than' filter mapper
     *
     * @param string $id
     *            Doctrine ORM column identifier
     * @param string $field
     *            FormInterface name
     * @return \Evence\Bundle\GridBundle\Grid\Filter\FilterMapper
     */
    static function gt($id, $field)
    {
        return new FilterMapper($id, $field, self::FILTER_TYPE_GT);
    }

    /**
     * Creates a 'lesser than' filter mapper
     *
     * @param string $id
     *            Doctrine ORM column identifier
     * @param string $field
     *            FormInterface name
     * @return \Evence\Bundle\GridBundle\Grid\Filter\FilterMapper
     */
    static function lt($id, $field)
    {
        return new FilterMapper($id, $field, self::FILTER_TYPE_LT);
    }

    /**
     * Creates a 'like' filter mapper
     *
     * @param string $id
     *            Doctrine ORM column identifier
     * @param string $field
     *            FormInterface name
     * @param bool $strict
     *            Whether or not to use strict like or just include wildcards
     * @return \Evence\Bundle\GridBundle\Grid\Filter\FilterMapper
     */
    static function like($id, $field, $strict = false)
    {
        return new FilterMapper($id, $field, self::FILTER_TYPE_LIKE, [
            'strict' => $strict
        ]);
    }

    /**
     * Creates a 'not like' filter mapper
     *
     * @param string $id
     *            Doctrine ORM column identifier
     * @param string $field
     *            FormInterface name
     * @param bool $strict
     *            Whether or not to use strict like or just include wildcards
     * @return \Evence\Bundle\GridBundle\Grid\Filter\FilterMapper
     */
    static function notLike($id, $field, $strict = false)
    {
        return new FilterMapper($id, $field, self::FILTER_TYPE_NOTLIKE, [
            'strict' => $strict
        ]);
    }

    /**
     * Creates an 'is null' filter mapper
     *
     * @param string $id
     *            Doctrine ORM column identifier
     * @param string $field
     *            FormInterface name
     * @return \Evence\Bundle\GridBundle\Grid\Filter\FilterMapper
     */
    static function null($id, $field)
    {
        return new FilterMapper($id, $field, self::FILTER_TYPE_ISNULL, []);
    }

    /**
     * Creates an 'is not null' filter mapper
     *
     * @param string $id
     *            Doctrine ORM column identifier
     * @param string $field
     *            FormInterface name
     * @return \Evence\Bundle\GridBundle\Grid\Filter\FilterMapper
     */
    static function notNull($id, $field)
    {
        return new FilterMapper($id, $field, self::FILTER_TYPE_NOTNULL, []);
    }

    /**
     * Creates a 'between' filter mapper
     *
     * @param string $id
     *            Doctrine ORM column identifier
     * @param string $field
     *            FormInterface name
     * @param string $secondField            
     * @return \Evence\Bundle\GridBundle\Grid\Filter\FilterMapper
     */
    static function between($id, $field, $secondField)
    {
        return new FilterMapper($id, $field, self::FILTER_TYPE_BETWEEN, [
            'secondField' => $secondField
        ]);
    }

    /**
     *
     * @param QueryBuilder $qb            
     * @param FormInterface $form            
     * @param string $prefix            
     */
    public function filterQuery($qb, FormInterface $form, $prefix = 'e')
    {
        $document = false;
        
        if ($qb instanceof Builder)
            $document = true;
        
        if (! strstr($this->id, ".") && ! $document)
            $id = $prefix . '.' . $this->id;
        else
            $id = $this->id;
        
        $data = $this->getData($form);
        
        if (! $data)
            return;
        
        if ($data instanceof \DateTime) {
            $data = $data->format('Y-m-d H:i');
        }
        $param = $this->field;
        switch ($this->type) {
            case self::FILTER_TYPE_EQ:
                if ($document)
                    $qb->field($id)->equals($data);
                else
                    $qb->andWhere($qb->expr()
                        ->eq($id, ':' . $param))
                        ->setParameter($param, $data);
                
                break;
            case self::FILTER_TYPE_LT:
                if ($document)
                    $qb->field($id)->lt($data);
                else
                    $qb->andWhere($qb->expr()
                        ->lt($id, ':' . $param))
                        ->setParameter($param, $data);
                break;
            case self::FILTER_TYPE_GT:
                if ($document)
                    $qb->field($id)->gt($data);
                else
                    $qb->andWhere($qb->expr()
                        ->gt($id, ':' . $param))
                        ->setParameter($param, $data);
                break;
            case self::FILTER_TYPE_LIKE:
                if ($document)
                    throw \Exception("Method not support by Document");
                else
                    $qb->andWhere($qb->expr()
                        ->like($id, ':' . $param))
                        ->setParameter($param, ($this->args['strict'] ? $data : '%' . $data . '%'));
                break;
            case self::FILTER_TYPE_NOTLIKE:
                if ($document)
                    throw \Exception("Method not support by Document");
                else
                    $qb->andWhere($qb->expr()
                        ->notLike($id, ':' . $param))
                        ->setParameter($param, ($this->args['strict'] ? $data : '%' . $data . '%'));
                break;
            case self::FILTER_TYPE_ISNULL:
                if ($qb instanceof Builder)
                    $qb->field($id)->equals('');
                else
                    $qb->andWhere($qb->expr()
                        ->isNull($id));
                break;
            case self::FILTER_TYPE_NOTNULL:
                if ($qb instanceof Builder)
                    $qb->field($id)->notEqual('');
                $qb->andWhere($qb->expr()
                    ->isNotNull($id));
                break;
            case self::FILTER_TYPE_BETWEEN:
                // $qb->andWhere($qb->expr()->($id));
                break;
        }
    }

    /**
     * Get data of the current field or the selected field
     *
     * @param FormInterface $form
     *            Form
     * @param string $field
     *            FormInterface name
     * @return \Symfony\Component\Form\mixed
     */
    public function getData(FormInterface $form, $field = null)
    {
        if (! $field)
            return $form->get($this->field)->getData();
        else
            return $form->get($field)->getData();
    }

    /**
     * Get Id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get field
     *
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }
}
 
 