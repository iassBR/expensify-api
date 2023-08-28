<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $incomeMonth = Income::whereMonth('created_at', now()->format('m'))->get()->sum('value');

        $essentialSpending = $this->calculateSpendingBasedOnPercentual($incomeMonth, 50);
        $superfluousSpending = $this->calculateSpendingBasedOnPercentual($incomeMonth, 30);
        $investmentingAndEmergency = $this->calculateSpendingBasedOnPercentual($incomeMonth, 20);

        $essentialSpended = Expense::whereMonth('created_at', now()->format('m'))->where('type', 'essential')->get()->sum('value');
        $superfluousSpended = Expense::whereMonth('created_at', now()->format('m'))->where('type', 'superfluous')->get()->sum('value');
        $investSpended = Expense::whereMonth('created_at', now()->format('m'))->where('type', 'investment')->get()->sum('value');
        $emergencySpended = Expense::whereMonth('created_at', now()->format('m'))->where('type', 'emergency')->get()->sum('value');

        $totalExpended = $essentialSpended + $superfluousSpended + $investSpended + $emergencySpended;
        $expenses = [
            ['totalIncoming' => number_format($incomeMonth, 2, '.')],
            ['totalSpended' => number_format($totalExpended, 2, '.')],
            ['essential' => ['toSpend' => $essentialSpending, 'spended' => number_format($essentialSpended, 2, '.')]],
            ['superfluous' => ['toSpend' => $superfluousSpending, 'spended' => number_format($superfluousSpended, 2, '.')]],
            ['investmentingAndEmergency' => ['toSpend' => $investmentingAndEmergency, 'spended' => $investSpended + $emergencySpended]]
        ];

        return response()->json(['expenses' => $expenses]);
    }

    private function calculateSpendingBasedOnPercentual($total, $percentual)
    {
        $value = ($percentual * $total) / 100;

        return number_format($value, 2, '.');
    }
}
