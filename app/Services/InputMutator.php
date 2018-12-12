<?php
/**
 * Author: Xavier Au
 * Date: 9/12/2018
 * Time: 6:14 PM
 */

namespace App\Services;


use Carbon\Carbon;

class InputMutator
{
    /**
     * @var array
     */
    private $inputs;


    /**
     * InputMutator constructor.
     * @param array $inputs
     */
    public function __construct(array $inputs) {
        $this->inputs = $inputs;
    }

    public function boolean($fields) {
        $fields = is_array($fields) ? $fields : [$fields];

        collect($fields)->each(function ($field) {
            $this->inputs[$field] = isset($this->inputs[$field]) ? $this->inputs[$field] : false;
        });

        return $this;
    }

    public function date($fields, string $format) {
        $fields = is_array($fields) ? $fields : [$fields];

        collect($fields)->each(function ($field) use ($format) {
            if ($this->inputs[$field]) {
                $this->inputs[$field] = Carbon::createFromFormat($format,
                    $this->inputs[$field]);
            }

        });

        return $this;
    }

    public function get(): array {
        return $this->inputs;
    }
}