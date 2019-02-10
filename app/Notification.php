<?php

namespace App;

use App\Enums\DelegateDuplicationStatus;
use App\Enums\SystemEvents;
use App\Mail\NotificationMailable;
use App\Mail\TransactionMail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Mail;

class Notification extends Model
{
    protected $fillable = [
        'name',
        'event',
        'role_id',
        'template',
        'schedule',
        'from_name',
        'from_email',
        'cc',
        'bcc',
        'subject',
        'include_ticket',
        'verified_only',
        'include_duplicated'
    ];

    protected $casts = [
        'include_duplicated' => 'boolean'
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


    /**
     * @param $notifiable
     * @throws \Exception
     */
    private function sendNotificationToDelegate($notifiable): void {

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
            'cc'                 => "nullable|email",
            'bcc'                => "nullable|email",
            'files'              => "nullable",
            'files.*'            => "exists:upload_files,id",
        ];
    }

    public function send($notifiable = null): void {

        if ($notifiable) {
            if (strtolower($notifiable->is_duplicated) === strtolower(DelegateDuplicationStatus::DUPLICATED) and $this->include_duplicated === false) {
                return;
            }

            $this->sendNotificationToDelegate($notifiable);

        } elseif ($this->role) {

            $query = $this->role->delegates();

            if (!$this->include_duplicated) {
                $query->where('is_duplicated', "<>",
                    DelegateDuplicationStatus::DUPLICATED);
            }

            if ($this->verified_only) {
                $query->where('is_verified', true);
            }

            $delegates = $query->get();

            $delegates->each(function (Delegate $delegate) {
                $this->sendNotificationToDelegate($delegate);
            });

        } else {
            $query = Delegate::latest();

            if (!$this->include_duplicated) {
                $query->where('is_duplicated', DelegateDuplicationStatus::NO);
            }

            if ($this->verified_only) {
                $query->where('is_verified', true);
            }

            $delegates = $query->get();

            $delegates->each(function ($d) {
                $this->sendNotificationToDelegate($d);
            });

            $this->is_sent = true;
            $this->save();
        }
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


}
