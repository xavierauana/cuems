<?php

namespace App\Http\Controllers;

use App\Event;
use App\Expense;
use App\ExpenseCategory;
use App\Vendor;
use Illuminate\Http\Request;

class ExpensesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Event $event
     * @return \Illuminate\Http\Response
     */
    public function index(Event $event) {
        $event->load('expenses');

        return view("admin.events.expenses.index", compact("event"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Event $event
     * @return \Illuminate\Http\Response
     */
    public function create(Event $event) {

        $categories = ExpenseCategory::pluck('name', 'id');
        $vendors = Vendor::pluck('name', 'id');

        return view("admin.events.expenses.create",
            compact('event', 'categories', 'vendors'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Event               $event
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Event $event) {
        $validatedData = $this->validate($request, [
            "amount"      => "required",
            "vendor_id"   => "required",
            "category_id" => "required",
            "note"        => "nullable",
            "files"       => "nullable"
        ]);

        if (!Vendor::find($validatedData['vendor_id'])) {
            $newVendor = Vendor::create([
                'name' => $validatedData['vendor_id']
            ]);
            $validatedData['vendor_id'] = $newVendor->id;

        }
        if (!ExpenseCategory::find($validatedData['category_id'])) {
            $newCategory = ExpenseCategory::create([
                'name' => $validatedData['category_id']
            ]);
            $validatedData['category_id'] = $newCategory->id;
        }

        $newExpenses = $event->expenses()->create($validatedData);

        if ($validatedData['files']) {
            foreach ($validatedData['files'] as $path) {
                $newExpenses->files()->create([
                    'path' => $path
                ]);
            }
        }

        return redirect()->route('events.expenses.index', compact('event'))
                         ->withStatus('New expense created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Event  $event
     * @param \App\Expense $expense
     * @return void
     */
    public function show(Event $event, Expense $expense) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Event  $event
     * @param \App\Expense $expense
     * @return void
     */
    public function edit(Event $event, Expense $expense) {
        $expense->load(['files', 'category', 'vendor']);
        $categories = ExpenseCategory::pluck('name', 'id');
        $vendors = Vendor::pluck('name', 'id');

        return view("admin.events.expenses.edit",
            compact('event', 'expense', 'categories', 'vendors'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param \App\Event                $event
     * @param \App\Expense              $expense
     * @return void
     */
    public function update(Request $request, Event $event, Expense $expense
    ) {
        $expense = $event->expenses()->findOrFail($expense->id);

        $validatedData = $this->validate($request, [
            "amount"      => "required",
            "vendor_id"   => "required",
            "category_id" => "required",
            "note"        => "nullable",
            "files"       => "nullable"
        ]);

        if (!Vendor::find($validatedData['vendor_id'])) {
            $newVendor = Vendor::create([
                'name' => $validatedData['vendor_id']
            ]);
            $validatedData['vendor_id'] = $newVendor->id;

        }
        if (!ExpenseCategory::find($validatedData['category_id'])) {
            $newCategory = ExpenseCategory::create([
                'name' => $validatedData['category_id']
            ]);
            $validatedData['category_id'] = $newCategory->id;
        }

        $expense->updated($validatedData);

        if ($validatedData['files']) {
            foreach ($validatedData['files'] as $path) {
                $expense->files()->create([
                    'path' => $path
                ]);
            }
        }

        return redirect()->route('events.expenses.index', compact('event'))
                         ->withStatus('Expense updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \App\Event              $event
     * @param \App\Expense             $expense
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, Event $event, Expense $expense) {
        if ($expense = $event->expenses()->find($expense->id)) {
            $expense->delete();
        }

        return $request->ajax() ?
            response()->json(['status' => 'completed']) :
            redirect()
                ->route('events.expenses.index', $event)
                ->withStatus('Institution deleted.');
    }
}
