<?php
/**
 * Author: Xavier Au
 * Date: 2019-01-09
 * Time: 10:25
 */

namespace App\Contracts;


use App\Event;
use Illuminate\Support\Collection;

interface DuplicateCheckerInterface
{

    public function find($field, $value): Collection;

    public function isDuplicated($field, $value): bool;

    public function setEvent(Event $event): DuplicateCheckerInterface;
}