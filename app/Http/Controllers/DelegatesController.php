<?php

namespace App\Http\Controllers;

use App\Contracts\DuplicateCheckerInterface;
use App\Delegate;
use App\Enums\DelegateDuplicationStatus;
use App\Enums\SystemEvents;
use App\Enums\TransactionStatus;
use App\Event;
use App\Events\SystemEvent;
use App\Exports\DelegateExport;
use App\Exports\NewDelegate;
use App\Http\Requests\DelegateUpdateRequest;
use App\Imports\DelegatesImport;
use App\Imports\NewDelegateImport;
use App\Jobs\ImportDelegates;
use App\Jobs\UpdateNewDelegates;
use App\Services\DelegateCreationService;
use App\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class DelegatesController extends Controller
{
    /**
     * @var \App\Delegate
     */
    private $repo;

    /**
     * DelegatesController constructor.
     * @param \App\Delegate $delegate
     */
    public function __construct(Delegate $delegate) {
        $this->repo = $delegate;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Event               $event
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Event $event, Request $request) {
        $query = $event->delegates()
                       ->with('event')
                       ->where("is_duplicated",
                           "<>", DelegateDuplicationStatus::DUPLICATED);

        $queries = $request->query();

        $query = $this->constructSearchQuery($this->repo, $request, $query,
            'keyword');

        unset($queries['keyword']);

        foreach ($queries as $key => $oder) {
            $query = $query->orderBy($key, $oder);
        }

        $delegates = $query->paginate();

        return view('admin.events.delegates.index',
            compact('event', 'delegates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \App\Event $event
     * @return \Illuminate\Http\Response
     * @throws \ReflectionException
     */
    public function create(Event $event) {

        $reflection = new \ReflectionClass(TransactionStatus::class);

        $status = array_flip($reflection->getConstants());

        return view("admin.events.delegates.create",
            compact("event", "status"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Event                            $event
     * @param  \Illuminate\Http\Request             $request
     * @param \App\Transaction                      $transaction
     * @param \App\Services\DelegateCreationService $service
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \ReflectionException
     */
    public function store(
        Event $event, Request $request, Transaction $transaction,
        DelegateCreationService $service
    ): RedirectResponse {

        $rules = array_merge($this->repo->getStoreRules(),
            $transaction->getRules());

        $validatedData = $this->validate($request, $rules);

        $newDelegate = $service->create($event, $validatedData);

        Log::info('fire event: ' . $newDelegate->name);

        event(new SystemEvent(SystemEvents::ADMIN_CREATE_DELEGATE,
            $newDelegate));

        return redirect()->route("events.delegates.index", $event);

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Event     $event
     * @param  \App\Delegate $delegate
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event, Delegate $delegate) {

        //        $checker = new DelegateDuplicateChecker($event);
        $checker = app(DuplicateCheckerInterface::class)->setEvent($event);

        $duplicates = $checker->find('email', $delegate->email)
                              ->filter(function ($i) use (
                                  $delegate
                              ) {
                                  return $i->id !== $delegate->id;
                              });

        $duplicates = $duplicates->merge($checker->find('mobile',
            $delegate->mobile)
                                                 ->filter(function ($i) use (
                                                     $delegate
                                                 ) {
                                                     return $i->id !== $delegate->id;
                                                 }));
        $duplicates = $duplicates->unique('id');

        return view("admin.events.delegates.show",
            compact('event', 'delegate', 'duplicates'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Event     $event
     * @param  \App\Delegate $delegate
     * @return \Illuminate\Http\Response
     * @throws \ReflectionException
     */
    public function edit(Event $event, Delegate $delegate) {
        $reflection = new \ReflectionClass(TransactionStatus::class);

        $status = array_flip($reflection->getConstants());

        return view("admin.events.delegates.edit",
            compact('event', 'delegate', 'status'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\DelegateUpdateRequest $request
     * @param \App\Event                               $event
     * @param  \App\Delegate                           $delegate
     * @param \App\Transaction                         $transaction
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function update(
        DelegateUpdateRequest $request, Event $event, Delegate $delegate,
        Transaction $transaction
    ) {
        $validatedData = $request->validated();

        DB::beginTransaction();

        try {

            $this->updateDelegate($delegate, $validatedData);

            DB::commit();

        } catch (\Exception $exception) {

            DB::rollBack();

            throw $exception;
        }

        return redirect()->route("events.delegates.index", $event);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Event     $event
     * @param  \App\Delegate $delegate
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Event $event, Delegate $delegate
    ) {
        $delegate = $event->delegates()->find($delegate->id);

        $delegate->delete();

        return redirect()->back()->withStatus('Delegate deleted');
    }

    /**
     *  Mass import delegates
     *
     * @param \App\Event                            $event
     * @param \Illuminate\Http\Request              $request
     * @param \App\Services\DelegateCreationService $service
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postImport(
        Event $event, Request $request, DelegateCreationService $service
    ): RedirectResponse {
        $this->validate($request, [
            'file' => 'required|file|min:0'
        ]);

        $collection = Excel::toCollection(new DelegatesImport,
            $request->file('file'));

        $job = new ImportDelegates($event, $collection->first(),
            $request->user());

        $this->dispatch($job);

        return redirect()->route('events.delegates.index', $event);
    }

    public function getImport(Event $event, Request $request) {
        return view("admin.events.delegates.import", compact('event'));
    }


    /**
     * @param \App\Event               $event
     * @param \Illuminate\Http\Request $request
     * @param                          $validatedData
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    private function createDelegate(
        Event $event, Request $request, $validatedData
    ) {
        $registrationId = ($event->delegates()
                                 ->max('registration_id') ?? 0) + 1;
        $validatedData['registration_id'] = $registrationId;
        $newDelegate = $event->delegates()->create($validatedData);

        $newDelegate->transactions()->create($validatedData);

        $newDelegate->roles()->sync($validatedData['roles_id']);

        DB::table('delegate_creations')->insert([
            'delegate_id' => $newDelegate->id,
            'user_id'     => $request->user()->id
        ]);

        if (isset($validatedData['sponsor']['sponsor_id'])) {
            $sponsorData = $validatedData['sponsor'];

            $newDelegate->sponsorRecord()->create($sponsorData);
        }

        return $newDelegate;
    }

    /**
     * @param \App\Delegate $delegate
     * @param array         $validatedData
     * @return \App\Delegate
     */
    private function updateDelegate(
        Delegate $delegate, array $validatedData
    ): Delegate {
        $validatedData['duplicated_with'] = $validatedData['is_duplicated'] ? $validatedData['duplicated_with'] : null;
        $delegate->update($validatedData);

        $delegate->transactions()->latest()->first()
                 ->update($validatedData);

        $delegate->roles()->sync($validatedData['roles_id']);

        if (isset($validatedData['sponsor']['sponsor_id'])) {
            $sponsorData = $validatedData['sponsor'];

            $delegate->sponsorRecord()
                     ->updateOrCreate(['delegate_id' => $delegate->id],
                         $sponsorData);
        } elseif ($delegate->sponsorRecord) {
            $delegate->sponsorRecord->delete();
        }

        return $delegate;
    }

    public function search(Request $request, Event $event) {

        if ($keyword = $request->query('keyword')) {
            $delegates = $event->delegates()
                               ->paginate();

            return view("admin.events.delegates.index",
                compact('delegates', 'event'));
        }

        return redirect()->route('events.delegates.index', $event);

    }

    public function export(Request $request, Event $event) {
        $event->load('delegates.transactions.ticket');
        $event->load('delegates.roles');

        if ($request->query('duplicated')) {
            return Excel::download(new DelegateExport($event, 'duplicated'),
                'delegates.xls');
        } else {
            return Excel::download(new DelegateExport($event),
                'delegates.xls');
        }
    }

    public function new(Event $event) {
        $delegates = $event->delegates()->whereIsVerified(false)->get();

        return view("admin.events.delegates.new.index",
            compact('delegates', 'event'));
    }

    public function duplicates(Event $event) {
        $delegates = $event->delegates()
                           ->with('transactions.ticket')
                           ->whereIsDuplicated(DelegateDuplicationStatus::DUPLICATED)
                           ->get();

        return view("admin.events.delegates.duplicates.index",
            compact('delegates', 'event'));
    }

    public function exportNew(Event $event) {
        $event->load('delegates.transactions.ticket');
        $event->load('delegates.roles');

        return Excel::download(new NewDelegate($event),
            'new_delegates.xls');
    }

    public function getImportNew(Event $event) {

        return view('admin.events.delegates.new.import', compact('event'));
    }

    public function importNew(Event $event, Request $request) {

        $this->validate($request, [
            'file' => 'required|file|min:1'
        ]);

        $file = $request->file('file');

        $collection = Excel::toCollection(new NewDelegateImport(),
            $file);

        $this->dispatch(new UpdateNewDelegates($collection, $event));

        return redirect()->route("events.delegates.new", $event)
                         ->withStatus("delegates verified!");
    }

    public function template() {
        return response()->download(storage_path('app/templates/delegates_template.xlsx'));
    }
}
