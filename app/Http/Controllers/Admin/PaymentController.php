<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = Payment::with(['booking.user', 'booking.package'])->latest()->paginate(10);

        return view('admin.payments.index', compact('payments'));
    }

    public function edit(Payment $payment): View
    {
        $payment->load(['booking.user']);

        return view('admin.payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:unpaid,paid,refunded'],
            'reference_number' => ['nullable', 'string', 'max:255'],
        ]);

        if ($data['status'] === 'paid' && ! $payment->paid_at) {
            $data['paid_at'] = now();
        }

        if ($data['status'] === 'unpaid') {
            $data['paid_at'] = null;
        }

        $payment->update($data);

        if ($payment->status === 'paid') {
            $payment->booking->update([
                'status' => 'confirmed',
            ]);
        }

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment updated successfully.');
    }
}
