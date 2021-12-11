<?php

namespace Remix;

/**
 * Remix Filter : definition of a input item from the POST form.
 *
 * @package  Remix\Web\Form
 * @see \Remix\Synthesizer
 * @see \Remix\Oscillator
 * @todo Write the details.
 */
class Filter extends Gear
{
    /**
     * Key of the input field.
     * @var string
     */
    protected $key = '';

    /**
     * Label of the input field.
     * @var string
     */
    protected $label = '';

    /**
     * Oscillators of the input field.
     * @var array<int, \Remix\Oscillator>
     */
    protected $oscillators = [];

    /**
     * Predefined oscillator class names.
     * @var array<string, string>
     */
    protected const OSCILLATORS = [
        'required' => \Remix\Oscillators\Required::class,
        'max' => \Remix\Oscillators\Max::class,
        'email' => \Remix\Oscillators\Email::class,
    ];

    /**
     * Set the name and label of the input field.
     * @param string $key    Key of the input field
     * @param string $label  Label of the input field
     */
    private function __construct(string $key, string $label)
    {
        parent::__construct($key);

        $this->key = $key;
        $this->label = $label;
    }
    // function __construct()

    /**
     * Define a field.
     * @param  string      $key    Key of the input field
     * @param  string|null $label  Label of the input field
     * @return self                Constructed instance
     */
    public static function define(string $key, string $label = null): self
    {
        return new static($key, $label ?: $key);
    }

    /**
     * Append oscillators.
     *
     * @param  \Remix\Oscillator|string|array<int, \Remix\Oscillator> $def
     *               Oscillator definition, allow the following :
     *                   * Oscillator instance
     *                   * string of Oscillator name
     *                   * array of Oscillator
     * @return self  Instance of itself
     */
    public function rules($def): self
    {
        if (is_string($def)) {
            foreach (explode('|', $def) as $rule) {
                $this->oscillators[] = $this->oscillateFromString($rule);
            }
        } elseif ($def instanceof Oscillator) {
            $this->oscillators[] = $def;
        } elseif (is_array($def)) {
            foreach ($def as $rule) {
                $this->rules($rule);
            }
        }
        return $this;
    }

    /**
     * Append a oscillator from string.
     * @param  string $expression  Oscillator definition, includes optional parameters
     * @return Oscillator          Generated Oscillator instance
     */
    protected function oscillateFromString(string $expression): Oscillator
    {
        if (strpos($expression, ':') !== false) {
            list($name, $option) = explode(':', $expression, 2);
        } else {
            $name = $expression;
            $option = null;
        }
        if (! isset(static::OSCILLATORS[$name])) {
            throw new RemixException("unknown rule '{$name}'");
        }
        $class = static::OSCILLATORS[$name];
        return new $class($option);
    }

    /**
     * Key of the item.
     * @return string  key
     */
    public function key(): string
    {
        return $this->key;
    }

    /**
     * Label of the item.
     * @return string  label
     */
    public function label(): string
    {
        return $this->label;
    }

    /**
     * Run all oscillators.
     * @param  string $value  input value
     * @return string         error message or empty string
     */
    public function run(string $value): string
    {
        foreach ($this->oscillators as $oscillator) {
            if (! $oscillator->run($value)) {
                $search = ['{key}', '{label}', '{value}'];
                $replace = [$this->key, $this->label, $value];
                return str_replace($search, $replace, $oscillator->error());
            }
        }
        return '';
    }
}
