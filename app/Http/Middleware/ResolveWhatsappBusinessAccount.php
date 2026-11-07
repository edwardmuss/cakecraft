<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WhatsappBusinessAccount;

class ResolveWhatsappBusinessAccount
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $branchId = Auth::user()->branch_id;

        /** ----------------------------------
         * 1️⃣ Resolve active WABA ID
         * ---------------------------------- */
        $wabaId = session('active_waba_id');

        $wabaQuery = WhatsappBusinessAccount::where('branch_id', $branchId)
            ->where('status', 'active');

        // If session has WABA → validate it
        if ($wabaId) {
            $waba = $wabaQuery->where('id', $wabaId)->first();

            if (!$waba) {
                session()->forget('active_waba_id');
            }
        }

        // Auto-pick first WABA if none selected / invalid
        if (!isset($waba)) {
            $waba = $wabaQuery->first();

            if ($waba) {
                session(['active_waba_id' => $waba->id]);
            }
        }

        /** ----------------------------------
         * 2️⃣ No WABA exists → redirect to setup
         * ---------------------------------- */
        if (!$waba) {
            return redirect()
                ->route('whatsapp.setup')
                ->with('warning', 'Please connect a WhatsApp Business Account first.');
        }

        /** ----------------------------------
         * 3️⃣ Bind globally (VERY IMPORTANT)
         * ---------------------------------- */
        app()->instance('activeWaba', $waba);

        return $next($request);
    }
}
