<?php

namespace Remix;

/**
 * Remix Filter : definition of a input item from form
 *
 * @package  Remix\Web
 * @todo Write the details.
 */
class Filter extends Gear
{
    /**
     * Key of item
     * @var string
     */
    protected $key = '';

    /**
     * Label of item
     * @var string
     */
    protected $label = '';

    /**
     * Input rules
     * @var array<int, string>
     */
    protected $rules = [];

    protected function __construct(string $key, string $label)
    {
        Delay::logBirth(static::class . ' [' . $key . ']');

        $this->key = $key;
        $this->label = $label ?: $key;
    }
    // function __construct()

    public function __destruct()
    {
        Delay::logDeath(static::class . ' [' . $this->key . ']');
    }
    // function __destruct()

    /**
     * Append rules
     *
     * Multiple settings can be made by connecting with '|'
     *
     * @param  string $rules  rule definition
     * @return self           instance of itself
     */
    public function rules(string $rules): self
    {
        $arr = explode('|', $rules);
        $this->rules = array_merge($this->rules, $arr);
        return $this;
    }

    /**
     * Key of the item
     *
     * @return string  key
     */
    public function key(): string
    {
        return $this->key;
    }

    /**
     * Label of the item
     *
     * @return string  label
     */
    public function label(): string
    {
        return $this->label;
    }

    /**
     * Run all rules
     *
     * @param  string $value  input value
     * @return string         error message or empty string
     */
    public function run(string $value): string
    {
        foreach ($this->rules as $rule) {
            $error = $this->runRule($rule, $value);
            if ($error !== '') {
                $error = str_replace('{label}', $this->label, $error);
                return $error;
            }
        }
        return '';
    }

    /**
     * Run the rule
     *
     * @param  string $rule    rule name
     * @param  string $value   input value
     * @return string          error message or empty string
     * @throws RemixException  undefined rule
     */
    protected function runRule(string $rule, string $value): string
    {
        if (strpos($rule, ':') !== false) {
            list($name, $option) = explode(':', $rule, 2);
        } else {
            $name = $rule;
            $option = null;
        }

        switch ($name) {
            case 'required':
                if ($value === null || $value === '') {
                    return "{label} is required";
                }
                break;

            case 'max':
                if (strlen($value) > (int)$option) {
                    return "{label} must be {$option} characters or less";
                }
                break;

            case 'email':
                if (! preg_match('/[0-9A-Za-z_\-\.]+@[0-9A-Za-z_\-\.]+\.[a-z]+/', $value)) {
                    return "{label} is invalid email address";
                }
                break;

            default:
                throw new RemixException("unknown rule '{$name}'");
                break;
        }
        return '';
    }
}
