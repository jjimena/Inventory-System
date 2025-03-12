<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Product;
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
//     public function processPayment(Request $request, $orderItemId)
// {
//     // Custom validation messages
//     $messages = [
//         'gcash_number.required' => 'Please enter your GCash number.',
//         'gcash_number.digits' => 'GCash number must be exactly 11 digits.',
//         'reference_number.required' => 'Please enter a reference number.',
//     ];

//     // Validate common fields
//     $request->validate([
//         'payment_method' => 'required|in:Card,GCash,cod',
//     ]);

//     // Validate reference number for card and GCash payments
//     if (in_array($request->payment_method, ['Card', 'GCash'])) {
//         $request->validate([
//             'reference_number' => 'required|string',
//         ], $messages);
//     }

//     DB::beginTransaction(); // Start a database transaction

//     try {
//         $orderItem = OrderItem::findOrFail($orderItemId);

//         // Validate GCash number if payment method is GCash
//         if ($request->payment_method === 'GCash') {
//             $validator = Validator::make($request->all(), [
//                 'gcash_number' => 'required|digits:11',
//             ], $messages);

//             if ($validator->fails()) {
//                 return redirect()->back()->withErrors($validator)->withInput();
//             }
//         }

//         // Simulate payment processing
//         $paymentSuccessful = $this->simulatePayment($request->all());

//         if ($paymentSuccessful) {
//             // Save payment details
//             $gcashNumber = $request->payment_method === 'GCash' ? $request->gcash_number : null;
//             $this->savePaymentDetails($orderItem, $request->payment_method, $request->reference_number, $gcashNumber);

//             // Update the order status based on payment method
//             $orderItem->status = $request->payment_method === 'cod' ? 'cod' : 'paid';
//             $orderItem->save();

//             // If this was a temporarily held order, persist related items
//             $tempOrderItems = session('temp_order_items', []);
//             if (!empty($tempOrderItems)) {
//                 foreach ($tempOrderItems as $tempOrderItem) {
//                     OrderItem::create($tempOrderItem);
//                 }
//                 // Clear temporary session storage
//                 session()->forget(['temp_order_items', 'total_price']);
//             }

//             DB::commit(); // Commit transaction
//             return redirect()->route('dashboard.order-items.index')->with('success', 'Payment processed successfully!');
//         } else {
//             DB::rollBack(); // Rollback transaction if payment fails
//             return redirect()->back()->with('error', 'Payment failed. Please try again.');
//         }
//     } catch (\Exception $e) {
//         DB::rollBack(); // Rollback transaction if there's an error
//         return redirect()->back()->with('error', 'Error processing payment: ' . $e->getMessage());
//     }
// }
// public function processPayment(Request $request, $orderItemId)
// {
//     $messages = [
//         'gcash_number.required' => 'Please enter your GCash number.',
//         'gcash_number.digits' => 'GCash number must be exactly 11 digits.',
//         'reference_number.required' => 'Please enter a reference number.',
//     ];

//     $request->validate([
//         'payment_method' => 'required|in:Card,GCash,cod',
//     ]);

//     if (in_array($request->payment_method, ['Card', 'GCash'])) {
//         $request->validate(['reference_number' => 'required|string'], $messages);
//     }

//     try {
//         $orderItem = OrderItem::findOrFail($orderItemId);

//         if ($request->payment_method === 'GCash') {
//             $validator = Validator::make($request->all(), [
//                 'gcash_number' => 'required|digits:11',
//             ], $messages);

//             if ($validator->fails()) {
//                 return redirect()->back()->with('error', 'Invalid GCash number. Please enter exactly 11 digits.');
//             }
//         }

//         // Simulated payment processing
//         $paymentSuccessful = $this->simulatePayment($request->all());

//         if ($paymentSuccessful) {
//             // Update order status
//             $orderItem->status = $request->payment_method === 'cod' ? 'cod' : 'paid';
//             $orderItem->save();

//             // Update batch stock or perform batch-specific actions
//             if ($orderItem->batch_id) {
//                 $batch = OrderItem::find($orderItem->batch_id);

//                 if ($batch) {
//                     $batch->update([
//                         'payment_status' => 'paid', // Example action, customize as needed
//                     ]);
//                 }
//             }

//             // Redirect after successful payment
//             return redirect()->route('dashboard.order-items.index')->with('success', 'Payment processed successfully!');
//         } else {
//             return redirect()->back()->with('error', 'Payment failed. Please try again.');
//         }
//     } catch (\Exception $e) {
//         return redirect()->back()->with('error', 'Error processing payment: ' . $e->getMessage());
//     }
// }

// public function processPayment(Request $request, $orderItemId)
// {
//     $messages = [
//         'gcash_number.required' => 'Please enter your GCash number.',
//         'gcash_number.digits' => 'GCash number must be exactly 11 digits.',
//         'reference_number.required' => 'Please enter a reference number.',
//     ];

