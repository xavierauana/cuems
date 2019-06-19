<?php

namespace App;

use App\Enums\CarbonCopyType;
use App\Enums\DelegateDuplicationStatus;
use App\Enums\SystemEvents;
use App\Jobs\ScheduleNotification;
use App\Mail\NotificationMailable;
use App\Mail\TransactionMail;
use Collective\Html\Eloquent\FormAccessible;
use Illuminate\Database\Eloquent\Builder;
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
        'type',
        'event',
        'role_id',
        'options',
        'subject',
        'keyword',
        'template',
        'schedule',
        'from_name',
        'from_email',
        'include_ticket',
        'verified_only',
        'check_in_date',
        'include_duplicated',
    ];

    protected $casts = [
        'include_duplicated' => 'boolean',
        'schedule'           => 'datetime',
        'check_in_date'      => 'date'
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

    public function getOptionsAttribute(?string $value): array {
        return $value ? unserialize($value) : [];
    }

    // Mutator
    public function setOptionsAttribute($value) {
        $options = $this->options;
        foreach ($value as $key => $val) {
            $options[$key] = $val;
        }
        $this->attributes['options'] = serialize($options);
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
            'schedule'           => 'required_if:type,attendee|date',
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
            'check_in_date'      => "nullable",
            'keyword'            => "nullable",
            'type'               => "nullable",
            'options'            => 'nullable'
        ];
    }

    /**
     * @param null $notifiable
     * @throws \Exception
     */
    public function send($notifiable = null): void {
        Log::info('send');
        if ($notifiable) {
            $this->checkAndSendNotificationToDelegate($notifiable);
        } elseif ($this->role) {
            $collection = $this->getDelegatesWIthRole();

            if (isset(($this->options)['unique_transaction']) and ($this->options)['unique_transaction']) {
                $collection = $collection->unique('id');
            }
            $collection->each(function (Delegate $delegate) {
                $this->checkAndSendNotificationToDelegate($delegate);
            });
        } else {
            $collection = $this->getFilteredDelegateQuery()
                               ->get();

            Log::info(isset(($this->options)['unique_transaction']) and ($this->options)['unique_transaction']);

            if (isset(($this->options)['unique_transaction']) and ($this->options)['unique_transaction']) {
                $collection = $collection->unique('id');

                Log::info('delegate counts ' . $collection->count());

            }

            $collection->pluck('id')->map(function($id){
                Log::info("id: {$id}");
            });


            $collection->each(function (Delegate $delegate) {
                $this->checkAndSendNotificationToDelegate($delegate);
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

        $query = $this->getFilteredDelegateQuery();

        if ($this->role_id) {
            $query->hasRole((int)$this->role_id);
            //            $query->whereIn('id', function ($query) {
            //                $query->select('delegate_id')
            //                      ->from('delegate_delegate_role')
            //                      ->where('delegate_role_id', $this->role_id);
            //            });
        }

        return $query->get();
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

    /**
     * @param $notifiable
     * @throws \Exception
     */
    private function checkAndSendNotificationToDelegate($notifiable
    ): void {
        if ($this->isScheduleAction and $notifiable instanceof Delegate) {
            Log::info('schedule send');
            ScheduleNotification::dispatch($this, $notifiable)
                                ->onQueue('email');
        } else {
            Log::info('normal send');
            $this->sendNotificationToDelegate($notifiable);
        }
    }

    /**
     * @param $query
     */
    private function filterOptions(&$query): void {
        if (!$this->include_duplicated) {
            $query->where('is_duplicated', "<>",
                DelegateDuplicationStatus::DUPLICATED);
        }

        if ($this->verified_only) {
            $query->where('is_verified', true);
        }
    }


    private function getExcludedList(): array {
        return [
            "lyy763@ha.org.hk",
            "kwy576@ha.org.hk",
            "loc2000a@gmail.com",
            "ith664@ha.org.hk",
            "hyh456@ha.org.hk",
            "wwy618@ha.org.hk",
            "wym173@ha.org.hk",
            "chf361@ha.org.hk",
            "skm491@ha.org.hk",
            "lwh305@ha.org.hk",
            "swk390@ha.org.hk",
            "chy338@ha.org.hk",
            "tct496@ha.org.hk",
            "kwchan4581@gmail.com",
            "ml151@ha.org.hk",
            "ckh439@ha.org.hk",
            "macho@ha.org.hk",
            "tkl168@ha.org.hk",
            "wjl924@ha.org.hk",
            "qidingyz@hotmail.com",
            "ivymstang@hkbh.org.hk",
            "kwanmc@ha.org.hk",
            "laist@ha.org.hk",
            "katiechan@cuhk.edu.hk",
            "moamina@yahoo.com",
            "chakwl@ha.org.hk",
            "cccz03@ha.org.hk",
            "albertkclai@hkbh.org.hk",
            "tkn637@ha.org.hk",
            "lamky7@ha.org.hk",
            "lmy084@ha.org.hk",
            "yuenkt@ha.org.hk",
            "lydiahptam@gmail.com",
            "chancpi@ha.org.hk",
            "lamky73@hotmail.com",
            "yeeks@ha.org.hk",
            "chiucs@ha.org.hk",
            "lws043@ha.org.hk",
            "cmc07571@ha.org.hk",
            "drlamho@yahoo.com.hk",
            "cwllarry@yahoo.com.hk",
            "chrischung99@yahoo.com",
            "kittyktcheung@gmail.com",
            "tsangty@ha.org.hk",
            "jonescmchan@gmail.com",
            "medct813@netvigator.com",
            "saraht2266@gmail.com",
            "swf518@ha.org.hk",
            "cch916@ha.org.hk",
            "fongcliniccme@gmail.com",
            "pyh523@ha.org.hk",
            "superaunt02@yahoo.com.hk",
            "wingkee02@gmail.com",
            "ywm604@ha.org.hk",
            "1155122477@link.cuhk.edu.hk",
            "rossleithen@gmail.com",
            "thomak@hotmail.com.hk",
            "ryancy@outlook.com",
            "ktse81@yahoo.com.hk",
            "felicitosvillena@gmail.com",
            "lionelgeolingo1971@gmail.com",
            "paomesina76@gmail.com",
            "aalexfu@netvigator.com",
            "amychanz@yahoo.com.hk",
            "bc_fyy@yahoo.com",
            "pallaschong@gmail.com",
            "Kenwood360@hotmail.com",
            "alan.lee@boehringer-ingelheim.com",
            "suki.chan@novartis.com",
            "suki.chan@novartis.com",
            "suki.chan@novartis.com",
            "johnny181919@hotmail.com",
            "laihinlarry@gmail.com",
            "judy.gy.cheng@hksh.com",
            "ericlcs1985@yahoo.com",
            "dr.kklau0602@gmail.com",
            "chchen@ha.org.hk",
            "ltl125@ha.org.hk",
            "sts879@wtsh.ha.org.hk",
            "Evan.Law@takeda.com",
            "Evan.Law@takeda.com",
            "Evan.Law@takeda.com",
            "conradkslam@gmail.com",
            "chiwailocal@yahoo.com",
            "drlausf@gmail.com",
            "drakokc@yahoo.com.hk",
            "ivanchtam@yahoo.com.hk",
            "chau.lucia268@gmail.com",
            "hww511@ha.org.hk",
            "twc769@ha.org.hk",
            "drshilpapafwardhan@gmail.com",
            "hoskv@hotmail.com",
            "cmc07295@ha.org.hk",
            "chanky10@ha.org.hk",
            "drkenyan@gmail.com",
            "suki.chan@novartis.com",
            "suki.chan@novartis.com",
            "plauwong@gmail.com",
            "drcheungtony@gmail.com",
            "bonba6880@yahoo.com.hk",
            "drandrewcyho@gmail.com",
            "secretary@newtownmedical.com.hk",
            "eyyuen@premierclinic.hk",
            "thomas.list@gmail.com",
            "anthonychan@polyhealth.com.hk",
            "coriue.cheong@boehringer-ingelheim.com",
            "yanlam2@hotmail.com",
            "cp.ccpz02@gmail.com",
            "kankaman11@yahoo.com.hk",
            "chungka@gmail.com",
            "kent.wl.cheung@gmail.com",
            "atirw@yahoo.com.hk",
            "stephen.or@bayer.com",
            "stephen.or@bayer.com",
            "stephen.or@bayer.com",
            "foxeslei@yahoo.com.hk",
            "peksanyuen@yahoo.com.hk",
            "koandrew@hotmail.com",
            "Peterpfkong@gmail.com",
            "sandy_law1209@hotmail.com",
            "sandysktang@yahoo.com",
            "ywlauhk@netvigator.com",
            "dmcdrugorder@gmail.com",
            "eddy.tai@menariniapac.com",
            "lawnlalan@yahoo.com",
            "atwaileung@yahoo.com.hk",
            "twm082@ha.org.hk",
            "canilao61@yahoo.com.hk",
            "vikkiswk@yahoo.com.hk",
            "wanhang_lau@hotmail.com",
            "chowwingkar@gmail.com",
            "chanagnes@gmail.com",
            "fyt109@ha.org.hk",
            "cyy640@ha.org.hk",
            "tyk895@ha.org.hk",
            "chanchikuen4@gmail.com",
            "mlklee@netvigator.com",
            "vshchoy@yahoo.com.hk",
            "mhs600@ha.org.hk",
            "uhssykho@gmail.com",
            "kayi0899@yahoo.com.hk",
            "michaellio@msn.com",
            "kwong.leslie@gmail.com",
            "tracy810602@yahoo.com",
            "cmc06345@ha.org.hk",
            "chumanhin88@yahoo.com.hk",
            "errolwong119@gmail.com",
            "dr.ymfung@yahoo.com.hk",
            "tchanpharm@gmail.com",
            "kcmak7@gmail.com",
            "yiukatherine@yahoo.com.hk",
            "cheunyne@ha.org.hk",
            "leungshekming1994@gmail.com",
            "ck708@ha.org.hk",
            "ctf319@ha.org.hk",
            "ctk334@ha.org.hk",
            "tetrahymena.lao@gmail.com",
            "kaiyeung0818@gmail.com",
            "hehahuang@hotmail.com",
            "tracylee31@yahoo.com.hk",
            "ich686@ha.org.hk",
            "winkychank2@gmail.com",
            "hyl051@ha.org.hk",
            "kkm581@ha.org.hk",
            "hwl643@ha.org.hk",
            "echimak@gmail.com",
            "loletta0128@yahoo.com.hk",
            "yuyancarmen@gmail.com",
            "csm339@ha.org.hk",
            "lau_chingman@yahoo.com",
            "mayswling@yahoo.com",
            "yyf318@ha.org.hk",
            "wat498@ha.org.hk",
            "lks095@ha.org.hk",
            "ccj460@ha.org.hk",
            "QC600@ha.org.hk",
            "ckk020@ha.org.hk",
            "wmh483@ha.org.hk",
            "mkw395@ha.org.hk",
            "ckt223@ha.org.hk",
            "yfsdermac@dh.gov.hk",
            "johnwoo_1123@yahoo.com.hk",
            "yfsdermac@dh.gov.hk",
            "lkw417@ha.org.hk",
            "cwk577@ha.org.hk",
            "mwl955@ha.org.hk",
            "mks038@ntwc.ha.org.hk",
            "lky972@ha.org.hk",
            "snoopyanyan23@yahoo.com.hk",
            "kingsleychanhk@gmail.com",
            "sh080@ha.org.hk",
            "holoyi@gmail.com",
            "lhy649@ha.org.hk",
            "lwzz04@ha.org.hk",
            "lamtw@ha.org.hk",
            "ksysunny@gmail.com",
            "williamfoo@hkbh.org.hk",
            "lamps1@ha.org.hk",
            "s.loszeching@gmail.com",
            "francischleung@hkbh.org.hk",
            "cwingw@gmail.com",
            "lkk691@ha.org.hk",
            "laust@ha.org.hk",
            "rliang@hksh.com",
            "wongpn@ha.org.hk",
            "yeungs@ha.org.hk",
            "chansf01@ha.org.hk",
            "chimwaiying@yahoo.com.hk",
            "wim315@ha.org.hk",
            "shanhs@ha.org.hk",
            "ckhj01@ha.org.hk",
            "htlz01@ha.org.hk",
            "yeungyc@ha.org.hk",
            "kytsang20@gmail.com",
            "kaiming.chan@gmail.com",
            "myflagyl@yahoo.com.hk",
            "hws463@ha.org.hk",
            "jennyngai25@yahoo.com",
            "tsangcc2@ha.org.hk",
            "mickfu@gmail.com",
            "leecyc@ha.org.hk",
            "wmj416@ha.org.hk",
            "kypoonpky@gmail.com",
            "fongnoel@hotmail.com",
            "nst418@ha.org.hk",
            "ls616@ha.org.hk",
            "nyc232@ha.org.hk",
            "thk883@ha.org.hk",
            "cc36mc26@yahoo.com",
            "dr.paultam@yahoo.com.hk",
            "1155105985@link.cuhk.edu.hk",
            "1155122349@link.cuhk.edu.hk",
            "1155122057@link.cuhk.edu.hk",
            "tamkin_shing@hotmail.com",
            "brisingr2008@gmail.com",
            "LVW802@uch.ha.org.hk",
            "eddiethchan@gmail.com",
            "myh375@ha.org.hk",
            "1155075608@link.cuhk.edu.hk",
            "nicanorvargas1970@gmail.com",
            "joseph.robles@mims.com",
            "hannalyluich@gmail.com",
            "argulest@netvigator.com",
            "socneu@gleneagles.hk",
            "health@asiabiobank.com",
            "liao2ting@gmail.com",
            "drchanhhclinic@gmail.com",
            "douglaschung125@gmail.com",
            "dickycws@yahoo.com.hk",
            "edmondcch@gmail.com",
            "peerchiu@netvigator.com",
            "dr.warrenwong@yahoo.com",
            "suki.chan@novartis.com",
            "suki.chan@novartis.com",
            "ckkmcarmen@gmail.com",
            "andrewsy@eisaihk.com",
            "jpkorn2@yahoo.com",
            "kingsleychanhk@gmail.com",
            "eddy.tai@menariniapac.com",
            "drkhoo@ymail.com",
            "uso62789@macau.ctm.net",
            "victorhoho03@yahoo.com.hk",
            "idi.wong@pfizer.com",
            "pollypftam@gmail.com",
            "rachel.kwong@pfizer.com",
            "angelalau282@yahoo.com",
            "lbk043@ha.org.hk",
            "khh379@ha.org.hk",
            "hlw256@ha.org.hk",
            "Evan.Law@takeda.com",
            "evan.law@takeda.com",
            "wongkw78@hotmail.com",
            "oreochau@gmail.com",
            "jpsl@netvigator.com",
            "dkkl1888@yahoo.co.uk",
            "ivy.ng@sanofi.com",
            "ivy.ng@sanofi.com",
            "ivy.ng@sanofi.com",
            "leungkapou@hotmail.com",
            "drvincenttam@yahoo.com.hk",
            "khwong2906@yahoo.com.hk",
            "ippk@netvigator.com",
            "tanghoichinglarissa@hotmail.com",
            "ericchan@chiron.care",
            "macuk2@hotmail.com",
            "drgolf@netvigator.com",
            "lo_shuk_kam@yahoo.com.hk",
            "suki.chan@novartis.com",
            "suki.chan@novartis.com",
            "suki.chan@novartis.com",
            "suki.chan@novartis.com",
            "drstevenho@gmail.com",
            "dr_syshe@hotmail.com",
            "drnormaanchan@gmail.com",
            "dennykkchan@gmail.com",
            "sammi.gu@merck.com",
            "ypso@premierclinic.hk",
            "fnyeung@premierclinic.hk",
            "lkf729@gmail.com",
            "alan.ng@boehringer-ingelheim.com",
            "ming.shum@boehringer-ingelheim.com",
            "phebe.pao@boehringer-ingelheim.com",
            "yuhchk@gmail.com",
            "ajmsun@gmail.com",
            "jason.tong@uqconnect.edu.au",
            "ckhung@netvigator.com",
            "yanshingmedical@gmail.com",
            "light2012@ymail.com",
            "drtnchau@gmail.com",
            "chriswong@dchauriga.com",
            "Chris.Wong@gilead.com",
            "mashingyin@gmail.com",
            "dr_awong@hotmail.com",
            "pacoleeclinic@yahoo.com.hk",
            "kwh635@hotmail.com",
            "sunnyclinic@biznetvigator.com",
            "pel8879@yahoo.com.hk",
            "ellachan@connexustravel.com",
            "Chungpyrebecca@yahoo.com.hk",
            "drdenniscccheng@gmail.com",
            "eddy.tai@menariniapac.com",
            "info@famcare.com.hk",
            "leungtatchi@yahoo.com",
            "drlamsk@yahoo.com.hk",
            "alice_ykchan@yahoo.com.hk",
            "roychow20@hotmail.com",
            "pch766@ha.org.hk",
            "lorrainelly@gmail.com",
            "haileylintw@gmail.com",
            "tengchongshing@hotmail.com",
            "yuenman9371@gmail.com",
            "hokcc@netvigator.com",
            "ysk952@ha.org.hk",
            "lck238a@ha.org.hk",
            "lkw271@ha.org.hk",
            "kmcaroll@yahoo.com",
            "yeungym@ha.org.hk",
            "maklk@ha.org.hk",
            "pekpeksio@yahoo.com.hk",
            "szewaiwo@gmail.com",
            "mussy_kam@yahoo.com.hk",
            "winnie103@gmail.com",
            "howard_pak@yahoo.com",
            "minghk1013@yahoo.com.hk",
            "kcahroein@gmail.com",
            "sky859@ha.org.hk",
            "chu_anita@yahoo.com.hk",
            "rex.hung@mail.stpaul.org.hk",
            "lwk585@ha.org.hk",
            "omnihealthmedical@yahoo.com.hk",
            "jeffreyleehim@gmail.com",
            "lokyeechan94@gmail.com",
            "tinlee88@yahoo.com.hk",
            "benzs6002@hotmail.com",
            "banananurse@hotmail.com",
            "Quatre881903@hotmail.com",
            "lhy230@ha.org.hk",
            "pwl682@ha.org.hk",
            "lcl834@ha.org.hk",
            "wyk193@ha.org.hk",
            "lcm686@ha.org.hk",
            "keishinghui9351@gmail.com",
            "emily.waiyi@gmail.com",
            "lkf283@ha.org.hk",
            "priscillayyt@yahoo.com.hk",
            "andylam0526@yahoo.com.hk",
            "cwf168@ha.org.hk",
            "shiuyeungkwan1991@gmail.com",
            "lys920@ha.org.hk",
            "whsz02@ha.org.hk",
            "samuelhung2010@hotmail.com",
            "adrianpoon@yahoo.com.hk",
            "hehehahayam@hotmail.com",
            "bonmak23@hotmail.com",
            "wny338@ha.org.hk",
            "lsm872@ha.org.hk",
            "yfsdermac@dh.gov.hk",
            "ckp783@ha.org.hk",
            "kcc028@ha.org.hk",
            "yck530@ha.org.hk",
            "fyk460@ha.org.hk",
            "ckl081@ha.org.hk",
            "fmc841@ha.org.hk",
            "tys3581@ha.org.hk",
            "wtl552@ha.org.hk",
            "wky697@ha.org.hk",
            "kamtmjanice@gmail.com",
            "hokt2@ha.org.hk",
            "cwl518@ha.org.hk",
            "lyh183@ha.org.hk",
            "lmw712@ha.org.hk",
            "lok816@ha.org.hk",
            "chanyk3@ha.org.hk",
            "au_kar_ming@hotmail.com",
            "thjoshua@gmail.com",
            "cms120@ha.org.hk",
            "cco338@ha.org.hk",
            "ctp099@ha.org.hk",
            "cyfz02@ha.org.hk",
            "alexsze@yahoo.com",
            "Owhz01@ha.org.hk",
            "ngck6@ha.org.hk",
            "chanyhj@ha.org.hk",
            "ccc601@ha.org.hk",
            "pky310@ha.org.hk",
            "auchikinalex2008@gmail.com",
            "tscheng@hkbh.org.hk",
            "cpsj01@ha.org.hk",
            "chanlv@ha.org.hk",
            "makyf@ha.org.hk",
            "yipwm@ha.org.hk",
            "leungcs1@ha.org.hk",
            "cch670@ha.org.hk",
            "simoncwwong@hkbh.org.hk",
            "leehoikan@yahoo.com.hk",
            "ycw642@ha.org.hk",
            "ngcya@ha.org.hk",
            "wwy480@ha.org.hk",
            "maksk@ha.org.hk",
            "ckwddhksemr@yahoo.com.hk",
            "tiffanyvivace@yahoo.com",
            "yuensk@ha.org.hk",
            "mederlmt@gmail.com",
            "mflaw99@yahoo.com.hk",
            "drmlyip@yahoo.com.hk",
            "mtang@ha.org.hk",
            "losk@ha.org.hk",
            "mokk@ha.org.hk",
            "huie@ha.org.hk",
            "lwn752@ha.org.hk",
            "tcw732@ha.org.hk",
            "wly362@ha.org.hk",
            "myh487@ha.org.hk",
            "kenyc1023@gmail.com",
            "wongyy4@ha.org.hk",
            "1155121545@link.cuhk.edu.hk",
            "vlee0218@gmail.com",
            "wongocean1@yahoo.com.hk",
            "wingmingfung@gmail.com",
            "stepheniemak@gmail.com",
            "sophia.ling@gmail.com",
            "edmundhylo@yahoo.com",
            "eddy.tai@menariniapac.com",
            "tck983@ha.org.hk",
            "carmeladelaflor1973@gmail.com",
            "albertoang1953@gmail.com",
            "rodelfermantez1977@gmail.com",
            "peiyee890810@yahoo.com",
            "cbwong2007@yahoo.com",
            "Bernard.Loo@astrazeneca.com",
            "lmtso@netvigator.com",
            "ivy.ng@sanofi.com",
            "ivy.ng@sanofi.com",
            "siwengmps@yahoo.com.hk",
            "dr.tony.kc.lam@gmail.com",
            "laisekfai@msn.com",
            "fannycheungwe.care@gmail.com",
            "drgwkyip@gmail.com",
            "vincent.y.cheung@hksh.com",
            "ptongcy@gmail.com",
            "dchan1219317@gmail.com",
            "michelleho@eisaihk.com",
            "dicksonchan@eisaihk.com",
            "becky_beagle@hotmail.com",
            "idi.wong@pfizer.com",
            "tuetos2001@yahoo.com.hk",
            "calvin.chiu@pfizer.com",
            "drwbwong@yahoo.com.hk",
            "yxj024@pmh.ha.org.hk",
            "cyh423a@ha.org.hk",
            "timothy.kh.fung@hksh.com",
            "cwy864@ha.org.hk",
            "hkl774@ha.org.hk",
            "cmc163@ha.org.hk",
            "Evan.Law@takeda.com",
            "evan.law@takeda.com",
            "Evan.Law@takeda.com",
            "lawsaion@hkma.org",
            "chongwanyip@gmail.com",
            "warrenchak@yahoo.com.hk",
            "wh.chow@twah.org.hk",
            "patlaukw@netvigator.com",
            "ivy.ng@sanofi.com",
            "shumtaichun@yahoo.com.hk",
            "drleungcw@outlook.com",
            "1146440534@qq.com",
            "cheungwaicheung@gmail.com",
            "DOCTORDEARDANIEL@YAHOO.COM.HK",
            "davie.wc.lam@gmail.com",
            "lau779900@yahoo.com.hk",
            "msnurse.dringe@yahoo.com.hk",
            "wocwleung@yahoo.com.hk",
            "suki.chan@novartis.com",
            "suki.chan@novartis.com",
            "pmflau@gmail.com",
            "suki.chan@novartis.com",
            "suki.chan@novartis.com",
            "suki.chan@novartis.com",
            "dooclinic@gmail.com",
            "tsangpingham@gmail.com",
            "dr.choilh@gmail.com",
            "dr_skkong@yahoo.com.hk",
            "bruceng2@hotmail.com",
            "bruceng2@hotmail.com",
            "atwyeung@netvigator.com",
            "timothy.suen@merckgroup.com",
            "wongasp@on-nets.com",
            "hkwok@hkoccmed.com",
            "alo@premiermedical.com.hk",
            "drngkca@yahoo.com.hk",
            "dr.angelinelo@gmail.com",
            "kungnns@netvigator.com",
            "mhunghg@yahoo.com.hk",
            "ashken.siu@dksh.com",
            "sleepreport@yahoo.com.hk",
            "ashken.siu@dksh.com",
            "vinju@netvigator.com",
            "spang75@gmail.com",
            "sc2012sc2007@yahoo.com",
            "drclinicphoto@yahoo.com",
            "dreauclinic@gmail.com",
            "stephen.or@bayer.com",
            "tkiTT122@hotmail.com",
            "drdavidwatclinic@gmail.com",
            "kenk1393@gmail.com",
            "annette111@netvigator.com",
            "siang_yew@yahoo.com",
            "chanhowyin@hotmail.com",
            "eddy.tai@menariniapac.com",
            "kkrubyau@hotmail.com",
            "wmfyeung@hotmail.com",
            "auts123@yahoo.com.hk",
            "drshawnng@gmail.com",
            "paulsit2008@yahoo.com.hk",
            "sec@tungchun.hk",
            "wty504@ha.org.hk",
            "tcz586@ha.org.hk",
            "tyh421@ha.org.hk",
            "ipchiching@hotmail.com",
            "tyf770@ych.ha.org.hk",
            "luiwaiting1217@hotmail.com",
            "cc808@ha.org.hk",
            "lmk843@ha.org.hk",
            "cyk768@ha.org.hk",
            "hungwaihim@gmail.com",
            "aslleung@ha.org.hk",
            "mabelmm1104@gmail.com",
            "wms807@ha.org.hk",
            "phoenix.tung@hotmail.com",
            "ronnietsang827@gmail.com",
            "keithlee668@gmail.com",
            "minkminkko115@gmail.com",
            "chungzx@hotmail.com",
            "candylai38@yahoo.com.hk",
            "alan_shinglung@yahoo.com.hk",
            "kpl999999@gmail.com",
            "liw765@ha.org.hk",
            "emily5192001@yahoo.com.hk",
            "terence_chowcp@yahoo.com.hk",
            "macaualvin@yahoo.com.hk",
            "fancdetour@gmail.com",
            "agnesc115@gmail.com",
            "rosannatse1005@gmail.com",
            "cmk233@ha.org.hk",
            "cindypscheung@yahoo.com.hk",
            "danielip0216@gmail.com",
            "kkh914@ha.org.hk",
            "cheahpc@ha.org.hk",
            "davidlau182001@yahoo.com.hk",
            "cychong222@hotmail.com",
            "atang803@gmail.com",
            "ly134@ha.org.hk",
            "nhy320@ha.org.hk",
            "lcl867@ha.org.hk",
            "mchanmy@hotmail.com",
            "henryng5@gmail.com",
            "ckl532@ha.org.hk",
            "rickibiotic@yahoo.com.hk",
            "tesslui@gmail.com",
            "ingridsy_2004@yahoo.com.hk",
            "pharmlui@yahoo.com.hk",
            "wongs9d@gmail.com",
            "cky447@ha.org.hk",
            "hpy725@ha.org.hk",
            "hkwong612@gmail.com",
            "lwk794@ha.org.hk",
            "tnm326@ha.org.hk",
            "chm873@ha.org.hk",
            "lws415@ha.org.hk",
            "cindyching21@hotmail.com",
            "ckw518@ha.org.hk",
            "ccc802@ha.org.hk",
            "tcs694@ha.org.hk",
            "freda927chu@live.hk",
        ];
    }

    /**
     * @return \App\Delegate
     */
    private function getFilteredDelegateQuery(): Builder {

        $query = Delegate::where('delegates.event_id', $this->event_id);

        if ($this->type === 'attendee') {
            $query = Event::findOrFail($this->event_id)
                          ->getCheckinJoinQuery()
                          ->select('delegates.*');

            if ($this->check_in_date) {
                $query->whereDate('check_in.created_at', $this->check_in_date);
            }
            if ($this->keyword) {
                $query->where(function ($query) {
                    $query->where('delegates.first_name', 'like',
                        "%{$this->keyword}%")
                          ->orWhere('delegates.last_name', 'like',
                              "%{$this->keyword}%")
                          ->orWhere('delegates.email', 'like',
                              "%{$this->keyword}%")
                          ->orWhere('tickets.name', 'like',
                              "%{$this->keyword}%");
                });
            }
        }

        $this->filterOptions($query);

        return $query;
    }
}
