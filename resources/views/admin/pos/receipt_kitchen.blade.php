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

        .kitchen-wrap {
            padding: 6px 4px;
            width: 72mm;
        }

        .kitchen-title-box {
            display: inline-block;
            border: 2px solid #000;
            padding: 4px 16px;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .kitchen-order-num { font-size: 14px; font-weight: bold; }
        .kitchen-type      { font-size: 13px; font-weight: bold; }
        .kitchen-due       { font-size: 13px; }

        .kitchen-divider {
            border: none;
            border-top: 3px solid #000;
            margin: 6px 0;
        }

        .kitchen-item {
            font-size: 22px;
            font-weight: 900;
            text-transform: uppercase;
            line-height: 1.1;
            margin: 8px 0 3px 0;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
            word-break: break-word;
        }

        .kitchen-option {
            font-size: 16px;
            font-weight: bold;
            padding-left: 8px;
            margin-bottom: 3px;
            word-break: break-word;
        }

        .kitchen-note {
            font-size: 18px;
            font-weight: bold;
            border: 3px dashed #000;
            padding: 6px;
            margin-top: 10px;
            word-break: break-word;
        }

        .kitchen-footer-feed {
            height: 30mm;
            display: block;
        }
    </style>
</head>
<body>

    <div class="kitchen-wrap">
        <div class="center">
            <div class="kitchen-title-box">KITCHEN</div><br>
            <div class="kitchen-order-num">#{{ $order->order_number }}</div>
            <div class="kitchen-type">{{ strtoupper($order->delivery_type) }}</div>
            <div class="kitchen-due">Due: {{ $order->time }}</div>
        </div>

        <hr class="kitchen-divider">

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

        <div class="center" style="font-size:10px; font-weight:bold; margin-top:16px;">*** KITCHEN COPY ***</div>

        <div class="kitchen-footer-feed"></div>
    </div>

    <script>
        window.onload = function() {
            window.print();
            setTimeout(() => { window.close(); }, 1500);
        };
    </script>
</body>
</html>