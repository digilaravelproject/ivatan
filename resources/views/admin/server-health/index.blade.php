@extends('admin.layouts.app')
@section('title', 'Server Health')
@section('content')
<div class="space-y-6" x-data="serverHealth()" x-init="init()">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Server Health</h1>
        <div class="flex items-center gap-3">
            <span class="text-sm text-gray-500" x-text="lastChecked"></span>
            <button @click="refreshAll" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                Refresh All
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Reverb Status --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Reverb (WebSocket)</h2>
                <span class="inline-block w-3 h-3 rounded-full"
                      :class="reverb.loading ? 'bg-yellow-400 animate-pulse' : reverb.connected ? 'bg-green-500' : 'bg-red-500'"></span>
            </div>
            <div x-show="reverb.loading" class="text-sm text-gray-400">Checking...</div>
            <div x-show="!reverb.loading" class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Status</span>
                    <span x-text="reverb.connected ? 'Connected' : 'Disconnected'"
                          :class="reverb.connected ? 'text-green-600 font-medium' : 'text-red-600 font-medium'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Process</span>
                    <span x-text="reverb.process_running ? 'Running' : 'Stopped'"
                          :class="reverb.process_running ? 'text-green-600' : 'text-red-600'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Internal Host</span>
                    <span class="font-mono" x-text="reverb.internal_host"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Internal Port</span>
                    <span class="font-mono" x-text="reverb.internal_port"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">External Host</span>
                    <span class="font-mono" x-text="reverb.external_host"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">External Port</span>
                    <span class="font-mono" x-text="reverb.external_port"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Scheme</span>
                    <span class="font-mono" x-text="reverb.scheme"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">App Key</span>
                    <span class="font-mono text-xs" x-text="reverb.app_key"></span>
                </div>
                <div x-show="reverb.error" class="mt-2 p-2 bg-red-50 text-red-600 text-xs rounded">
                    <span x-text="reverb.error"></span>
                </div>
            </div>
        </div>

        {{-- Queue Status --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Queue</h2>
                <span class="inline-block w-3 h-3 rounded-full"
                      :class="queue.loading ? 'bg-yellow-400 animate-pulse' : queue.worker_running ? 'bg-green-500' : 'bg-red-500'"></span>
            </div>
            <div x-show="queue.loading" class="text-sm text-gray-400">Checking...</div>
            <div x-show="!queue.loading" class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Worker</span>
                    <span x-text="queue.worker_running ? 'Running' : 'Stopped'"
                          :class="queue.worker_running ? 'text-green-600 font-medium' : 'text-red-600 font-medium'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Driver</span>
                    <span class="font-mono" x-text="queue.queue_driver"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Queue</span>
                    <span class="font-mono" x-text="queue.queue_name"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Pending Jobs</span>
                    <span class="font-mono" x-text="queue.pending_jobs"
                          :class="queue.pending_jobs > 0 ? 'text-yellow-600 font-medium' : ''"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Failed Jobs</span>
                    <span class="font-mono" x-text="queue.failed_jobs"
                          :class="queue.failed_jobs > 0 ? 'text-red-600 font-medium' : ''"></span>
                </div>
            </div>
        </div>

        {{-- System Info --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">System</h2>
                <span class="inline-block w-3 h-3 rounded-full"
                      :class="system.loading ? 'bg-yellow-400 animate-pulse' : 'bg-green-500'"></span>
            </div>
            <div x-show="system.loading" class="text-sm text-gray-400">Loading...</div>
            <div x-show="!system.loading" class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">PHP</span>
                    <span class="font-mono" x-text="system.php_version"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Laravel</span>
                    <span class="font-mono" x-text="system.laravel_version"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Environment</span>
                    <span class="font-mono" x-text="system.environment"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">OS</span>
                    <span class="font-mono text-xs" x-text="system.os"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Timezone</span>
                    <span class="font-mono" x-text="system.timezone"></span>
                </div>
                <div class="border-t pt-2 mt-2">
                    <div class="flex justify-between mb-1">
                        <span class="text-gray-500">Disk Usage</span>
                        <span class="font-mono text-xs" x-text="system.disk_used + ' / ' + system.disk_total"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all duration-500"
                             :class="system.disk_percent > 80 ? 'bg-red-500' : system.disk_percent > 60 ? 'bg-yellow-500' : 'bg-green-500'"
                             :style="'width: ' + system.disk_percent + '%'"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1" x-text="system.disk_percent + '% used'"></p>
                </div>
                <div x-show="system.memory_total" class="border-t pt-2 mt-2">
                    <div class="flex justify-between mb-1">
                        <span class="text-gray-500">Memory</span>
                        <span class="font-mono text-xs" x-text="system.memory_used + ' / ' + system.memory_total"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Debug Mode Warning --}}
    <div x-show="!system.loading && system.debug_mode" class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <span class="text-sm text-yellow-700"><strong>Warning:</strong> APP_DEBUG is enabled. Disable in production.</span>
        </div>
    </div>
</div>

<script>
function serverHealth() {
    return {
        reverb: { loading: true, connected: false, process_running: false, internal_host: '', internal_port: 0, external_host: '', external_port: 0, scheme: 'https', app_key: '', error: null },
        queue: { loading: true, worker_running: false, pending_jobs: 0, failed_jobs: 0, queue_driver: '', queue_name: '' },
        system: { loading: true, php_version: '', laravel_version: '', os: '', environment: '', debug_mode: false, disk_total: '', disk_free: '', disk_used: '', disk_percent: 0, memory_used: null, memory_total: null, timezone: '' },
        lastChecked: '',

        init() {
            this.refreshAll();
        },

        async refreshAll() {
            await Promise.all([
                this.checkReverb(),
                this.checkQueue(),
                this.checkSystem(),
            ]);
            this.lastChecked = 'Last checked: ' + new Date().toLocaleTimeString();
        },

        async checkReverb() {
            this.reverb.loading = true;
            try {
                const res = await axios.get('/admin/server-health/check/reverb');
                this.reverb = { ...this.reverb, ...res.data, loading: false };
            } catch (e) {
                this.reverb.loading = false;
                this.reverb.error = 'Failed to fetch';
            }
        },

        async checkQueue() {
            this.queue.loading = true;
            try {
                const res = await axios.get('/admin/server-health/check/queue');
                this.queue = { ...this.queue, ...res.data, loading: false };
            } catch (e) {
                this.queue.loading = false;
            }
        },

        async checkSystem() {
            this.system.loading = true;
            try {
                const res = await axios.get('/admin/server-health/check/system');
                this.system = { ...this.system, ...res.data, loading: false };
            } catch (e) {
                this.system.loading = false;
            }
        },
    };
}
</script>
@endsection
