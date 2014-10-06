<?php

namespace Idma\Authority;

use ArrayIterator;
use Closure;

/**
 * RuleRepository collections contain and interact with Rule instances
 *
 * @package Idma\Authority
 */
class RuleRepository implements \Countable, \ArrayAccess, \IteratorAggregate
{
    /**
     * @type array Internal container for the rules
     */
    protected $rules;

    /**
     * RuleRepository constructor.
     *
     * @param array $rules Initial list of rules for the collection
     */
    public function __construct(array $rules = [])
    {
        $this->rules = $rules;
    }

    /**
     * Add a rule to the collection.
     *
     * @param Rule $rule
     *
     * @return void
     */
    public function add(Rule $rule)
    {
        $this->rules[] = $rule;
    }

    /**
     * Runs a reduce callback on the collection.
     *
     * @param Closure $callback     Callback to use for the reduce algorithm
     * @param mixed   $initialValue Initial value for the reduce set
     *
     * @return RuleRepository
     */
    public function reduce(Closure $callback, $initialValue = [])
    {
        $rules = array_reduce($this->rules, $callback, $initialValue);
        return new static($rules);
    }

    /**
     * Get all rules only relevant to the given action and resource.
     *
     * @param string $action   Action to check against
     * @param string $resource Resource to check against
     *
     * @return RuleRepository
     */
    public function getRelevantRules($action, $resource)
    {
        $rules = array_reduce($this->rules, function($rules, $currentRule) use ($action, $resource) {
            /** @type Rule $currentRule */
            if ($currentRule->isRelevant($action, $resource)) {
                $rules[] = $currentRule;
            }

            return $rules;
        }, []);

        return new static($rules);
    }

    /**
     * Return the first element in the array or null if empty.
     *
     * @return Rule|null
     */
    public function first()
    {
        return count($this->rules) > 0 ? reset($this->rules) : null;
    }

    /**
     * Return the last element in the array or null if empty.
     *
     * @return Rule|null
     */
    public function last()
    {
        return count($this->rules) > 0 ? end($this->rules) : null;
    }

    /**
     * Return a raw array of all rules.
     *
     * @return array
     */
    public function all()
    {
        return $this->rules;
    }

    /**
     * Determine if rules is empty.
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->rules);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->rules);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->rules);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->rules);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($key)
    {
        return $this->rules[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($key, $value)
    {
        $this->rules[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($key)
    {
        unset($this->rules[$key]);
    }
}
