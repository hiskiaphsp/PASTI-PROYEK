<x-web-layout title="Order">
    {{-- @section('css')
        <script type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="config('midtrans.client_key')"></script>
    @endsection --}}
<main>
        <!-- breadcrumb-area-start -->
        <div class="breadcrumb__area grey-bg pt-5 pb-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="tp-breadcrumb__content">
                            <div class="tp-breadcrumb__list">
                                <span class="tp-breadcrumb__active"><a href="index.html">Cart</a></span>
                                <span class="dvdr">/</span>
                                <span>Order Info</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- breadcrumb-area-end -->

        <section class="cart-area pb-80 mt-20">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h3>Cart Info</h3>
                        <div class="table-content table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="cart-product-name">Order Number</th>
                                        <th class="product-price">Order Amount</th>
                                        <th class="product-quantity">Status</th>
                                        <th class="product-subtotal">Payment Method</th>
                                        <th class="product-remove">Create At</th>
                                        <th class="product-remove">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($mergedOrders))
                                        @foreach ($mergedOrders as $order)
                                        <tr>
                                            <td>{{ $order['order_number'] }}</td>
                                            <td>{{ $order['order_amount'] }}</td>
                                            <td>{{ $order['order_status'] }}</td>
                                            <td>{{ $order['payment_method'] }}</td>
                                            <td>{{ $order['created_at'] }}</td>
                                            <td>
                                                <div class="dropdown-basic me-0">
                                                    <div class="btn-group dropstart">
                                                        <a class="dropdown-toggle btn" type="button" data-bs-toggle="dropdown" aria-expanded="false"></a>
                                                        <ul class="dropdown-menu dropdown-block">
                                                            @if ($order['order_status'] == 'Unpaid')
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('order.show', $order['id']) }}">Bayar</a>
                                                                </li>
                                                            @endif
                                                            @if ($order['order_status'] == 'Pending')
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('order.cancel', ['id' => $order['id']]) }}" onclick="event.preventDefault(); document.getElementById('cancel-order-form-{{ $order['id'] }}').submit();">Cancel</a>
                                                                    <form id="cancel-order-form-{{ $order['id'] }}" action="{{ route('order.cancel', ['id' => $order['id']]) }}" method="POST" style="display: none;">
                                                                        @method('PUT')
                                                                        @csrf
                                                                    </form>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    @section('script')

    @endsection
</x-web-layout>
