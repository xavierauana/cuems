<?php

namespace App\Http\Controllers;

use App\Delegate;
use App\Enums\SystemEvents;
use App\Enums\TransactionStatus;
use App\Event;
use App\Events\SystemEvent;
use App\Exports\DelegateExport;
use App\Jobs\ImportDelegates;
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
        $delegates = $event->delegates;

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

        $delegates = $event->delegates()->where(array_keys($input)[0], '=',
            array_values($input)[0])->get();

        return response()->json($delegates);
    }

    public function export(Event $event) {
        $event->load('delegates.transactions.ticket');
        $event->load('delegates.roles');

        return Excel::download(new DelegateExport($event), 'delegates.xls');
    }
}
