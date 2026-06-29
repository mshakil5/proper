<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            width: 72mm;
            margin: 0;
            padding: 0;
            color: #000;
            background: #fff;
            font-size: 12px;
            line-height: 1.4;
        }

        @media print {
            @page {
                size: 80mm auto;
                margin: 0mm;
            }
            body {
                width: 72mm;
                margin: 0;
                padding: 0;
            }
            .no-print { display: none !important; }
        }

        .center { text-align: center; }

        .divider {
            border: none;
            border-top: 1px dashed #555;
            margin: 6px 0;
            width: 100%;
        }

        .customer-wrap {
            padding: 6px 4px;
            width: 72mm;
        }

        .shop-name {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
        }

        .shop-address {
            font-size: 11px;
            text-align: center;
        }

        .delivery-label {
            font-size: 11px;
            text-align: center;
            margin: 4px 0 2px 0;
        }

        .big-name {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 3px;
            word-break: break-word;
        }

        .customer-name {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }

        .customer-phone {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }

        .customer-address {
            font-size: 11px;
            text-align: center;
            margin-top: 2px;
            word-break: break-word;
        }

        .item-row {
            display: table;
            width: 100%;
            margin: 4px 0 2px 0;
        }

        .item-qty {
            display: table-cell;
            width: 20px;
            font-size: 12px;
            vertical-align: top;
        }

        .item-name {
            display: table-cell;
            font-size: 12px;
            font-weight: bold;
            vertical-align: top;
            padding-right: 4px;
            word-break: break-word;
        }

        .item-price {
            display: table-cell;
            font-size: 12px;
            white-space: nowrap;
            text-align: right;
            vertical-align: top;
            width: 45px;
        }

        .item-option {
            font-size: 11px;
            padding-left: 20px;
            margin-bottom: 3px;
            word-break: break-word;
        }

        .delivery-row {
            display: table;
            width: 100%;
            font-size: 12px;
            font-weight: bold;
            margin: 4px 0 2px 0;
        }

        .delivery-row span:first-child { display: table-cell; }
        .delivery-row span:last-child {
            display: table-cell;
            text-align: right;
            white-space: nowrap;
            width: 45px;
        }

        .total-row {
            display: table;
            width: 100%;
            font-size: 13px;
            font-weight: bold;
            margin: 4px 0 2px 0;
        }

        .total-row span:first-child { display: table-cell; }
        .total-row span:last-child {
            display: table-cell;
            text-align: right;
            white-space: nowrap;
            width: 45px;
        }

        .payment-row {
            display: table;
            width: 100%;
            font-size: 12px;
            margin: 2px 0;
        }

        .payment-row span:first-child { display: table-cell; }
        .payment-row span:last-child {
            display: table-cell;
            text-align: right;
            white-space: nowrap;
            width: 45px;
        }

        .meta-text {
            font-size: 11px;
            text-align: center;
        }

        .thank-you {
            font-size: 12px;
            text-align: center;
            margin: 4px 0;
        }

        .feed-space {
            height: 30mm;
            display: block;
        }
    </style>
</head>
<body>

    <div class="customer-wrap">

        <div class="shop-name">PROPER TAKEAWAY LINCOLN</div>
        <div class="shop-address">11 Clifton street</div>
        <div class="shop-address">LN5 8LQ Lincoln</div>

        <hr class="divider">

        <div class="delivery-label">{{ strtoupper($order->delivery_type) }}</div>
        <div class="big-name">{{ strtoupper($order->first_name) }} {{ strtoupper($order->last_name) }}</div>

        <hr class="divider">

        <div class="customer-name">{{ strtoupper($order->first_name) }} {{ strtoupper($order->last_name) }}</div>
        <div class="customer-phone">{{ $order->phone }}</div>
        @if($order->delivery_type === 'delivery')
            <div class="customer-address">{{ $order->address_1 }}</div>
            @if($order->address_2)<div class="customer-address">{{ $order->address_2 }}</div>@endif
            <div class="customer-address">{{ $order->postcode }} {{ $order->city }}</div>
        @endif

        <hr class="divider">

        <div>
            @foreach($order->items as $item)
                <div class="item-row">
                    <span class="item-qty">{{ $item->quantity }}x</span>
                    <span class="item-name">{{ $item->product_name }}</span>
                    <span class="item-price">£{{ number_format($item->total, 2) }}</span>
                </div>
                @foreach($item->options as $opt)
                    <div class="item-option">+ {{ $opt->option_name }}</div>
                @endforeach
            @endforeach

            @if($order->delivery_charge > 0)
                <div class="delivery-row">
                    <span>Delivery</span>
                    <span>£{{ number_format($order->delivery_charge, 2) }}</span>
                </div>
            @endif
        </div>

        <hr class="divider">

        <div class="total-row">
            <span>TOTAL</span>
            <span>£{{ number_format($order->total, 2) }}</span>
        </div>
        <div class="payment-row">
            <span>{{ strtoupper($order->payment_method ?? 'cash') }}</span>
            <span>£{{ number_format($order->total, 2) }}</span>
        </div>

        <hr class="divider">

        <div class="meta-text">Ref: {{ $order->order_number }}</div>
        <div class="meta-text">{{ $order->created_at->format('d/m/Y H:i') }}</div>

        <hr class="divider">

        <div class="thank-you">Thank you for your order!</div>

        <div class="feed-space"></div>
    </div>

    <script>
        window.onload = function() {
            window.print();
            setTimeout(() => { window.close(); }, 1500);
        };
    </script>
</body>
</html>