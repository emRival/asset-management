<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\File;

class SystemLogs extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Administration';
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'System Logs';
    protected static ?string $title = 'System Logs';
    protected static ?string $slug = 'system-logs';
    protected static ?int $navigationSort = 99;

    protected string $view = 'filament.pages.system-logs';

    public array $logs = [];
    public string $levelFilter = '';
    public string $searchQuery = '';
    public int $perPage = 50;

    public function mount(): void
    {
        $this->loadLogs();
    }

    public function loadLogs(): void
    {
        $logFile = storage_path('logs/laravel.log');

        if (!File::exists($logFile)) {
            $this->logs = [];
            return;
        }

        // Only read the last 100KB to avoid memory issues
        $fileSize = filesize($logFile);
        $maxBytes = 100 * 1024; // 100KB

        if ($fileSize > $maxBytes) {
            $handle = fopen($logFile, 'r');
            fseek($handle, -$maxBytes, SEEK_END);
            $content = fread($handle, $maxBytes);
            fclose($handle);
            // Remove the first incomplete line
            $content = substr($content, strpos($content, "\n") + 1);
        } else {
            $content = File::get($logFile);
        }

        $logs = [];

        preg_match_all(
            '/\[(\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:[+-]\d{2}:\d{2})?)\]\s+(\w+)\.(\w+):\s+(.*?)(?=\n\[|\z)/s',
            $content,
            $matches,
            PREG_SET_ORDER
        );

        foreach (array_reverse($matches) as $match) {
            $level = strtolower($match[3]);

            if ($this->levelFilter && $level !== $this->levelFilter) {
                continue;
            }

            $message = trim($match[4]);
            $messageParts = explode("\n", $message, 2);
            $mainMessage = $messageParts[0];
            // Limit stack trace to prevent huge Livewire payloads
            $stackTrace = isset($messageParts[1]) ? mb_substr($messageParts[1], 0, 1500) : null;

            if ($this->searchQuery && !str_contains(strtolower($mainMessage), strtolower($this->searchQuery))) {
                continue;
            }

            $logs[] = [
                'date' => $match[1],
                'env' => $match[2],
                'level' => $match[3],
                'msg' => mb_substr($mainMessage, 0, 500),
                'trace' => $stackTrace,
            ];

            // Stop once we have enough
            if (count($logs) >= $this->perPage) {
                break;
            }
        }

        $this->logs = $logs;
    }

    public function updatedLevelFilter(): void
    {
        $this->loadLogs();
    }

    public function updatedSearchQuery(): void
    {
        $this->loadLogs();
    }

    public function loadMore(): void
    {
        $this->perPage += 50;
        $this->loadLogs();
    }

    public function clearLogs(): void
    {
        $logFile = storage_path('logs/laravel.log');
        if (File::exists($logFile)) {
            File::put($logFile, '');
        }
        $this->logs = [];
    }

    public function refreshLogs(): void
    {
        $this->loadLogs();
    }
}
