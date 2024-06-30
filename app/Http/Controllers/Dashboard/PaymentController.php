<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('nocache'); // Apply nocache middleware to all methods in this controller
    }

    public function showPaymentForm($orderItemId)
    {
        $orderItem = OrderItem::findOrFail($orderItemId);

        if ($orderItem->status === 'paid' || $orderItem->status === 'cod') {
            return redirect()->route('dashboard.order-items.index')
                ->with('error', 'This order has already been paid.');
        }

        $totalPrice = $orderItem->unit_price * $orderItem->quantity;

        return view('dashboard.order-item.payment', compact('orderItem', 'totalPrice'));
        // $orderItem = OrderItem::findOrFail($orderItemId);
        // $totalPrice = $orderItem->unit_price * $orderItem->quantity;

        // return view('dashboard.order-item.payment', compact('orderItem', 'totalPrice'));
    }
    
    public function processPayment(Request $request, $orderItemId)
    {
        // Custom validation messages
        $messages = [
            'gcash_number.required' => 'Please enter your GCash number.',
            'gcash_number.digits' => 'GCash number must be exactly 11 digits.',
            'reference_number.required' => 'Please enter a reference number.',
        ];

        // Validate common fields
        $request->validate([
            'payment_method' => 'required|in:Card,GCash,cod',
        ]);

        // Validate reference number for card and GCash payments
        if (in_array($request->payment_method, ['Card', 'GCash'])) {
            $request->validate([
                'reference_number' => 'required|string',
            ], $messages);
        }

        // Validate GCash number if payment method is GCash
        // if ($request->payment_method === 'GCash') {
        //     $request->validate([
        //         'gcash_number' => 'required|digits:11',
        //     ], $messages);
        // }

        try {
            $orderItem = OrderItem::findOrFail($orderItemId);
        
            // Validate GCash number if payment method is GCash
            if ($request->payment_method === 'GCash') {
                $validator = Validator::make($request->all(), [
                    'gcash_number' => 'required|digits:11',
                ], $messages);
        
                if (!$validator->fails()) {
                    // Proceed with simulated payment processing
                    $paymentSuccessful = $this->simulatePayment($request->all());
        
                    if ($paymentSuccessful) {
                        // Save payment details and update order status
                        $gcashNumber = $request->payment_method == 'GCash'? $request->gcash_number : null;
                        $this->savePaymentDetails($orderItem, $request->payment_method, $request->reference_number, $gcashNumber);
        
                        // Update order status to 'paid' or 'Cash On Delivery' based on payment method
                        $orderItem->status = $request->payment_method === 'cod'? 'cod' : 'paid';
                        $orderItem->save();
        
                        // Redirect to a different route after successful payment
                        return redirect()->route('dashboard.order-items.index')->with('success', 'Payment processed successfully!');
                    } else {
                        return redirect()->back()->with('error', 'Payment failed. Please try again.');
                    }
                } else {
                    // Validation failed, redirect back with error message
                    return redirect()->back()->with('error', 'Invalid GCash number. Please enter exactly 11 digits. Your order is pending for payment orders');
                }
            } else {
                // If not GCash, proceed with normal payment processing
                $paymentSuccessful = $this->simulatePayment($request->all());
        
                if ($paymentSuccessful) {
                    // Save payment details and update order status
                    $this->savePaymentDetails($orderItem, $request->payment_method, $request->reference_number, null);
        
                    // Update order status to 'paid'
                    $orderItem->status = 'paid';
                    $orderItem->save();
        
                    // Redirect to a different route after successful payment
                    return redirect()->route('dashboard.order-items.index')->with('success', 'Payment processed successfully!');
                } else {
                    return redirect()->back()->with('error', 'Payment failed. Please try again.');
                }
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error processing payment: '. $e->getMessage());
        }
    }

    private function simulatePayment($paymentDetails)
    {
        return true; // Simulating successful payment
    }

    private function savePaymentDetails(OrderItem $orderItem, $paymentMethod, $referenceNumber = null, $gcashNumber = null)
    {
        $orderItem->payment_method = $paymentMethod;
        if ($referenceNumber) {
            $orderItem->reference_number = $referenceNumber;
        }
        if ($gcashNumber) {
            $orderItem->gcash_number = $gcashNumber;
        }
        $orderItem->save();
    }
}