//     $request->validate([
//         'payment_method' => 'required|in:Card,GCash,cod',
//     ]);

//     // Additional validation for Card and GCash
//     if (in_array($request->payment_method, ['Card', 'GCash'])) {
//         $request->validate(['reference_number' => 'required|string'], $messages);
//     }

//     if ($request->payment_method === 'GCash') {
//         $request->validate(['gcash_number' => 'required|digits:11'], $messages);
//     }

//     DB::beginTransaction();

//     try {
//         $orderItem = OrderItem::findOrFail($orderItemId);
//         $product = Product::findOrFail($orderItem->product_id);

//         // Simulate payment processing
//         $paymentSuccessful = $this->simulatePayment($request->all());

//         if ($paymentSuccessful) {
//             // Deduct stock
//             if ($product->quantity_in_stock < $orderItem->quantity) {
//                 return redirect()->back()->with('error', 'Stock is insufficient to process the payment.');
//             }

//             $product->decrement('quantity_in_stock', $orderItem->quantity);

//             // Update order status and payment details
//             $orderItem->status = $request->payment_method === 'cod' ? 'cod' : 'paid';
//             $orderItem->payment_method = $request->payment_method;

//             // Save additional payment details
//             $orderItem->reference_number = $request->reference_number;
//             if ($request->payment_method === 'GCash') {
//                 $orderItem->gcash_number = $request->gcash_number;
//             }

//             $orderItem->save();

//             DB::commit();

//             // Redirect after successful payment
//             return redirect()->route('dashboard.order-items.index')->with('success', 'Payment processed successfully!');
//         } else {
//             DB::rollBack();
//             return redirect()->back()->with('error', 'Payment failed. Please try again.');
//         }
//     } catch (\Exception $e) {
//         DB::rollBack();
//         return redirect()->back()->with('error', 'Error processing payment: ' . $e->getMessage());
//     }
// }


public function processPayment(Request $request, $orderItemId)
{
    $messages = [
        'gcash_number.required' => 'Please enter your GCash number.',
        'gcash_number.digits' => 'GCash number must be exactly 11 digits.',
        'reference_number.required' => 'Please enter a reference number.',
    ];

    $validatedData = $request->validate([
        'payment_method' => 'required|in:Card,GCash,COD',
        'reference_number' => 'required_if:payment_method,Card,GCash|string',
        'gcash_number' => 'required_if:payment_method,GCash|digits:11',
    ], $messages);

    DB::beginTransaction();

    try {
        $orderItem = OrderItem::findOrFail($orderItemId);
        $product = Product::findOrFail($orderItem->product_id);

        // Check stock availability before processing payment
        if ($product->quantity_in_stock < $orderItem->quantity) {
            return redirect()->back()->with('error', 'Stock is insufficient to process the payment.');
        }

        // Simulate payment processing
        $paymentSuccessful = $this->simulatePayment($validatedData);

        if ($paymentSuccessful) {
            // Deduct stock only if payment is successful
            $product->decrement('quantity_in_stock', $orderItem->quantity);

            // Update order status and payment details
            $orderItem->status = $validatedData['payment_method'] === 'COD' ? 'cod' : 'paid';
            $orderItem->payment_method = $validatedData['payment_method'];
            $orderItem->reference_number = $validatedData['reference_number'];

            if ($validatedData['payment_method'] === 'GCash') {
                $orderItem->gcash_number = $validatedData['gcash_number'];
            }

            $orderItem->save();

            DB::commit();

            return redirect()->route('dashboard.order-items.index')->with('success', 'Payment processed successfully!');
        } else {
            DB::rollBack();
            return redirect()->back()->with('error', 'Payment failed. Please try again.');
        }
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Error processing payment: ' . $e->getMessage());
    }
}

// public function processPayment(Request $request, $orderItemId)
// {
//     $messages = [
//         'gcash_number.required' => 'Please enter your GCash number.',
//         'gcash_number.digits' => 'GCash number must be exactly 11 digits.',
//         'reference_number.required' => 'Please enter a reference number.',
//     ];

//     $request->validate([
//         'payment_method' => 'required|in:Card,GCash,cod',
//     ]);

//     if (in_array($request->payment_method, ['Card', 'GCash'])) {
//         $request->validate(['reference_number' => 'required|string'], $messages);
//     }

//     DB::beginTransaction();

//     try {
//         $orderItem = OrderItem::findOrFail($orderItemId);
//         $product = Product::findOrFail($orderItem->product_id);

//         // Simulate payment processing
//         $paymentSuccessful = $this->simulatePayment($request->all());

