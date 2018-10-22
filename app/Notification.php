<?php

namespace App;

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
        'subject',
        'include_ticket'
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

    // Accessor
    public function getEventNameAttribute(): string {
        $systemEvents = array_flip((new \ReflectionClass(SystemEvents::class))->getConstants());

        return ucwords(strtolower(str_replace("_", " ",
            $systemEvents[$this->event])));
    }


    /**
     * @param $notifiable
     * @throws \Exception
     */
    private function sendNotificationToDelegate($notifiable): void {

        list($email, $mail) = $this->createMail($notifiable);

        Mail::to($email)
            ->send($mail);
    }

    // Helpers

    public function getStoreRules(): array {
        return [
            'template'       => 'required',
            'name'           => 'required',
            'schedule'       => 'nullable|date',
            'event'          => 'required|in:' . implode(",",
                    array_values(SystemEvents::getEvents())),
            'role_id'        => 'nullable|in:0,' . implode(",",
                    DelegateRole::pluck("id")->toArray()),
            'from_name'      => "required",
            'from_email'     => "required|email",
            'subject'        => "required",
            'include_ticket' => "nullable|boolean",
        ];
    }

    public function send($notifiable = null): void {

        if ($notifiable) {
            $this->sendNotificationToDelegate($notifiable);
        } elseif ($this->role) {
            $delegates = $this->role->delegates;
            $delegates->each(function (Delegate $delegate) {
                $this->sendNotificationToDelegate($delegate);
            });
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
            $mail = new NotificationMailable($this,
                $notifiable,
                $this->event()->first(), $this->include_ticket);

        } elseif ($notifiable instanceof Transaction) {

            $notifiable->load('payee');

            $mail = new TransactionMail($this,
                $notifiable,
                $this->event()->first());
        } else {
            throw new \Exception("Not support notifiable");
        }

        return [$email, $mail];
    }


}
