<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Print Asset QR Codes</title>
    <style>
        body {
            font-family: sans-serif;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            text-align: center;
        }

        .card {
            border: 1px solid #ccc;
            padding: 15px;
            page-break-inside: avoid;
        }

        .name {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .code {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 10px;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <div class="no-print" style="background: #f4f4f4; padding: 10px; margin-bottom: 20px;">
        <button onclick="window.print()">Print Now</button>
        <button onclick="window.close()">Close</button>
    </div>
    <div class="grid">
        @foreach($assets as $asset)
            <div class="card">
                <div class="name">{{ $asset->name }}</div>
                <div class="code">{{ $asset->asset_code }}</div>
                <div class="qr">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->generate(route('track.asset', $asset->asset_code)) !!}
                </div>
            </div>
        @endforeach
    </div>
</body>

</html>