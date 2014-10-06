<?php

namespace Idma\Authority;

/**
 * RuleAlias allows a single action alias to represent a list of many actions
 *
 * @package Idma\Authority
 */
class RuleAlias
{
    /**
     * @type string Alias representing a list of actions
     */
    protected $alias;

    /**
     * @type array Array of actions represented by the alias
     */
    protected $actions;

    /**
     * RuleAlias constructor.
     *
     * @param string $alias   Alias for a set of actions
     * @param array  $actions Array of actions that $alias represents
     */
    public function __construct($alias, $actions)
    {
        $this->alias = $alias;
        $this->actions = (array) $actions;
    }

    /**
     * Determine if the alias represents the given action.
     *
     * @param string $action Action in question
     *
     * @return boolean
     */
    public function includes($action)
    {
        return in_array($action, $this->actions);
    }
}

