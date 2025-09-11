<x-layout>
    <?php
    $availablePaymentMethods = config('payment-methods');
    dump($paymentMethods);
    dump($availablePaymentMethods);
    ?>
    <x-slot:heading>
        Payment methods
    </x-slot>
    <h1>Payment methods</h1>

    <table>
        <thead>
            <tr>
                <th>Payment method</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($paymentMethods as $paymentMethod)
                <tr>
                    <td>
                        <form method="POST" action="{{ route('profile.payment_methods.rename', $paymentMethod->id) }}">
                            @csrf
                            <select name="type" class="form-select block w-full mt-1">
                            @foreach($availablePaymentMethods as $key => $option)
                                <option value="{{ $key }}" {{ $paymentMethod->type == $key ? 'selected' : '' }}>{{ __('payment-methods.'.$option['name']) }}</option>
                            @endforeach
                            </select>
                            <input type="text" name="provider" value="{{ $paymentMethod->provider }}" autocomplete="off" title="Payment provider name"/>
                            <select name="payment_method" {{ $paymentMethod['type'] == '5' ? '' : 'class=display-none' }} title="Payment provider payment method">
                                <option value="">-- Select payment method --</option>
                                @if($paymentMethod['type'] == '5') {{-- Payment provider --}}
                                    @foreach($paymentMethods as $option)
                                        @if($availablePaymentMethods[$option['type']]['payment_provider_payment_method'])
                                            <option value="{{ $option['id'] }}" {{ $paymentMethod->payment_provider_payment_method_id == $option['id'] ? 'selected' : '' }}>{{ __('payment-methods.'.$availablePaymentMethods[$option['type']]['name']) }} - {{ $option['provider'] }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            <button type="submit" onclick="return confirm('The new name will be used for all the items where that payment method is in use.')">Rename</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('profile.payment_methods.delete', $paymentMethod->id) }}" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this payment method? It will be also removed from all the items where it was saved.')">Remove</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if (count($paymentMethods) < $maxPaymentMethods)
        <form method="POST" action="{{ route('profile.payment_methods.add') }}">
            @csrf
            <input type="text" name="name" placeholder="New payment method name" autocomplete="off" required />
            <button type="submit">Add Payment Method</button>
        </form>
    @else
        @php $isDemo = Auth::user()->demo ?? false; @endphp
        @if ($isDemo)
            <p>You have reached the maximum number of payment methods allowed for demo accounts ({{ $maxPaymentMethods }}). To add more payment methods, please register for a full account.</p>
        @else
            <p>You have reached the maximum number of payment methods ({{ $maxPaymentMethods }}).</p>
        @endif
    @endif

    <a href="{{ route('profile') }}">&larr; Back to Profile</a>
</x-layout>
