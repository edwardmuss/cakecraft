<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $branchId = $request->user()->branch_id;

        // --- Current Month Setup ---
        $currentYear = \Carbon\Carbon::now()->year;
        $currentMonth = \Carbon\Carbon::now()->month;
        $currentMonthStr = \Carbon\Carbon::now()->format('Y-m');

        // --- Base Query: ALL active invoices for this month ---
        $thisMonthInvoices = Invoice::where('branch_id', $branchId)
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($currentMonthStr, $currentYear, $currentMonth) {
                $q->where('communion_month', $currentMonthStr)
                    ->orWhere(function ($subQ) use ($currentYear, $currentMonth) {
                        $subQ->whereYear(DB::raw('COALESCE(communion_date, due_date)'), $currentYear)
                            ->whereMonth(DB::raw('COALESCE(communion_date, due_date)'), $currentMonth);
                    });
            });

        // TOTAL REVENUE (What was BILLED this month)
        $totalRevenue = (clone $thisMonthInvoices)->sum('total_amount');

        // OUTSTANDING BALANCE (What is UNPAID from this month's billing)
        $outstandingBalance = (clone $thisMonthInvoices)
            ->whereColumn('total_amount', '>', 'amount_paid')
            ->sum(DB::raw('total_amount - amount_paid'));

        // QUICK COUNTS (Only for this month)
        $totalCustomers = Customer::where('branch_id', $branchId)
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->count();

        $totalInvoices = (clone $thisMonthInvoices)->count();

        // Invoice Status Breakdown (Keep this all-time or active only, usually best to leave as is to see all pending logistics)
        $statusCounts = Invoice::where('branch_id', $branchId)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status')->toArray();

        $chartStatuses = [
            'Processing' => $statusCounts['processing'] ?? 0,
            'Partially Paid' => $statusCounts['partially_paid'] ?? 0,
            'Paid' => $statusCounts['paid'] ?? 0,
            'Packed' => $statusCounts['packed'] ?? 0,
            'Dispatched' => $statusCounts['dispatched'] ?? 0,
            'Delivered' => $statusCounts['delivered'] ?? 0,
            'Cancelled' => $statusCounts['cancelled'] ?? 0,
        ];

        // --- 5-Week Rolling Sales for Mobile ---
        $chartLabels = [];
        $weeklySales = [];

        for ($i = 4; $i >= 0; $i--) {
            $weekDate = \Carbon\Carbon::today()->subWeeks($i)->endOfWeek();
            $month = $weekDate->format('M');
            $weekOfMonth = ceil($weekDate->day / 7);
            $weekOfMonth = $weekOfMonth > 5 ? 5 : $weekOfMonth;
            $label = "{$month} W{$weekOfMonth}";

            $chartLabels[] = $label;
            $weeklySales[$label] = 0;
        }

        $startDate = \Carbon\Carbon::today()->subWeeks(4)->startOfWeek();
        $twoMonthsAgoStr = \Carbon\Carbon::today()->subMonths(2)->format('Y-m');

        $recentInvoices = Invoice::where('branch_id', $branchId)
            ->where(function ($q) use ($startDate, $twoMonthsAgoStr) {
                $q->where('communion_month', '>=', $twoMonthsAgoStr)
                    ->orWhere(DB::raw('COALESCE(communion_date, due_date)'), '>=', $startDate);
            })
            ->whereNotIn('status', ['cancelled'])
            ->get();

        foreach ($recentInvoices as $invoice) {
            $label = null;

            if ($invoice->communion_month && $invoice->communion_week) {
                $monthStr = \Carbon\Carbon::createFromFormat('Y-m', $invoice->communion_month)->format('M');
                $label = "{$monthStr} W{$invoice->communion_week}";
            } else {
                $targetDate = $invoice->communion_date ?? $invoice->due_date;
                if ($targetDate) {
                    $invDate = \Carbon\Carbon::parse($targetDate);
                    $monthStr = $invDate->format('M');
                    $weekOfMonth = ceil($invDate->day / 7);
                    $weekOfMonth = $weekOfMonth > 5 ? 5 : $weekOfMonth;
                    $label = "{$monthStr} W{$weekOfMonth}";
                }
            }

            if ($label && array_key_exists($label, $weeklySales)) {
                $weeklySales[$label] += $invoice->total_amount;
            }
        }

        return response()->json([
            'stats' => [
                'total_revenue' => $totalRevenue,
                'outstanding_balance' => $outstandingBalance,
                'total_customers' => $totalCustomers,
                'total_invoices' => $totalInvoices,
            ],
            'statuses' => $chartStatuses,
            'weekly_sales' => [
                'labels' => array_keys($weeklySales),
                'data' => array_values($weeklySales)
            ]
        ]);
    }
}
