<?php

namespace Idma\Authority;

class Authority
{
    /**
     * @type mixed Current user in the application for rules to apply to
     */
    protected $currentUser;

    /**
     * @type RuleRepository Collection of rules
     */
    protected $rules;

    /**
     * @type array List of aliases for groups of actions
     */
    protected $aliases = [];

    public function __construct($user)
    {
        $this->rules = new RuleRepository();
        $this->setCurrentUser($user);
    }

    /**
     * Determine if current user can access the given action and resource.
     *
     * @param string $action
     * @param mixed  $resource
     * @param null   $resourceValue
     *
     * @return boolean
     */
    public function can($action, $resource, $resourceValue = null)
    {
        if (!is_string($resource)) {
            $resourceValue = $resource;
            $resource      = get_class($resourceValue);
        }

        $rules = $this->getRulesFor($action, $resource);

        if (!$rules->isEmpty()) {
            $allowed = true;

            /** @type Rule $rule */
            foreach($rules->all() as $rule) {
                $allowed = $rule->isAllowed($this, $resourceValue);
            }
        } else {
            $allowed = false;
        }

        return $allowed;
    }

    /**
     * Determine if current user cannot access the given action and resource. This is negation of can().
     *
     * @param string $action
     * @param mixed  $resource
     * @param mixed  $resourceValue
     *
     * @return boolean
     */
    public function cannot($action, $resource, $resourceValue = null)
    {
        return !$this->can($action, $resource, $resourceValue);
    }

    /**
     * Define privilege for a given action and resource.
     *
     * @param string        $action    Action for the rule
     * @param mixed         $resource  Resource for the rule
     * @param \Closure|null $condition Optional condition for the rule
     *
     * @return Rule
     */
    public function allow($action, $resource, $condition = null)
    {
        return $this->addRule(true, $action, $resource, $condition);
    }

    /**
     * Define restriction for a given action and resource.
     *
     * @param string        $action    Action for the rule
     * @param mixed         $resource  Resource for the rule
     * @param \Closure|null $condition Optional condition for the rule
     *
     * @return Rule
     */
    public function deny($action, $resource, $condition = null)
    {
        return $this->addRule(false, $action, $resource, $condition);
    }

    /**
     * Define rule for a given action and resource
     *
     * @param boolean       $allow     True if privilege, false if restriction
     * @param string        $action    Action for the rule
     * @param mixed         $resource  Resource for the rule
     * @param \Closure|null $condition Optional condition for the rule
     *
     * @return Rule
     */
    public function addRule($allow, $action, $resource, $condition = null)
    {
        $rule = new Rule($allow, $action, $resource, $condition);
        $this->rules->add($rule);

        return $rule;
    }

    /**
     * Define new alias for an action
     *
     * @param string $name    Name of action
     * @param array  $actions Actions that $name aliases
     *
     * @return RuleAlias
     */
    public function addAlias($name, $actions)
    {
        $alias                = new RuleAlias($name, $actions);
        $this->aliases[$name] = $alias;

        return $alias;
    }

    /**
     * Returns all rules relevant to the given action and resource.
     *
     * @param $action
     * @param $resource
     *
     * @return RuleRepository
     */
    public function getRulesFor($action, $resource)
    {
        $aliases = $this->getAliasesForAction($action);

        return $this->rules->getRelevantRules($aliases, $resource);
    }

    /**
     * Returns the current rule set.
     *
     * @return RuleRepository
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Returns all actions a given action applies to.
     *
     * @param $action
     *
     * @return array
     */
    public function getAliasesForAction($action)
    {
        $actions = [$action];
        /** @type RuleAlias $alias */
        foreach ($this->aliases as $key => $alias) {
            if ($alias->includes($action)) {
                $actions[] = $key;
            }
        }

        return $actions;
    }

    /**
     * Returns all aliases.
     *
     * @return array
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * Returns a RuleAlias for a given action name.
     *
     * @param $name
     *
     * @return RuleAlias|null
     */
    public function getAlias($name)
    {
        return $this->aliases[$name];
    }

    /**
     * Set current user.
     *
     * @param mixed $currentUser Current user in the application
     */
    public function setCurrentUser($currentUser)
    {
        $this->currentUser = $currentUser;
    }

    /**
     * Returns current user.
     *
     * @return mixed
     */
    public function getCurrentUser()
    {
        return $this->currentUser;
    }

    /**
     * Returns current user - alias of getCurrentUser().
     *
     * @return mixed
     */
    public function user()
    {
        return $this->getCurrentUser();
    }
}
