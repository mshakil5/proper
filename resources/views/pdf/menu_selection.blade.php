<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Menu Selection</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2 { text-align: center; margin-bottom: 20px; }
        .category { margin-bottom: 20px; }
        .category h4 { margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        p { margin: 5px 0; }
    </style>
</head>
<body>
    <h2>Menu Selection for Booking</h2>

    <p><strong>Name:</strong> {{ $booking->name }}</p>
    <p><strong>Date & Time:</strong> {{ $booking->date }} at {{ $booking->time }}</p>
    <p><strong>People:</strong> {{ $booking->people }}</p>

    @php
        $categories = ['soup' => [], 'main' => [], 'side' => [], 'dessert' => []];

        foreach ($menuSelection as $key => $value) {
            $parts = explode('_', $key);
            $cat = $parts[0];
            $item = implode(' ', array_slice($parts, 1));

            if (isset($categories[$cat])) {
                $categories[$cat][] = ['name' => ucwords($item), 'value' => $value];
            }
        }
    @endphp

    @foreach($categories as $cat => $items)
        @if(count($items) > 0)
            <div class="category">
                <h4>{{ ucfirst($cat) }}</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Selected</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['value'] == 1 ? 'Yes' : 'No' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endforeach

    @if($booking->message)
        <p><strong>Customer Message:</strong><br>{!! nl2br(e($booking->message)) !!}</p>
    @endif
</body>
</html>
