<?php
/**
 * Author: Xavier Au
 * Date: 2/12/2018
 * Time: 6:39 PM
 */

namespace App\Services;


use App\Contracts\DuplicateCheckerInterface;
use App\Event;
use Illuminate\Support\Collection;

class DelegateDuplicateChecker implements DuplicateCheckerInterface
{
    /**
     * @var \App\Event
     */
    private $event;

    public function find($field, $value): Collection {
        $predicates = $this->fetchPredicates($field, $value);

        return $this->event->delegates()
                           ->where($predicates)
                           ->orderBy('created_at', 'asc')
                           ->get();
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
     * @return array|null
     */
    private function fetchPredicates($field, $value): ?array {
        $predicates = ((is_array($field) and is_array($value))) ?
            $this->createPredicates($field, $value) : [[$field, '=', $value]];

        if (empty($predicates)) {
            throw new \InvalidArgumentException("field and value is not properly set");
        }

        return $predicates;
    }

    public function setEvent(Event $event): DuplicateCheckerInterface {
        $this->event = $event;

        return $this;
    }

    public function convertRegistrationIdToInt(string $registration_id): int {
        $newDuplicatedId = ltrim(str_replace(setting($this->event,
                'registration_id_prefix') ?? "",
            "", $registration_id), "0");

        return $newDuplicatedId;

    }

    public function convertRegistrationIdToString(int $registration_id
    ): int {

        $prefix = setting($this->event, 'registration_id_prefix') ?? "";

        return $prefix . str_pad($registration_id, 4, 0,
                STR_PAD_LEFT);

    }
}