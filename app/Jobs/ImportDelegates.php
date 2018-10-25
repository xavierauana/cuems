<?php

namespace App\Jobs;

use App\Delegate;
use App\DelegateRole;
use App\Event;
use App\Transaction;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ImportDelegates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var \App\Event
     */
    private $event;
    /**
     * @var string
     */
    private $filePath;
    /**
     * @var \App\User
     */
    private $user;

    /**
     * Create a new job instance.
     *
     * @param \App\Event $event
     * @param string     $filePath
     * @param \App\User  $user
     */
    public function __construct(Event $event, string $filePath, User $user) {
        //
        $this->event = $event;
        $this->filePath = $filePath;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @param \App\Transaction $transaction
     * @return void
     * @throws \ReflectionException
     */
    public function handle(
        Transaction $transaction, Delegate $delegate, DelegateRole $role
    ) {

        $batchId = str_random();

        $rules = $this->getValidationRules($transaction, $delegate, $role);

        foreach ($this->getData() as $record) {

            if ($validatedData = $this->validateData($record, $rules)) {

                DB::beginTransaction();

                try {
                    $newDelegate = $this->event->delegates()
                                               ->create($validatedData);

                    $newDelegate->roles()->save($role->where('code', '=',
                        $validatedData['role'])->first());

                    $newDelegate->transactions()->create($validatedData);

                    DB::table('delegate_creations')->insert([
                        'delegate_id' => $newDelegate->id,
                        'user_id'     => $this->user->id
                    ]);

                    DB::table("import_delegates")->insert([
                        'batch_id'    => $batchId,
                        'delegate_id' => $newDelegate->id,
                        'is_success'  => true,
                    ]);

                } catch (\Exception $exception) {

                    DB::table("import_delegates")->insert([
                        'batch_id'   => $batchId,
                        'note'       => serialize($record),
                        'is_success' => false,
                    ]);
                }
            }
        }
    }


    private function getData() {

        $handle = fopen($this->filePath, "r");

        $header = null;

        while ($data = fgetcsv($handle)) {
            if ($header === null) {
                $header = $data;
            } else {
                yield array_combine($header, $data);
            }
        }

        fclose($handle);

        File::delete($this->filePath);
    }

    private function validateData(array $data, array $rules): ?array {
        $validator = Validator::make($data, $rules);

        if ($validator->passes()) {
            return $validator->validate();
        }

        return null;
    }

    /**
     * @param \App\Transaction  $transaction
     * @param \App\Delegate     $delegate
     * @param \App\DelegateRole $role
     * @return array
     * @throws \ReflectionException
     */
    private function getValidationRules(
        Transaction $transaction, Delegate $delegate, DelegateRole $role
    ): array {
        $rules = $delegate->getStoreRules();
        $rules = array_merge($rules, [
            'role' => 'required|in:' . implode(',',
                    $role->pluck('code')->toArray())
        ]);
        $rules = $rules = array_merge($rules, $transaction->getRules());

        return $rules;
    }
}
