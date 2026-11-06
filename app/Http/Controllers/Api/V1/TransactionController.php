<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Transactions;
use App\Models\product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transactions::with(['user', 'paymentMethod', 'transactionDetails.product']);
        if ($request->has('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        $transactions = $query->latest()->paginate(20);
        return response()->json(
            [
                'success' => true,
                'message' => 'Daftar transaksi berhasil diambil',
                'data' => $transactions,
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'nullable|exists:customers,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'paid_amount' => 'required|numeric|min:0',
            'transaction_details' => 'required|array|min:1',
            'transaction_details.*.product_id' => 'required|exists:products,id',
            'transaction_details.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Validasi data transaksi gagal',
                    'errors' => $validator->errors(),
                ],
                422
            );
        }

        $userId = $request->user()->id;
        DB::beginTransaction();

        try {
            $totalAmount = 0;
            $totalProfit = 0;
            $preparedDetails = [];

            foreach ($request->transaction_details as $detail) {
                $product = Product::lockForUpdate()->find($detail['product_id']);
                if ($product->stock < $detail['quantity']) {
                    DB::rollBack();
                    return response()->json(
                        [
                            'success' => false,
                            'message' => "Stok produk {$product->name} tidak mencukupi",
                        ],
                        400
                    );
                }

                $unitPrice = $product->sale_price;
                $costPrice = $product->purchase_price;
                $subtotal = $unitPrice * $detail['quantity'];
                $totalAmount += $subtotal;
                $totalProfit += ($unitPrice - $costPrice) * $detail['quantity'];
                $preparedDetails[] = [
                    'product_id' => $product->id,
                    'quantity' => $detail['quantity'],
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ];

                $product->stock -= $detail['quantity'];
                $product->save();
            }
            if ($request->paid_amount < $totalAmount) {
                DB::rollBack();
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Jumlah pembayaran tidak mencukupi',
                    ],
                    400
                );
            }
            $changeAmount = $request->paid_amount - $totalAmount;
            $invoiceNumber = 'INV/' . date('Ymd') . '/' . str_pad(Transactions::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            $transaction = Transactions::create([
                'user_id' => $userId,
                'customer_id' => $request->customer_id,
                'payment_method_id' => $request->payment_method_id,
                'total' => $totalAmount,
                'paid_amount' => $request->paid_amount,
                'change_amount' => $changeAmount,
                'invoice_number' => $invoiceNumber,
                'unit_price' => $request->unit_price,
                'subtotal' => $request->subtotal,
                'total_profit' => $totalProfit,
                'status' => 'success',
            ]);

            foreach ($preparedDetails as $detail) {
                $transaction->transactionDetails()->create($detail);
            }
            DB::commit();
            $transaction->load(['user', 'customer', 'paymentMethod', 'transactionDetails.product']);
            return response()->json(
                [
                    'success' => true,
                    'message' => 'Transaksi berhasil disimpan dan stok diperbarui',
                    'data' => $transaction,
                ],
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan transaksi: ' . $e->getMessage(),
                ],
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaction = Transactions::with(['user', 'customer', 'paymentMethod', 'transactionDetails.product'])->find($id);
        if (!$transaction) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan',
                ],
                404
            );
        }
        return response()->json(
            [
                'success' => true,
                'message' => 'Transaksi ditemukan',
                'data' => $transaction,
            ]
        );
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