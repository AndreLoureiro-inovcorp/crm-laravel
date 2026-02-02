<?php

namespace App\Http\Controllers;

use App\Models\DealProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProductStatsController extends Controller
{
    /**
     * Display product statistics.
     */
    public function index(Request $request)
    {
        $query = DealProduct::query()
            ->join('deals', 'deal_products.deal_id', '=', 'deals.id')
            ->where('deals.tenant_id', auth()->user()->tenant_id)
            ->select(
                'deal_products.product_name',
                DB::raw('SUM(deal_products.quantity) as total_quantity'),
                DB::raw('SUM(deal_products.total_price) as total_value'),
                DB::raw('COUNT(DISTINCT deal_products.deal_id) as deal_count')
            )
            ->groupBy('deal_products.product_name');

        if ($request->stage) {
            $query->where('deals.stage', $request->stage);
        }

        if ($request->owner_id) {
            $query->where('deals.owner_id', $request->owner_id);
        }

        if ($request->start_date) {
            $query->where('deals.created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('deals.created_at', '<=', $request->end_date);
        }

        $products = $query->orderBy('total_value', 'desc')->get();

        return Inertia::render('Products/Stats', [
            'products' => $products,
            'filters' => $request->only(['stage', 'owner_id', 'start_date', 'end_date']),
        ]);
    }

    /**
     * Show deals for a specific product.
     */
    public function show(Request $request, string $productName)
    {
        $deals = DealProduct::query()
            ->join('deals', 'deal_products.deal_id', '=', 'deals.id')
            ->where('deals.tenant_id', auth()->user()->tenant_id)
            ->where('deal_products.product_name', $productName)
            ->with(['deal.entity', 'deal.person', 'deal.owner'])
            ->select('deal_products.*', 'deals.title as deal_title', 'deals.stage', 'deals.value as deal_value')
            ->get();

        return Inertia::render('Products/Show', [
            'product_name' => $productName,
            'deals' => $deals,
        ]);
    }

    /**
     * Export product statistics to CSV.
     */
    public function export(Request $request)
    {
        $query = DealProduct::query()
            ->join('deals', 'deal_products.deal_id', '=', 'deals.id')
            ->where('deals.tenant_id', auth()->user()->tenant_id)
            ->select(
                'deal_products.product_name',
                DB::raw('SUM(deal_products.quantity) as total_quantity'),
                DB::raw('SUM(deal_products.total_price) as total_value'),
                DB::raw('COUNT(DISTINCT deal_products.deal_id) as deal_count')
            )
            ->groupBy('deal_products.product_name');

        if ($request->stage) {
            $query->where('deals.stage', $request->stage);
        }

        if ($request->owner_id) {
            $query->where('deals.owner_id', $request->owner_id);
        }

        if ($request->start_date) {
            $query->where('deals.created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('deals.created_at', '<=', $request->end_date);
        }

        $products = $query->orderBy('total_value', 'desc')->get();

        $csv = "Produto,Quantidade Total,Valor Total,Número de Negócios\n";
        foreach ($products as $product) {
            $csv .= "\"{$product->product_name}\",{$product->total_quantity},{$product->total_value},{$product->deal_count}\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="estatisticas-produtos.csv"',
        ]);
    }
}
