<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            width: 80mm;
            margin: 0 auto;
            color: #000;
            background: #fff;
            font-size: 13px;
            line-height: 1.5;
        }

        @media print {
            @page { size: 80mm auto; margin: 0; }
            body { width: 80mm; margin: 0; }
            .page-cut { page-break-after: always; }
        }

        .center { text-align: center; }

        .divider {
            border: none;
            border-top: 1px solid #bbb;
            margin: 8px 0;
        }

        /* ── CUSTOMER COPY ── */

        .shop-name {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
        }

        .shop-address {
            font-size: 12px;
            text-align: center;
        }

        .delivery-label {
            font-size: 12px;
            text-align: center;
            margin: 6px 0 2px 0;
        }

        /* Big name - like "MENTO SOFTWARE" in image */
        .big-name {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 4px;
        }

        .customer-name {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
        }

        .customer-phone {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
        }

        .customer-address {
            font-size: 12px;
            text-align: center;
            margin-top: 2px;
        }

        /* Item table rows */
        .item-row {
            display: flex;
            align-items: flex-start;
            margin: 6px 0 2px 0;
        }

        .item-qty {
            width: 24px;
            font-size: 13px;
            flex-shrink: 0;
        }

        .item-name {
            flex: 1;
            font-size: 13px;
            font-weight: bold;
            padding-right: 4px;
        }

        .item-price {
            font-size: 13px;
            white-space: nowrap;
            text-align: right;
            min-width: 50px;
        }

        .item-option {
            font-size: 12px;
            padding-left: 24px;
            margin-top: -2px;
            margin-bottom: 4px;
        }

        .delivery-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            font-weight: bold;
            margin: 6px 0 2px 0;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            font-weight: bold;
            margin: 4px 0 2px 0;
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin: 2px 0;
        }

        .meta-text {
            font-size: 12px;
            text-align: center;
        }

        .thank-you {
            font-size: 13px;
            text-align: center;
            margin: 4px 0;
        }

        /* ── KITCHEN COPY ── */
        .kitchen-wrap { padding: 8px 6px; }

        .kitchen-title-box {
            display: inline-block;
            border: 2px solid #000;
            padding: 4px 18px;
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .kitchen-order-num { font-size: 15px; font-weight: bold; }
        .kitchen-type { font-size: 14px; font-weight: bold; }
        .kitchen-due { font-size: 14px; }

        .kitchen-divider {
            border: none;
            border-top: 3px solid #000;
            margin: 8px 0;
        }

        .kitchen-item {
            font-size: 24px;
            font-weight: 900;
            text-transform: uppercase;
            line-height: 1.1;
            margin: 10px 0 4px 0;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        .kitchen-option {
            font-size: 18px;
            font-weight: bold;
            padding-left: 10px;
            margin-bottom: 4px;
        }

        .kitchen-note {
            font-size: 20px;
            font-weight: bold;
            border: 3px dashed #000;
            padding: 8px;
            margin-top: 12px;
        }

        .footer-pad { height: 80px; }
    </style>
</head>
<body>

    {{-- ════════ CUSTOMER COPY ════════ --}}
    <div style="padding: 10px 8px;">

        {{-- Shop Header --}}
        <div class="shop-name">PROPER TAKEAWAY LINCOLN</div>
        <div class="shop-address">11 Clifton street</div>
        <div class="shop-address">LN5 8LQ Lincoln</div>

        <div class="divider"></div>

        {{-- Order type label + big customer/brand name --}}
        <div class="delivery-label">{{ strtoupper($order->delivery_type) }}</div>
        <div class="big-name">{{ strtoupper($order->first_name) }} {{ strtoupper($order->last_name) }}</div>

        <div class="divider"></div>

        {{-- Customer details --}}
        <div class="customer-name">{{ strtoupper($order->first_name) }} {{ strtoupper($order->last_name) }}</div>
        <div class="customer-phone">{{ $order->phone }}</div>
        @if($order->delivery_type === 'delivery')
            <div class="customer-address">{{ $order->address_1 }}</div>
            @if($order->address_2)<div class="customer-address">{{ $order->address_2 }}</div>@endif
            <div class="customer-address">{{ $order->postcode }} {{ $order->city }}</div>
        @endif

        <div class="divider"></div>

        {{-- Items --}}
        <div>
            @foreach($order->items as $item)
                <div class="item-row">
                    <span class="item-qty">{{ $item->quantity }}x</span>
                    <span class="item-name">{{ $item->product_name }}</span>
                    <span class="item-price">£ {{ number_format($item->total, 2) }}</span>
                </div>
                @foreach($item->options as $opt)
                    <div class="item-option">Option: {{ $opt->option_name }}</div>
                @endforeach
            @endforeach

            @if($order->delivery_charge > 0)
                <div class="delivery-row">
                    <span>Delivery</span>
                    <span>£ {{ number_format($order->delivery_charge, 2) }}</span>
                </div>
            @endif
        </div>

        <div class="divider"></div>

        {{-- Total --}}
        <div class="total-row">
            <span>TOTAL</span>
            <span>£ {{ number_format($order->total, 2) }}</span>
        </div>
        <div class="payment-row">
            <span>{{ $order->payment_method ?? 'PayPal' }}</span>
            <span>£ {{ number_format($order->total, 2) }}</span>
        </div>

        <div class="divider"></div>

        {{-- Reference + Date --}}
        <div class="meta-text">Reference: {{ $order->order_number }}</div>
        <div class="meta-text">Placed on {{ $order->created_at->format('d/m/Y H:i') }}</div>

        <div class="divider"></div>

        <div class="thank-you">Thank you for your order!</div>

        <div class="footer-pad"></div>
    </div>

    <div class="page-cut"></div>

    {{-- ════════ KITCHEN COPY ════════ --}}
    <div class="kitchen-wrap">
        <div class="center">
            <div class="kitchen-title-box">KITCHEN</div>
            <div class="kitchen-order-num">#{{ $order->order_number }}</div>
            <div class="kitchen-type">{{ strtoupper($order->delivery_type) }}</div>
            <div class="kitchen-due">Due: {{ $order->time }}</div>
        </div>

        <div class="kitchen-divider"></div>

        <div>
            @foreach($order->items as $item)
                <div class="kitchen-item">{{ $item->quantity }}X {{ strtoupper($item->product_name) }}</div>
                @foreach($item->options as $opt)
                    <div class="kitchen-option">{{ strtoupper($opt->option_name) }}</div>
                @endforeach
            @endforeach
        </div>

        @if($order->notes)
            <div class="kitchen-note">NOTE: {{ strtoupper($order->notes) }}</div>
        @endif

        <div class="center" style="font-size:11px; font-weight:bold; margin-top:20px;">*** KITCHEN COPY ***</div>
        <div style="height: 100px;"></div>
    </div>

    <script>
        window.onload = function() {
            window.print();
            setTimeout(() => { window.close(); }, 1500);
        };
    </script>
</body>
</html>