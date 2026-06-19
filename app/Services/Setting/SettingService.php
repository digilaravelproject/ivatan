<?php

namespace App\Services\Setting;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SettingService
{
    protected const CACHE_KEY = 'app_settings';
    protected const CACHE_TTL = 3600;

    protected ?array $allSettings = null;

    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->loadAll();

        return $settings[$key] ?? $default;
    }

    public function set(string $key, mixed $value, ?string $type = null, ?string $group = null, bool $encrypted = false, ?string $description = null): Setting
    {
        $record = Setting::firstOrNew(['key' => $key]);
        $record->is_encrypted = $encrypted;
        $record->value = $value;
        $record->type = $type ?? $this->inferType($value);
        $record->group = $group;
        $record->description = $description;
        $record->save();

        $this->clearCache();

        return $record;
    }

    public function setMultiple(array $settings, ?string $group = null): void
    {
        foreach ($settings as $key => $value) {
            $type = null;
            $encrypted = false;
            $description = null;
            $groupName = $group;

            if (is_array($value)) {
                $type = $value['type'] ?? null;
                $encrypted = $value['encrypted'] ?? false;
                $description = $value['description'] ?? null;
                $groupName = $value['group'] ?? $group;
                $value = $value['value'] ?? null;
            }

            $this->set($key, $value, $type, $groupName, $encrypted, $description);
        }

        $this->clearCache();
    }

    public function getGroup(string $group): array
    {
        return Setting::where('group', $group)
            ->get()
            ->keyBy('key')
            ->map(fn($setting) => $setting->value)
            ->toArray();
    }

    public function getGatewayConfig(string $gateway): array
    {
        $prefix = "payment.{$gateway}";

        return [
            'key' => $this->get("{$prefix}.key", ''),
            'secret' => $this->get("{$prefix}.secret", ''),
            'webhook_secret' => $this->get("{$prefix}.webhook_secret", ''),
            'webhook_url' => $this->get("{$prefix}.webhook_url", ''),
            'env' => $this->get("{$prefix}.env", 'sandbox'),
            'webhook_username' => $this->get("{$prefix}.webhook_username", ''),
            'webhook_password' => $this->get("{$prefix}.webhook_password", ''),
        ];
    }

    public function clearCache(): void
    {
        $this->allSettings = null;
        Cache::forget(self::CACHE_KEY);
    }

    protected function loadAll(): array
    {
        if ($this->allSettings !== null) {
            return $this->allSettings;
        }

        $this->allSettings = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Setting::all()
                ->keyBy('key')
                ->map(function ($setting) {
                    return $setting->value;
                })
                ->toArray();
        });

        return $this->allSettings;
    }

    protected function inferType(mixed $value): string
    {
        return match (true) {
            is_bool($value) => 'boolean',
            is_int($value) => 'integer',
            is_array($value) || is_object($value) => 'json',
            is_string($value) && in_array($value, ['true', 'false', '1', '0', 'yes', 'no']) => 'boolean',
            default => 'string',
        };
    }
}
