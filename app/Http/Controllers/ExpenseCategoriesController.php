<?php

namespace App\Http\Controllers;

use App\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoriesController extends Controller
{
    /**
     * @var \App\Institution
     */
    private $repo;

    private $paginateNumber = 20;

    /**
     * InstitutionsController constructor.
     * @param \App\Institution $repo
     */
    public function __construct(ExpenseCategory $repo) {
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        $categories = ($keyword = $request->query("keywords")) ?
            $this->repo
                ->where('name', 'like', "%{$keyword}%")
                ->paginate($this->paginateNumber) :
            $this->repo
                ->paginate($this->paginateNumber);


        return view("admin.expenseCategories.index", compact("categories"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view("admin.expenseCategories.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ExpenseCategory $expenseCategory
     * @return \Illuminate\Http\Response
     */
    public function show(ExpenseCategory $expenseCategory) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ExpenseCategory $expenseCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(ExpenseCategory $expenseCategory) {
        return view("admin.expenseCategories.edit", compact("expenseCategory"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\ExpenseCategory     $expenseCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExpenseCategory $expenseCategory) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ExpenseCategory $expenseCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, ExpenseCategory $expenseCategory
    ) {
        $expenseCategory->delete();

        return $request->ajax() ?
            response()->json(['status' => 'completed']) :
            redirect()
                ->route('expense_categories.index')
                ->withStatus('Expense category deleted.');
    }
}
