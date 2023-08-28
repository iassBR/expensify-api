<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExpenseCollection;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{

    protected $ids = [];
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = isset($request->per_page) ? $request->per_page : 100;

        return new ExpenseCollection(Expense::filter($request->all())->orderByDesc('value')->paginate($perPage));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(array $data): Expense
    {
        return Expense::create($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Expense
    {
        return Expense::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(array $data, string $id): bool
    {
        $expense = Expense::findOrFail($id);

        $this->syncProductWithInstallments($expense, $data);

        return $expense->update($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $expense = Expense::findOrFail($id);

        $expense->delete();
    }

    public function syncExpenses(Request $request): void
    {
        $expenses = $request->expenses;


        DB::transaction(function () use ($expenses) {
            foreach ($expenses as $expenseData) {

                if (isset($expenseData['id']) && isset($expenseData['delete'])) {
                    $this->ids[] = $expenseData['id'];
                    continue;
                }

                if (isset($expenseData['id'])) {
                    unset($expenseData['isEditingData']);

                    $this->update($expenseData, $expenseData['id']);
                } else {
                    $this->store($expenseData);
                }
            }

            $this->performRemoval();
        });
    }

    protected function performRemoval()
    {
        foreach ($this->ids as $id) {
            $this->destroy($id);
        }
    }

    protected function syncProductWithInstallments(Expense $expense, array $data): void
    {
        if (!$data['is_installment']) {
            return;
        }

        if (count($expense->installments) > 0) {
            return;
        }

        $start_at = Carbon::createFromFormat('Y-m-d', $data['start_installment_at']);
        $end_at = Carbon::createFromFormat('Y-m-d', $data['end_installment_at']);

        $diff = $end_at->diffInMonths($start_at);

        for ($i = 0; $i < $diff; $i++) {
            $installmentChieldExpenseData = [
                'name' => $data['name'],
                'description' => 'Installment ' . $i + 1 . ' of ' . $diff,
                'value' => $data['value'],
                'parent_id' => $data['id'],
                'category' => $data['category'],
                'type' => $data['type'],
                'user_id' => $data['user_id'],
            ];

            $this->store($installmentChieldExpenseData);
        }
    }
}
