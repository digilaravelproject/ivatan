<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AdminInvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Invoice::with(['user:id,name,email', 'plan', 'subscription']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $invoices = $query->latest()->paginate(20)->withQueryString();

        $summary = [
            'total' => Invoice::count(),
            'paid' => Invoice::where('status', 'paid')->count(),
            'pending' => Invoice::where('status', 'pending')->count(),
            'total_revenue' => Invoice::where('status', 'paid')->sum('amount'),
            'overdue' => Invoice::where('status', 'pending')
                ->where('due_date', '<', now())
                ->count(),
        ];

        return view('admin.invoices.index', compact('invoices', 'summary'));
    }

    public function show(int $id): View
    {
        $invoice = Invoice::with([
            'user:id,name,email,username,phone',
            'plan',
            'subscription',
        ])->findOrFail($id);

        return view('admin.invoices.show', compact('invoice'));
    }

    public function resend(int $id): RedirectResponse
    {
        $invoice = Invoice::with('user')->findOrFail($id);

        Log::info('Invoice resend requested (mail driver not configured)', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'user_email' => $invoice->user?->email,
        ]);

        return redirect()->back()->with('success', "Invoice {$invoice->invoice_number} marked for resend. Email dispatch is pending mail driver setup.");
    }
}
