<?php

namespace App;

use App\Enums\CarbonCopyType;
use App\Enums\DelegateDuplicationStatus;
use App\Enums\SystemEvents;
use App\Jobs\ScheduleNotification;
use App\Mail\NotificationMailable;
use App\Mail\TransactionMail;
use Collective\Html\Eloquent\FormAccessible;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Notification extends Model
{
    use FormAccessible;

    private $isScheduleAction = false;

    protected $fillable = [
        'name',
        'event',
        'role_id',
        'template',
        'schedule',
        'from_name',
        'from_email',
        'subject',
        'include_ticket',
        'verified_only',
        'include_duplicated'
    ];

    protected $casts = [
        'include_duplicated' => 'boolean',
        'schedule'           => 'datetime'
    ];


    // Relation
    public function event(): Relation {
        return $this->belongsTo(Event::class);
    }

    public function recipient(): Relation {
        return $this->morphTo();
    }

    public function role(): Relation {
        return $this->belongsTo(DelegateRole::class);
    }

    public function uploadFiles(): Relation {
        return $this->belongsToMany(UploadFile::class);
    }

    public function copies(): HasMany {
        return $this->hasMany(CarbonCopy::class, 'notification_id');
    }

    // Accessor
    public function getEventNameAttribute(): string {
        $systemEvents = array_flip((new \ReflectionClass(SystemEvents::class))->getConstants());

        if (isset($systemEvents[$this->event])) {
            return ucwords(strtolower(str_replace("_", " ",
                $systemEvents[$this->event])));
        }

        return "None";
    }

    public function getFilesAttribute() {
        $result = $this->uploadFiles()->pluck('id')->toArray();

        return $result;
    }

    // Form accessable

    public function formCcAttribute(): string {
        $emails = $this->copies()->whereType(CarbonCopyType::CC()->getValue())
                       ->pluck('email')->toArray();

        return implode(',', $emails);
    }

    public function formBccAttribute(): string {
        $emails = $this->copies()->whereType(CarbonCopyType::BCC()->getValue())
                       ->pluck('email')->toArray();

        return implode(',', $emails);
    }


    /**
     * @param $notifiable
     * @throws \Exception
     */
    public function sendNotificationToDelegate($notifiable): void {

        list($email, $mail) = $this->createMail($notifiable);

        Mail::to($email)->send($mail);
    }

    // Helpers

    public function getStoreRules(): array {
        return [
            'template'           => 'required',
            'name'               => 'required',
            'schedule'           => 'nullable|date',
            'event'              => 'nullable|in:0,' . implode(",",
                    array_values(SystemEvents::getEvents())),
            'role_id'            => 'nullable|in:0,' . implode(",",
                    DelegateRole::pluck('id')->toArray()),
            'from_name'          => "required",
            'from_email'         => "required|email",
            'subject'            => "required",
            'verified_only'      => "nullable|boolean",
            'include_duplicated' => "nullable|boolean",
            'include_ticket'     => "nullable|boolean",
            'cc'                 => "nullable|emailsString",
            'bcc'                => "nullable|emailsString",
            'files'              => "nullable",
            'files.*'            => "exists:upload_files,id",
        ];
    }

    public function send($notifiable = null): void {

        if ($notifiable) {
            $this->sendNotificationToDelegate($notifiable);
        } elseif ($this->role) {
            $this->getDelegatesWIthRole()
                 ->each(function (Delegate $delegate) {
                     if ($this->isScheduleAction) {
                         ScheduleNotification::dispatch($this, $delegate)
                                             ->onQueue('email');
                     } else {
                         Log::info('not scheduled send');
                         $this->sendNotificationToDelegate($delegate);
                     }
                 });
        } else {
            $this->getAllDelegates()
                 ->each(function ($d) {
                     $this->sendNotificationToDelegate($d);
                 });
        }
    }

    public function addCc(string $email, string $name = null): CarbonCopy {
        $data = [
            'email' => $email,
            'name'  => $name,
            'type'  => CarbonCopyType::CC(),
        ];

        return $this->copies()->create($data);
    }

    public function addBcc(string $email, string $name = null) {
        $data = [
            'email' => $email,
            'name'  => $name,
            'type'  => CarbonCopyType::BCC(),
        ];

        return $this->copies()->create($data);
    }

    public function syncCc(array $emails) {
        $this->syncCopies($emails, CarbonCopyType::CC());
    }

    public function syncBcc(array $emails) {
        $this->syncCopies($emails, CarbonCopyType::BCC());
    }


    /**
     * @param $notifiable
     * @return \App\Mail\NotificationMailable|\App\Mail\TransactionMail
     * @throws \Exception
     */
    private function createMail($notifiable): array {
        $email = $notifiable->routeNotificationForMail();
        if ($notifiable instanceof Delegate) {
            $mail = new NotificationMailable(
                $this,
                $notifiable,
                $this->event()->first());

        } elseif ($notifiable instanceof Transaction) {

            $notifiable->load('payee');

            $mail = new TransactionMail(
                $this,
                $notifiable,
                $this->event()->first());
        } else {
            throw new \Exception("Not support notifiable");
        }

        return [$email, $mail];
    }

    /**
     * @param array                     $emails
     * @param \App\Enums\CarbonCopyType $type
     */
    private function syncCopies(array $emails, CarbonCopyType $type): void {
        $query = $this->copies()
                      ->whereType($type->getValue());

        $ccs = $query->get();

        $ccs->each(function (CarbonCopy $copy) use ($emails) {
            if (!in_array($copy->email, $emails)) {
                $copy->delete();
            }
        });


        foreach ($emails as $email) {
            if ($this->copies()
                     ->whereType($type->getValue())->whereEmail($email)
                     ->first() === null) {
                $this->copies()->create([
                    'email' => $email,
                    'type'  => $type->getValue()
                ]);
            }
        }
    }

    // Helps

    public static function parseEmailString(string $emailsString = null
    ): array {
        $emails = [];
        if ($emailsString) {
            $emails = array_map('trim', explode(',', $emailsString));
        }

        return $emails;
    }

    /**
     * @return mixed
     */
    private function getDelegatesWIthRole() {

        $query = Delegate::where('event_id', $this->event_id);

        if ($this->role_id) {
            $query->whereIn('id', function ($query) {
                $query->select('delegate_id')
                      ->from('delegate_delegate_role')
                      ->where('delegate_role_id', $this->role_id);
            });
        }

        if (!$this->include_duplicated) {
            $query->where('is_duplicated', "<>",
                DelegateDuplicationStatus::DUPLICATED);
        }

        if ($this->verified_only) {
            $query->where('is_verified', true);
        }

        $delegates = $query->get();

        return $delegates;
    }

    /**
     * @return \App\Delegate[]|\Illuminate\Database\Eloquent\Collection
     */
    private function getAllDelegates() {
        $query = Delegate::where('event_id', $this->event_id);

        if (!$this->include_duplicated) {
            $query->where('is_duplicated', DelegateDuplicationStatus::NO);
        }

        if ($this->verified_only) {
            $query->where('is_verified', true);
        }

        $delegates = $query->get();

        return $delegates;
    }

    public function markSent(): void {
        $this->is_sent = true;
        $this->save();
    }

    /**
     * @param bool $isScheduleAction
     * @return Notification
     */
    public function setIsScheduleAction(bool $isScheduleAction): Notification {
        $this->isScheduleAction = $isScheduleAction;

        return $this;
    }

}
