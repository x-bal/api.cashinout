<?php

namespace App\Http\Controllers;

use App\Http\Resources\CashResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CashController extends Controller
{

    public function index()
    {
        $transactions = Auth::user()->cashes()->whereBetween('when', [now()->firstOfMonth(), now()])->latest()->get();

        $debit = Auth::user()->cashes()
            ->whereBetween('when', [now()->firstOfMonth(), now()])
            ->where('amount',  '>=', 0)
            ->get('amount')->sum('amount');

        $credit = Auth::user()->cashes()
            ->whereBetween('when', [now()->firstOfMonth(), now()])
            ->where('amount',  '<', 0)
            ->get('amount')->sum('amount');

        $balances = Auth::user()->cashes()->get('amount')->sum('amount');

        return response()->json([
            'debit' => formatPrice($debit),
            'credit' => formatPrice($credit),
            'balances' => formatPrice($balances),
            'transactions' => CashResource::collection($transactions)
        ]);
    }

    public function store()
    {
        request()->validate([
            'name' => 'required',
            'amount' => 'required|numeric',
        ]);

        $when = request('when') ?? now();
        $slug = Str::slug(request('name') . "-" . Str::random(8));

        Auth::user()->cashes()->create([
            'name' => request('name'),
            'slug' => $slug,
            'when' => $when,
            'amount' => request('amount'),
            'description' => request('description')
        ]);

        return response()->json([
            'message' => 'Transaction has been saved.'
        ]);
    }
}