//         if ($paymentSuccessful) {
//             // Deduct stock
//             if ($product->quantity_in_stock < $orderItem->quantity) {
//                 return redirect()->back()->with('error', 'Stock is insufficient to process the payment.');
//             }

//             $product->decrement('quantity_in_stock', $orderItem->quantity);

//             // Update order status
//             $orderItem->status = $request->payment_method === 'cod' ? 'cod' : 'paid';
//             $orderItem->payment_method = $request->payment_method;  // Ensure payment method is saved
//             $orderItem->save();

//             DB::commit();

//             // Redirect after successful payment
//             return redirect()->route('dashboard.order-items.index')->with('success', 'Payment processed successfully!');
//         } else {
//             DB::rollBack();
//             return redirect()->back()->with('error', 'Payment failed. Please try again.');
//         }
//     } catch (\Exception $e) {
//         DB::rollBack();
//         return redirect()->back()->with('error', 'Error processing payment: ' . $e->getMessage());
//     }
// }



    // public function processPayment(Request $request, $orderItemId)
    // {
    //     // Custom validation messages
    //     $messages = [
    //         'gcash_number.required' => 'Please enter your GCash number.',
    //         'gcash_number.digits' => 'GCash number must be exactly 11 digits.',
    //         'reference_number.required' => 'Please enter a reference number.',
    //     ];

    //     // Validate common fields
    //     $request->validate([
    //         'payment_method' => 'required|in:Card,GCash,cod',
    //     ]);

    //     // Validate reference number for card and GCash payments
    //     if (in_array($request->payment_method, ['Card', 'GCash'])) {
    //         $request->validate([
    //             'reference_number' => 'required|string',
    //         ], $messages);
    //     }

    //     try {
    //         $orderItem = OrderItem::findOrFail($orderItemId);
        
    //         // Validate GCash number if payment method is GCash
    //         if ($request->payment_method === 'GCash') {
    //             $validator = Validator::make($request->all(), [
    //                 'gcash_number' => 'required|digits:11',
    //             ], $messages);
        
    //             if (!$validator->fails()) {
    //                 // Proceed with simulated payment processing
    //                 $paymentSuccessful = $this->simulatePayment($request->all());
        
    //                 if ($paymentSuccessful) {
    //                     // Save payment details and update order status
    //                     $gcashNumber = $request->payment_method == 'GCash'? $request->gcash_number : null;
    //                     $this->savePaymentDetails($orderItem, $request->payment_method, $request->reference_number, $gcashNumber);
        
    //                     // Update order status to 'paid' or 'Cash On Delivery' based on payment method
    //                     $orderItem->status = $request->payment_method === 'cod'? 'cod' : 'paid';
    //                     $orderItem->save();
        
    //                     // Redirect to a different route after successful payment
    //                     return redirect()->route('dashboard.order-items.index')->with('success', 'Payment processed successfully!');
    //                 } else {
    //                     return redirect()->back()->with('error', 'Payment failed. Please try again.');
    //                 }
    //             } else {
    //                 // Validation failed, redirect back with error message
    //                 return redirect()->back()->with('error', 'Invalid GCash number. Please enter exactly 11 digits. Your order is pending for payment orders');
    //             }
    //         } else {
    //             // If not GCash, proceed with normal payment processing
    //             $paymentSuccessful = $this->simulatePayment($request->all());
        
    //             if ($paymentSuccessful) {
    //                 // Save payment details and update order status
                    
    //                 $this->savePaymentDetails($orderItem, $request->payment_method, $request->reference_number, null);
                    
    //                 // Update order status to 'paid'
    //                 $orderItem->status = 'paid';
    //                 $orderItem->save();
        
    //                 // Redirect to a different route after successful payment
    //                 return redirect()->route('dashboard.order-items.index')->with('success', 'Payment processed successfully!');
    //             } else {
    //                 return redirect()->back()->with('error', 'Payment failed. Please try again.');
    //             }
    //         }
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Error processing payment: '. $e->getMessage());
    //     }
    // }

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

    public function cancelPayment($orderItemId)
    {
        // Fetch the order item
        $orderItem = OrderItem::findOrFail($orderItemId);

        // Check if the order status allows cancellation (e.g., 'paid')
        if ($orderItem->status === 'paid') {
            // Restore stock for the associated product
            $product = Product::find($orderItem->product_id);
            if ($product) {
                $product->quantity_in_stock += $orderItem->quantity; // Add back the stock
                $product->save();
            }

            // Reset order item details and change status to 'cancel'
            $orderItem->payment_method = null;
            $orderItem->reference_number = null;
            $orderItem->gcash_number = null;
            $orderItem->status = 'cancel'; // Mark as canceled
            $orderItem->save();
        }

        // Redirect to the order items page with an informational message
        return redirect()->route('dashboard.order-items.index')
            ->with('info', 'Payment process has been cancelled, and stock has been restored.');
    }
}
