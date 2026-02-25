<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Asset {{ $asset->asset_code }}</title>
    @vite('resources/css/app.css')
    <style>
        .timeline-container {
            position: relative;
            padding-left: 20px;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -20px;
            top: 8px;
            width: 12px;
            height: 12px;
            background: #e5e7eb;
            border-radius: 50%;
            z-index: 10;
        }

        .timeline-item::after {
            content: '';
            position: absolute;
            left: -14px;
            top: 20px;
            bottom: -8px;
            width: 2px;
            background: #e5e7eb;
        }

        .timeline-item:last-child::after {
            display: none;
        }
    </style>
</head>

<body class="bg-gray-100 antialiased font-sans text-gray-800">

    <div class="max-w-2xl mx-auto p-4 md:p-6 pb-20">

        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm p-5 mb-6 text-center border border-gray-100">
            <h1 class="text-xl font-bold tracking-tight text-gray-900 mb-1">Asset Tracking</h1>
            <p class="text-sm font-medium px-3 py-1 bg-amber-100 text-amber-800 inline-block rounded-md">
                {{ $asset->asset_code }}
            </p>
        </div>

        <!-- Main Images -->
        @if($asset->hasMedia('asset_images'))
            <div class="mb-6 flex overflow-x-auto gap-3 snap-x pb-2">
                @foreach($asset->getMedia('asset_images') as $media)
                    <div
                        class="snap-center shrink-0 w-[85%] md:w-2/3 bg-white rounded-xl overflow-hidden shadow-sm border border-gray-100">
                        <img src="{{ $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl() }}"
                            alt="Asset Image" class="w-full h-48 md:h-64 object-cover">
                    </div>
                @endforeach
            </div>
        @else
            <div
                class="bg-white rounded-xl shadow-sm p-8 text-center border border-gray-100 mb-6 flex flex-col items-center">
                <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                    </path>
                </svg>
                <p class="text-gray-400 text-sm font-medium">No images available for this asset.</p>
            </div>
        @endif

        <!-- Info Card -->
        <div class="bg-white rounded-xl shadow-sm p-5 mb-6 border border-gray-100">
            <h2 class="text-2xl font-bold mb-4">{{ $asset->name }}</h2>

            <div class="grid grid-cols-2 gap-y-4 gap-x-2 text-sm">
                <div>
                    <p class="text-gray-500 mb-1">Condition</p>
                    <span
                        class="inline-block px-2.5 py-0.5 rounded-full font-medium text-xs
                    {{ $asset->condition === 'Good' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $asset->condition === 'In Use' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $asset->condition === 'Maintenance' ? 'bg-amber-100 text-amber-800' : '' }}
                    {{ $asset->condition === 'Written Off' ? 'bg-red-100 text-red-800' : '' }}
                    {{ !in_array($asset->condition, ['Good', 'In Use', 'Maintenance', 'Written Off']) ? 'bg-gray-100 text-gray-800' : '' }}">
                        {{ $asset->condition }}
                    </span>
                </div>

                <div>
                    <p class="text-gray-500 mb-1">Category</p>
                    <p class="font-medium truncate">{{ $asset->category->name ?? '-' }}</p>
                </div>

                <div class="col-span-2 mt-2 pt-4 border-t border-gray-50 border-dashed"></div>

                <div>
                    <p class="text-gray-500 mb-1">Unit</p>
                    <p class="font-medium truncate">{{ $asset->unit->name ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-gray-500 mb-1">Division</p>
                    <p class="font-medium truncate">{{ $asset->division->name ?? '-' }}</p>
                </div>

                <div class="col-span-2 mt-2 pt-4 border-t border-gray-50 border-dashed"></div>

                <div class="col-span-2">
                    <p class="text-gray-500 mb-1">Location / Details</p>
                    <p class="font-medium mb-1">{{ $asset->location ?? '-' }}</p>
                    <p class="text-gray-600">{{ $asset->description ?? '' }}</p>
                </div>

            </div>
        </div>

        <!-- History Log -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
            <h3 class="text-lg font-bold mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Activity Timeline
            </h3>

            <div class="timeline-container ml-2">

                <!-- Manual Logs Setup (If Any) -->
                @foreach($asset->logs->sortByDesc('created_at') as $log)
                    <div class="timeline-item">
                        <p class="text-xs text-gray-500 mb-1 font-medium">{{ $log->created_at->format('d M Y, H:i') }}</p>
                        <div class="bg-amber-50 rounded-lg p-3 border border-amber-100">
                            <p class="font-semibold text-sm text-gray-900 mb-1 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                                {{ $log->user->name ?? 'System' }} updated condition
                            </p>
                            <p class="text-sm font-medium text-amber-800 mb-1">Status: {{ $log->action }}</p>
                            <p class="text-sm text-gray-600">{{ $log->description }}</p>
                        </div>
                    </div>
                @endforeach

                <!-- Auto Audit Logs Setup -->
                @foreach($asset->activities->where('event', '!=', 'created')->sortByDesc('created_at') as $activity)
                    <div class="timeline-item mt-3">
                        <p class="text-xs text-gray-500 mb-1 font-medium">{{ $activity->created_at->format('d M Y, H:i') }}
                        </p>
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                            <p class="font-semibold text-sm text-gray-900 mb-1 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                {{ $activity->causer->name ?? 'System' }} {{ $activity->description }} asset
                            </p>

                            @php
                                $properties = $activity->properties;
                                $attributes = $properties['attributes'] ?? [];
                                $old = $properties['old'] ?? [];
                            @endphp

                            @if(!empty($attributes))
                                <ul class="text-xs mt-2 space-y-1">
                                    @foreach($attributes as $key => $value)
                                        @if(!is_array($value) && !is_object($value))
                                            @php
                                                $oldValue = $old[$key] ?? '(empty)';
                                                if (is_array($oldValue) || is_object($oldValue))
                                                    $oldValue = '...';
                                            @endphp
                                            <li><span class="font-medium text-gray-600">{{ $key }}:</span> <s
                                                    class="text-gray-400">{{ $oldValue }}</s> <span
                                                    class="text-blue-600 font-medium ml-1">&rarr; {{ $value }}</span></li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endforeach

                @if($asset->logs->isEmpty() && $asset->activities->isEmpty())
                    <p class="text-sm text-gray-500 italic">No history recorded yet.</p>
                @endif

            </div>
        </div>

        <div class="text-center text-xs text-gray-400 mt-8">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>

    </div>

</body>

</html>