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
namespace Evence\Bundle\GridBundle\Grid\Event;

use Evence\Bundle\GridBundle\Grid\Grid;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\EventDispatcher\Event;

class GridEvent extends Event
{

    /*
     * const PRE_SET_ACTIONS = 'grid.configure.actions.set.pre';
     * const POST_SET_ACTIONS = 'grid.configure.actions.set.pre';
     *
     * const PRE_SET_FILTERS = 'grid.configure.actions.set.pre';
     * const POST_SET_ACTIONS = 'grid.configure.actions.set.pre';
     */
    const PRE_RENDER = 'grid.event.render.pre';

    const POST_RENDER = 'grid.event.render.post';

    const PRE_CONFIGURE = 'grid.event.configure.pre';

    const POST_CONFIGURE = 'grid.event.configure.post';

    const POST_SET_QUERY = 'grid.event.set.query.post';

    const POST_SET_COUNT_QUERY = 'grid.event.set.query.post';
    
    const PRE_MODIFY_ARRAY = 'grid.event.modify.array.pre';

    /**
     *
     * @var Grid
     */
    private $grid;

    /**
     *
     * @var QueryBuilder
     */
    private $querybuilder;
    
    /**
     *
     * @var array
     */
    private $data;

    public function getGrid()
    {
        return $this->grid;
    }

    public function setGrid(Grid $grid)
    {
        $this->grid = $grid;
        return $this;
    }

    public function getQuerybuilder()
    {
        return $this->querybuilder;
    }

    public function setQuerybuilder(QueryBuilder $querybuilder)
    {
        $this->querybuilder = $querybuilder;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }
 
}