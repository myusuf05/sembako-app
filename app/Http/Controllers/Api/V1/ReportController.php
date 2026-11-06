<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function summary()
    {
        $today = today();
        $omzetToday = Transactions::whereDate('created_at', $today)
            ->where('status', 'success')
            ->sum('total');
        $omzetMonth = Transactions::whereMonth('created_at', $today->month)
            ->whereYear('created_at', $today->year)
            ->where('status', 'success')
            ->sum('total');
        $totaltransactionsToday = Transactions::whereDate('created_at', $today)
            ->where('status', 'success')
            ->count();
        return response()->json([
            'success' => true,
            'message' => 'Summary retrieved successfully',
            'data' => [
                'omzet_today' => $omzetToday,
                'omzet_month' => $omzetMonth,
                'total_transactions_today' => $totaltransactionsToday,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function salesReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $sales = Transactions::with(['user', 'paymentMethod', 'transactionDetails.product'])
            ->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59'])
            ->where('status', 'success')
            ->get();
        $totalOmzet = $sales->sum('total');

        return response()->json([
            'success' => true,
            'filters' => [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ],
            'message' => 'Sales report retrieved successfully',
            'total_omzet' => $totalOmzet,
            'total_transactions' => $sales->count(),
            'data' => $sales,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
