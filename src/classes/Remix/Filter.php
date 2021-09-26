<?php

namespace Remix;

/**
 * Remix Filter : definition of a input item from the POST form
 *
 * @package  Remix\Web\Form
 * @see \Remix\Synthesizer
 * @see \Remix\Oscillator
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
     * Input oscillators
     * @var array<int, \Remix\Oscillator>
     */
    protected $oscillators = [];

    protected const OSCILLATORS = [
        'required' => \Remix\Oscillators\Required::class,
        'max' => \Remix\Oscillators\Max::class,
        'email' => \Remix\Oscillators\Email::class,
    ];

    private function __construct(string $key, string $label)
    {
        $this->log_param = $key;
        parent::__construct();

        $this->key = $key;
        $this->label = $label;
    }
    // function __construct()

    /**
     * Define a field
     *
     * @param  string      $key    Key of item
     * @param  string|null $label  Label of item
     * @return self                Instance which was constructed
     */
    public static function define(string $key, string $label = null): self
    {
        return new static($key, $label ?: $key);
    }

    /**
     * Append oscillators
     *
     * Multiple oscillators can be made by connecting with '|'
     *
     * @param  string|\Remix\Oscillator|array<int, \Remix\Oscillator> $obj  oscillator definition, or oscillator object
     * @return self  instance of itself
     */
    public function rules($obj): self
    {
        if (is_string($obj)) {
            foreach (explode('|', $obj) as $rule) {
                $this->oscillators[] = $this->oscillateFromString($rule);
            }
        } elseif ($obj instanceof Oscillator) {
            $this->oscillators[] = $obj;
        } elseif (is_array($obj)) {
            foreach ($obj as $rule) {
                $this->rules($rule);
            }
        }
        return $this;
    }

    /**
     * Append a oscillator from string
     * @param  string $expression  oscillator definition
     * @return Oscillator          generated Oscillator instance
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
     * Run all oscillators
     *
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