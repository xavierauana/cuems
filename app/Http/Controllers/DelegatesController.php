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
use App\Http\Requests\StoreDelegateRequest;
use App\Imports\DelegatesImport;
use App\Imports\NewDelegateImport;
use App\Jobs\ImportDelegates;
use App\Jobs\UpdateNewDelegates;
use App\PaymentRecord;
use App\Services\DelegateCreationService;
use App\Services\ImportDelegateService;
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
     * @param \App\Event               $event
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Event $event, Request $request) {
        $query = $event->delegates()
                       ->with('event')
                       ->where("is_duplicated",
                           "<>", DelegateDuplicationStatus::DUPLICATED)
                       ->search($request->query('keyword'), [$event])
                       ->orderBy($request->get('orderBy', 'registration_id'),
                           $request->get('order', 'dec'));

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
     * @param \App\Event                              $event
     * @param \App\Http\Requests\StoreDelegateRequest $request
     * @param \App\Services\DelegateCreationService   $service
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(
        Event $event, StoreDelegateRequest $request,
        DelegateCreationService $service
    ): RedirectResponse {

        $validatedData = $request->validated();

        if ($record = $this->isFailedPaymentConverted($request)) {
            $newDelegate = $service->adminCreateWithFailedTransaction($event,
                $validatedData, $record);
        } else {
            $newDelegate = $service->adminCreate($event, $validatedData);
        }

        event(new SystemEvent(SystemEvents::ADMIN_CREATE_DELEGATE,
            $newDelegate));

        return redirect()->route("events.delegates.index", $event);

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Event    $event
     * @param \App\Delegate $delegate
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event, Delegate $delegate) {
        $this->abortIfNotEventSubEntity($delegate, $event);
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


        if ($delegate->duplicated_with) {
            $duplicate = $event->delegates()
                               ->where('registration_id',
                                   $checker->convertRegistrationIdToInt($delegate->duplicated_with))
                               ->first();
            $duplicates->push($duplicate);
        }


        $duplicates = $duplicates->reject(null)->unique('id');

        return view("admin.events.delegates.show",
            compact('event', 'delegate', 'duplicates'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Event    $event
     * @param \App\Delegate $delegate
     * @return \Illuminate\Http\Response
     * @throws \ReflectionException
     */
    public function edit(Event $event, Delegate $delegate) {
        $this->abortIfNotEventSubEntity($delegate, $event);

        $delegate->load(['transactions.ticket', 'transactions.payee']);
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
     * @param \App\Delegate                            $delegate
     * @param \App\Transaction                         $transaction
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function update(
        DelegateUpdateRequest $request, Event $event, Delegate $delegate,
        Transaction $transaction
    ) {
        $this->abortIfNotEventSubEntity($delegate, $event);

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
     * @param \App\Event    $event
     * @param \App\Delegate $delegate
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Event $event, Delegate $delegate
    ) {
        $this->abortIfNotEventSubEntity($delegate, $event);

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
        Event $event, Request $request, ImportDelegateService $service
    ): RedirectResponse {
        $this->validate($request, [
            'file' => 'required|file|min:0'
        ]);

        $collection = Excel::toCollection(new DelegatesImport,
            $request->file('file'));

        ImportDelegates::dispatch($event, $collection->first(),
            $request->user());

        return redirect()->route('events.delegates.new', $event);
    }

    /**
     * @param \App\Event               $event
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getImport(Event $event, Request $request) {
        return view("admin.events.delegates.import", compact('event'));
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
        $validatedData['institution'] = $validatedData['institution'] == "Others" ? $validatedData['other_institution'] : $validatedData['institution'];
        $validatedData['position'] = $validatedData['position'] == "Others" ? $validatedData['other_position'] : $validatedData['position'];
        $validatedData['training_organisation'] = isset($validatedData['training_organisation']) and $validatedData['training_organisation'] == "Others" ?
            ($validatedData['training_other_organisation'] ?? null) :
            ($validatedData['training_organisation'] ?? null);

        $delegate->update($validatedData);

        $delegate->transactions()->latest()->first()
                 ->update($validatedData);

        $delegate->roles()->sync($validatedData['roles_id']);

        if (isset($validatedData['sponsor']) and isset($validatedData['sponsor']['sponsor_id'])) {
            $sponsorData = $validatedData['sponsor'];

            $delegate->sponsorRecord()
                     ->updateOrCreate(['delegate_id' => $delegate->id],
                         $sponsorData);
        } elseif ($delegate->sponsorRecord) {
            $delegate->sponsorRecord->delete();
        }

        return $delegate;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Event               $event
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function search(Request $request, Event $event) {

        if ($keyword = $request->query('keyword')) {
            $delegates = $event->delegates()
                               ->paginate();

            return view("admin.events.delegates.index",
                compact('delegates', 'event'));
        }

        return redirect()->route('events.delegates.index', $event);

    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Event               $event
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
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

    /**
     * @param \App\Event $event
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function new(Event $event) {
        $delegates = $event->delegates()
                           ->with('event')
                           ->whereIsVerified(false)
                           ->orderBy('registration_id', 'desc')
                           ->get();

        return view("admin.events.delegates.new.index",
            compact('delegates', 'event'));
    }

    /**
     * @param \App\Event $event
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function duplicates(Event $event) {
        $delegates = $event->delegates()
                           ->with('transactions.ticket')
                           ->whereIsDuplicated(DelegateDuplicationStatus::DUPLICATED)
                           ->orderBy('registration_id', 'desc')
                           ->get();

        return view("admin.events.delegates.duplicates.index",
            compact('delegates', 'event'));
    }

    /**
     * @param \App\Event $event
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportNew(Event $event) {
        $event->load('delegates.transactions.ticket');
        $event->load('delegates.roles');

        return Excel::download(new NewDelegate($event),
            'new_delegates.xls');
    }

    /**
     * @param \App\Event $event
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getImportNew(Event $event) {

        return view('admin.events.delegates.new.import', compact('event'));
    }

    /**
     * @param \App\Event               $event
     * @param \Illuminate\Http\Request $request
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
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

    /**
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function template() {
        return response()->download(storage_path('app/templates/delegates_template.xlsx'));
    }

    /**
     * @param \App\Transaction $transaction
     * @return array
     * @throws \ReflectionException
     */
    public function getStoreValidationRules(): array {
        return array_merge($this->repo->getStoreRules(),
            (new Transaction)->getRules());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchDuplicate() {
        if ($email = request('email')) {
            $delegates = Delegate::whereEmail($email)->get();

            return response()->json($delegates);
        }

        if ($mobile = request('mobile')) {
            $delegates = Delegate::whereMobile($mobile)->get();

            return response()->json($delegates);
        }
    }

    /**
     * @param \App\Event $event
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function sponsored(Event $event, Request $request) {

        $delegates = $event->delegates()->sponsored()
                           ->orderBy($request->get('orderBy',
                               'registration_id'),
                               $request->get('order', 'desc'))
                           ->paginate();

        return view("admin.events.delegates.sponsored",
            compact('delegates', 'event'));
    }

    /**
     * @param \App\Event               $event
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function waived(Event $event, Request $request) {

        $delegates = $event->delegates()->waived()
                           ->orderBy($request->get('orderBy',
                               'registration_id'),
                               $request->get('order', 'desc'))
                           ->paginate();

        return view("admin.events.delegates.waived",
            compact('delegates', 'event'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return null
     */
    private function isFailedPaymentConverted(Request $request) {
        if ($recordId = $request->query('conversion') and $record = PaymentRecord::findOrFail($recordId)) {
            return $record;
        }

        return null;
    }

}
