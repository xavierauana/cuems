<?php
/**
 * Author: Xavier Au
 * Date: 2/12/2018
 * Time: 6:39 PM
 */

namespace App\Services;


use App\Event;
use Illuminate\Support\Collection;

class DelegateDuplicateChecker
{
    /**
     * @var \App\Event
     */
    private $event;

    /**
     * DelegateDuplicateChecker constructor.
     */
    public function __construct(Event $event) {

        $this->event = $event;
    }

    public function find($field, $value): Collection {
        $predicates = $this->fetchPredicates($field, $value);

        return $this->event->delegates()->where($predicates)->get();
    }

    public function isDuplicated($field, $value): bool {
        $predicates = $this->fetchPredicates($field, $value);

        return $this->event->delegates()->where($predicates)->count() > 1;
    }

    /**
     * @param $field
     * @param $value
     * @return array
     */
    private function createPredicates(array $field, array $value): array {
        $predicates = [];

        foreach ($field as $index => $key) {

            if (isset($value[$index])) {
                $predicates[] = [$key, '=', $value[$index]];
            }
        }

        return $predicates;
    }

    /**
     * @param $field
     * @param $value
     */
    private function fetchPredicates($field, $value): ?array {
        $predicates = ((is_array($field) and is_array($value))) ?
            $this->createPredicates($field, $value) : [[$field, '=', $value]];

        if (empty($predicates)) {
            throw new \InvalidArgumentException("field and value is not properly set");
        }

        return $predicates;
    }
}