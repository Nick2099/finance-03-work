<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProfilePaymentMethodsController extends Controller
{
    /**
     * Get the max payment methods allowed for a user.
     */
    protected function getMaxPaymentMethods($user)
    {
        $isDemo = $user->demo ?? false;
        $userType = $user->type ?? "basic";
        if ($isDemo) {
            return config('appoptions.payment_methods_per_demo_user');
        } elseif ($userType === 'premium') {
            return config('appoptions.payment_methods_per_premium_user');
        } else {
            return config('appoptions.payment_methods_per_basic_user');
        }
    }

    /**
     * Display the user's payment methods page.
     */
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'You have to be logged in.');
        }

        $user = $request->user();
        $paymentMethods = $user->paymentMethods()->orderBy('id')->get();
        $maxPaymentMethods = $this->getMaxPaymentMethods($user);
        // dd($paymentMethods);
        return view('profile-payment-methods', [
            'paymentMethods' => $paymentMethods,
            'maxPaymentMethods' => $maxPaymentMethods,
        ]);
    }

    public function add(Request $request)
    {
        $user = $request->user();
        $maxPaymentMethods = $this->getMaxPaymentMethods($user);
        $count = $user->paymentMethods()->count();
        if ($count >= $maxPaymentMethods) {
            return redirect()->route('profile.payment_methods')->withErrors('Payment method limit reached.');
        }
        $request->validate([
            'type' => 'required|tinyint',
            'provider' => 'nullable|string|max:50',
        ]);
        // Find the lowest unused payment_method_id for this user
        $user->paymentMethods()->create([
            'type' => $request->input('type'),
            'provider' => $request->input('provider'),
        ]);
        return redirect()->route('profile.payment_methods');
    }

    public function rename(Request $request, $paymentMethodId)
    {
        $user = $request->user();
        $paymentMethod = $user->paymentMethods()->findOrFail($paymentMethodId);
        $request->validate([
            'type' => 'required|tinyint',
            'provider' => 'nullable|string|max:50',
        ]);
        $paymentMethod->type = $request->input('type');
        $paymentMethod->provider = $request->input('provider');
        $paymentMethod->save();
        return redirect()->route('profile.payment_methods');
    }
    
    public function delete(Request $request, $paymentMethodId)
    {
        $user = $request->user();
        $paymentMethod = $user->paymentMethods()->findOrFail($paymentMethodId);
        $paymentMethod->delete();

        // Remove payment_method_id from all items that used this payment method (remove all occurrences)

        return redirect()->route('profile.payment_methods');
    }
}
