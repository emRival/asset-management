<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Asset Labels - {{ config('app.name') }}</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
            color: #111827;
        }

        .no-print {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 30px;
            padding: 15px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        button {
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .btn-print {
            background-color: #2563eb;
            color: white;
        }

        .btn-print:hover {
            background-color: #1d4ed8;
        }

        .btn-close {
            background-color: #f3f4f6;
            color: #4b5563;
        }

        .btn-close:hover {
            background-color: #e5e7eb;
        }

        .label-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 15px;
            justify-items: center;
        }

        .label-card {
            background: white;
            width: 280px;
            height: 140px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            display: flex;
            padding: 12px;
            box-sizing: border-box;
            position: relative;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .label-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding-right: 8px;
        }

        .label-header {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .asset-name {
            font-size: 14px;
            font-weight: 800;
            line-height: 1.25;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            color: #111827;
        }

        .asset-info {
            font-size: 10px;
            color: #4b5563;
            margin-top: 4px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .asset-code {
            font-family: monospace;
            font-size: 12px;
            font-weight: 600;
            background: #f3f4f6;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 6px;
            color: #374151;
        }

        .label-qr {
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            padding: 4px;
            border: 1px solid #f3f4f6;
            border-radius: 6px;
        }

        .label-qr svg {
            width: 100% !important;
            height: 100% !important;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .no-print {
                display: none;
            }

            .label-grid {
                gap: 5mm;
            }

            .label-card {
                box-shadow: none;
                border: 1px solid #000;
                /* Crisper border for print */
            }
        }
    </style>
</head>

<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">Print Labels</button>
        <button class="btn-close" onclick="window.close()">Close Preview</button>
    </div>

    <div class="label-grid">
        @foreach($assets as $asset)
            <div class="label-card">
                <div class="label-content">
                    <div>
                        <div class="label-header">{{ config('app.name', 'AMS') }}</div>
                        <div class="asset-name">{{ $asset->name }}</div>
                        <div class="asset-info">
                            <div class="info-item">
                                <b>Loc:</b> {{ $asset->location ?? '-' }}
                            </div>
                            <div class="info-item">
                                <b>Date:</b> {{ $asset->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="asset-code">{{ $asset->asset_code }}</div>
                    </div>
                </div>
                <div class="label-qr">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(100)->generate(route('track.asset', $asset->asset_code)) !!}
                </div>
            </div>
        @endforeach
    </div>
</body>

</html>