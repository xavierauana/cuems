<?php
/**
 * Author: Xavier Au
 * Date: 2018-12-28
 * Time: 09:41
 */

namespace App\Services;


use App\Delegate;
use App\DelegateRole;
use App\Enums\DelegateDuplicationStatus;
use App\Event;
use App\User;
use Illuminate\Support\Facades\DB;

class DelegateCreationService
{
    /**
     * @var \App\Services\Role
     */
    private $role;

    private $user;

    /**
     * DelegateCreationService constructor.
     * @param \App\DelegateRole $role
     * @param \App\User|null    $user
     */
    public function __construct(DelegateRole $role, User $user = null) {
        $this->role = $role;
        $this->user = $user ?? request()->user();
    }

    /**
     * @param \App\Event $event
     * @param array      $data
     * @param bool       $checkDuplicate
     * @return \App\Delegate
     * @throws \Exception
     */
    public function create(
        Event $event, array $data, bool $checkDuplicate = false
    ): Delegate {
        DB::beginTransaction();

        try {
            $data['registration_id'] = ($event->delegates()
                                              ->max('registration_id') ?? 0) + 1;

            /** @var \App\Delegate $newDelegate */
            $newDelegate = $event->delegates()->create($data);

            $role = isset($data['role']) ?
                $this->role->where('code', $data['role'])->first() :
                $this->role->whereIsDefault(true)->firstOrFail();

            $newDelegate->roles()->save($role);

            $newDelegate->transactions()->create($data);

            $newDelegate->transactions()->create($data);

            if (isset($data['sponsor']['sponsor_id'])) {
                $sponsorData = $data['sponsor'];

                $newDelegate->sponsorRecord()->create($sponsorData);
            }

            DB::table('delegate_creations')->insert([
                'delegate_id' => $newDelegate->id,
                'user_id'     => $this->user->id
            ]);

            if ($checkDuplicate) {
                $this->checkDuplicated($event, $newDelegate);
            }
            DB::commit();

            return $newDelegate;

        } catch (\Exception $exception) {

            DB::rollBack();

            throw $exception;

        }
    }

    public function checkDuplicated(Event $event, Delegate $newDelegate): void {
        $checker = new DelegateDuplicateChecker($event);

        if ($checker->isDuplicated('email', $newDelegate->email) or
            $checker->isDuplicated('mobile', $newDelegate->mobile)) {
            $newDelegate->is_duplicated = DelegateDuplicationStatus::DUPLICATED;
        } else {
            $newDelegate->is_duplicated = DelegateDuplicationStatus::NO;
        }

        $newDelegate->is_verified = true;

        $newDelegate->save();
    }
}