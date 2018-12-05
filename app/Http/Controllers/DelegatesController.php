<?php

namespace App\Http\Controllers;

use App\Delegate;
use App\Enums\DelegateDuplicationStatus;
use App\Enums\SystemEvents;
use App\Enums\TransactionStatus;
use App\Event;
use App\Events\SystemEvent;
use App\Exports\DelegateExport;
use App\Exports\NewDelegate;
use App\Imports\NewDelegateImport;
use App\Jobs\ImportDelegates;
use App\Jobs\UpdateNewDelegates;
use App\Services\DelegateDuplicateChecker;
use App\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
     * @return \Illuminate\Http\Response
     */
    public function index(Event $event) {
        $delegates = $event->delegates()
                           ->whereIsDuplicated(DelegateDuplicationStatus::NO)
                           ->orderBy('last_name')
                           ->paginate();

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
     * @param \App\Event                $event
     * @param  \Illuminate\Http\Request $request
     * @param \App\Transaction          $transaction
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(
        Event $event, Request $request, Transaction $transaction
    ): RedirectResponse {

        $rules = array_merge($this->repo->getStoreRules(),
            $transaction->getRules());

        $validatedData = $this->validate($request, $rules);

        DB::beginTransaction();

        try {

            $newDelegate = $this->createDelegate($event, $request,
                $validatedData);

            $newDelegate->is_verified = true;
            $newDelegate->is_duplicated = DelegateDuplicationStatus::NO;

            $newDelegate->save();

            DB::commit();

            event(new SystemEvent(SystemEvents::ADMIN_CREATE_DELEGATE,
                $newDelegate));

        } catch (\Exception $exception) {

            DB::rollBack();

            throw $exception;
        }

        return redirect()->route("events.delegates.index", $event);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Delegate $delegate
     * @return \Illuminate\Http\Response
     */
    public function show(Delegate $delegate) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Delegate $delegate
     * @return \Illuminate\Http\Response
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
     * @param  \Illuminate\Http\Request $request
     * @param \App\Event                $event
     * @param  \App\Delegate            $delegate
     * @param \App\Transaction          $transaction
     * @return \Illuminate\Http\Response
     * @throws \ReflectionException
     */
    public function update(
        Request $request, Event $event, Delegate $delegate,
        Transaction $transaction
    ) {
        $rules = array_merge($delegate->getStoreRules(),
            $transaction->getRules());

        $validatedData = $this->validate($request, $rules);

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
     * @param  \App\Delegate $delegate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Delegate $delegate) {
        //
    }

    /**
     *  Mass import delegates
     *
     * @param \App\Event               $event
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Event $event, Request $request): RedirectResponse {
        $this->validate($request, [
            'file' => 'required|file|min:0'
        ]);

        $file = $request->file('file');

        $filePath = $file->move("../storage/temp",
            $file->getClientOriginalName());

        $job = new ImportDelegates($event, $filePath);

        $this->dispatch($job);

        return redirect()->route('events.tickets.index', $event);
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
        $newDelegate = $event->delegates()->create($validatedData);

        $newDelegate->transactions()->create($validatedData);

        $newDelegate->roles()->sync($validatedData['roles_id']);

        DB::table('delegate_creations')->insert([
            'delegate_id' => $newDelegate->id,
            'user_id'     => $request->user()->id
        ]);

        return $newDelegate;
    }

    /**
     * @param \App\Delegate $delegate
     * @param array         $validatedData
     * @return \App\Delegate
     */
    private function updateDelegate(Delegate $delegate, array $validatedData
    ): Delegate {
        $delegate->update($validatedData);

        $delegate->transactions()->latest()->first()->update($validatedData);

        $delegate->roles()->sync($validatedData['roles_id']);

        return $delegate;
    }

    public function postSearch(Request $request, Event $event) {

        $input = $request->all();

        $delegates = collect([]);

        if (array_keys($input)[0] and array_values($input)[0]) {
            $checker = new DelegateDuplicateChecker($event);
            $delegates = $checker->find(array_keys($input)[0],
                array_values($input)[0]);
        }


        return response()->json($delegates);
    }

    public function export(Event $event) {
        $event->load('delegates.transactions.ticket');
        $event->load('delegates.roles');

        return Excel::download(new DelegateExport($event), 'delegates.xls');
    }

    public function new(Event $event) {
        $delegates = $event->delegates()->whereIsVerified(false)->get();

        return view("admin.events.delegates.new.index",
            compact('delegates', 'event'));
    }

    public function duplicates(Event $event) {
        $delegates = $event->delegates()
                           ->whereIsDuplicated(DelegateDuplicationStatus::DUPLICATED)
                           ->get();

        return view("admin.events.delegates.duplicates.index",
            compact('delegates', 'event'));
    }

    public function exportNew(Event $event) {
        $event->load('delegates.transactions.ticket');
        $event->load('delegates.roles');

        return Excel::download(new NewDelegate($event), 'new_delegates.xls');
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
}
