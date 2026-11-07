<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\OrderRequest;
use App\Models\Payment;
use App\Services\OccasionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('view financials')) {
            return redirect()->route('invoices.index');
        }

        $branchId = Auth::user()->branch_id;
        $today = Carbon::today();
        $monthStart = $today->copy()->startOfMonth();

        $open = Invoice::where('branch_id', $branchId)->where('status', '!=', 'cancelled');

        $kpi = [
            'sales_month' => (clone $open)->where('created_at', '>=', $monthStart)->sum('total_amount'),
            'received_month' => Payment::where('branch_id', $branchId)->where('status', 'completed')->where('created_at', '>=', $monthStart)->sum('amount'),
            'receivables' => (clone $open)->whereColumn('total_amount', '>', 'amount_paid')->sum(DB::raw('total_amount - amount_paid')),
            'overdue_count' => (clone $open)->whereColumn('total_amount', '>', 'amount_paid')->whereNotNull('due_date')->whereDate('due_date', '<', $today)->count(),
            'new_web_orders' => OrderRequest::where('branch_id', $branchId)->where('status', 'new')->count(),
        ];

        // Production list: cakes needed over the next 7 days
        $dueSoon = Invoice::with(['customer', 'items'])
            ->where('branch_id', $branchId)
            ->whereNotIn('status', ['cancelled', 'delivered'])
            ->whereNotNull('occasion_date')
            ->whereBetween('occasion_date', [$today->toDateString(), $today->copy()->addDays(7)->toDateString()])
            ->orderBy('occasion_date')->get();

        $overdue = Invoice::with('customer')->where('branch_id', $branchId)->where('status', '!=', 'cancelled')
            ->whereColumn('total_amount', '>', 'amount_paid')->whereNotNull('due_date')->whereDate('due_date', '<', $today)
            ->orderBy('due_date')->take(6)->get();

        $webOrders = OrderRequest::where('branch_id', $branchId)->where('status', 'new')->latest()->take(5)->get();

        $occasions = app(OccasionService::class)->upcoming($branchId, 14, false)->take(6);

        // Sales vs money received, last 6 months
        $months = collect(range(5, 0))->map(fn ($i) => $today->copy()->subMonths($i)->startOfMonth());
        $sales = $months->map(fn ($m) => (float) Invoice::where('branch_id', $branchId)->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$m, $m->copy()->endOfMonth()])->sum('total_amount'));
        $received = $months->map(fn ($m) => (float) Payment::where('branch_id', $branchId)->where('status', 'completed')
            ->whereBetween('created_at', [$m, $m->copy()->endOfMonth()])->sum('amount'));
        $chart = ['labels' => $months->map->format('M')->all(), 'sales' => $sales->all(), 'received' => $received->all()];

        // Best selling cakes this year
        $topCakes = DB::table('invoice_items')
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->where('invoices.branch_id', $branchId)->where('invoices.status', '!=', 'cancelled')
            ->whereYear('invoices.created_at', $today->year)
            ->select('invoice_items.item_name', DB::raw('SUM(invoice_items.quantity) as qty'), DB::raw('SUM(invoice_items.total) as revenue'))
            ->groupBy('invoice_items.item_name')->orderByDesc('revenue')->limit(5)->get();

        return view('dashboard.cake', compact('kpi', 'dueSoon', 'overdue', 'webOrders', 'occasions', 'chart', 'topCakes'));
    }
}
