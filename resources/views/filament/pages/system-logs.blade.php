<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header Bar --}}
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; background: white; padding: 16px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.08); border: 1px solid rgba(0,0,0,.05);">
            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 12px;">
                <input
                    type="text"
                    wire:model.live.debounce.500ms="searchQuery"
                    placeholder="🔍 Search logs..."
                    style="width: 280px; padding: 8px 12px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 14px; outline: none;"
                />

                <select wire:model.live="levelFilter"
                    style="width: 170px; padding: 8px 12px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 14px; outline: none;">
                    <option value="">All Levels</option>
                    <option value="emergency">🔴 Emergency</option>
                    <option value="alert">🔴 Alert</option>
                    <option value="critical">🔴 Critical</option>
                    <option value="error">🟠 Error</option>
                    <option value="warning">🟡 Warning</option>
                    <option value="info">🔵 Info</option>
                    <option value="debug">⚪ Debug</option>
                </select>
            </div>

            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="display: inline-flex; align-items: center; gap: 6px; background: #f3f4f6; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 500; color: #374151;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #f59e0b;"></span>
                    {{ count($logs) }} entries
                </span>

                <button wire:click="refreshLogs" style="display: inline-flex; align-items: center; gap: 4px; padding: 6px 14px; border-radius: 8px; background: #f3f4f6; border: 1px solid #e5e7eb; font-size: 12px; font-weight: 500; color: #374151; cursor: pointer;">
                    ↻ Refresh
                </button>

                <button wire:click="clearLogs" wire:confirm="Are you sure you want to permanently delete all log entries?"
                    style="display: inline-flex; align-items: center; gap: 4px; padding: 6px 14px; border-radius: 8px; background: #fef2f2; border: 1px solid #fecaca; font-size: 12px; font-weight: 500; color: #dc2626; cursor: pointer;">
                    🗑 Clear
                </button>
            </div>
        </div>

        {{-- Log Entries --}}
        <div style="display: flex; flex-direction: column; gap: 8px;">
            @forelse($logs as $log)
                @php
                    $lvl = strtolower($log['level']);
                    $isErr = in_array($lvl, ['error', 'critical', 'alert', 'emergency']);
                    $isWarn = $lvl === 'warning';
                    $borderColor = match(true) {
                        $isErr => '#ef4444',
                        $isWarn => '#f59e0b',
                        $lvl === 'info' => '#3b82f6',
                        $lvl === 'debug' => '#9ca3af',
                        default => '#d1d5db',
                    };
                    $bgColor = match(true) {
                        $isErr => '#fef2f2',
                        $isWarn => '#fffbeb',
                        default => '#ffffff',
                    };
                    $badgeBg = match($lvl) {
                        'emergency', 'alert', 'critical' => '#dc2626',
                        'error' => '#f97316',
                        'warning' => '#f59e0b',
                        'info', 'notice' => '#3b82f6',
                        'debug' => '#9ca3af',
                        default => '#6b7280',
                    };
                @endphp

                <div style="border-radius: 8px; border-left: 4px solid {{ $borderColor }}; background: {{ $bgColor }}; box-shadow: 0 1px 3px rgba(0,0,0,.06); border: 1px solid rgba(0,0,0,.05); overflow: hidden; transition: box-shadow 0.15s;">
                    <div style="display: flex; align-items: flex-start; gap: 16px; padding: 12px 16px;">
                        {{-- Level Badge --}}
                        <span style="flex-shrink: 0; margin-top: 2px; display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; background: {{ $badgeBg }}; color: white;">
                            {{ $log['level'] }}
                        </span>

                        {{-- Content --}}
                        <div style="min-width: 0; flex: 1;">
                            <p style="font-size: 13px; color: #111827; word-break: break-word; line-height: 1.5; margin: 0;">
                                {{ $log['msg'] }}
                            </p>

                            @if(!empty($log['trace']))
                                <details style="margin-top: 8px;">
                                    <summary style="cursor: pointer; font-size: 12px; color: #6b7280; user-select: none;">
                                        ▶ Stack Trace
                                    </summary>
                                    <pre style="margin-top: 8px; overflow-x: auto; border-radius: 8px; background: #1f2937; padding: 12px; font-size: 11px; line-height: 1.5; color: #d1d5db; max-height: 200px; overflow-y: auto;">{{ $log['trace'] }}</pre>
                                </details>
                            @endif
                        </div>

                        {{-- Metadata --}}
                        <div style="flex-shrink: 0; text-align: right;">
                            <p style="white-space: nowrap; font-family: ui-monospace, monospace; font-size: 11px; color: #6b7280; margin: 0;">
                                {{ $log['date'] }}
                            </p>
                            <p style="margin-top: 2px; font-size: 10px; color: #9ca3af;">
                                {{ $log['env'] }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; background: white; padding: 64px 32px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.08); border: 1px solid rgba(0,0,0,.05);">
                    <p style="font-size: 40px; margin: 0;">📄</p>
                    <h3 style="margin-top: 12px; font-size: 14px; font-weight: 600; color: #111827;">No log entries</h3>
                    <p style="margin-top: 4px; font-size: 14px; color: #6b7280;">The log file is empty or no entries match your filters.</p>
                </div>
            @endforelse
        </div>

        {{-- Load More --}}
        @if(count($logs) >= $perPage)
            <div style="display: flex; justify-content: center;">
                <button wire:click="loadMore"
                    style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 20px; border-radius: 8px; background: #f3f4f6; border: 1px solid #e5e7eb; font-size: 13px; font-weight: 500; color: #374151; cursor: pointer;">
                    ↓ Load More
                </button>
            </div>
        @endif
    </div>
</x-filament-panels::page>