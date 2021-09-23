<?php

namespace Remix;

use Remix\Filter;
use Utility\Hash\ReadOnlyHash;
use Utility\Http\PostHash;

/**
 * Remix Synthesizer : input form validation
 *
 * @package  Remix\Web
 * @todo Write the details.
 */
abstract class Synthesizer extends Gear
{
    /**
     * input from $_POST
     * @var \Utility\Hash\ReadOnlyHash
     */
    protected $input = [];

    /**
     * errors
     * @var \Utility\Hash\ReadOnlyHash
     */
    protected $errors = null;

    /**
     * filters definition
     * @return array<int, \Remix\Filter>
     */
    abstract protected function filters(): array;

    /**
     * Run validation
     * @return self  instance of itself
     */
    public function run(): self
    {
        $post = PostHash::factory();
        $input = [];
        $errors = [];

        foreach ($this->filters() as $filter) {
            $key = $filter->key();
            $value = (string)$post->get($key);
            $input[$key] = $value;

            $error = $filter->run($value);
            if ($error !== '') {
                $errors[$key] = $error;
            }
        }

        if (count($errors) !== 0) {
            $errors[0] = 'some errors';
        }

        $this->input = new ReadOnlyHash($input);
        $this->errors = new ReadOnlyHash($errors);
        return $this;
    }

    /**
     * Inputs from form
     * @return \Utility\Hash\ReadOnlyHash  Hash of input params
     */
    public function input(): ReadOnlyHash
    {
        return $this->input;
    }

    /**
     * Validation errors
     * @return \Utility\Hash\ReadOnlyHash  Hash of errors
     */
    public function errors(): ReadOnlyHash
    {
        return $this->errors;
    }
}
