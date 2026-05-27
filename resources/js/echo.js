import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

const localHosts = new Set(['localhost', '127.0.0.1', '::1']);
const normalizeScheme = (scheme) => (scheme || (import.meta.env.PROD ? 'https' : 'http')).replace(':', '');
const isTlsScheme = (scheme) => ['https', 'wss'].includes(normalizeScheme(scheme));
const toPort = (port, fallback) => Number(port || fallback);
function normalizeHost(host) {
    const value = (host || '')
        .replace(/^wss?:\/\//, '')
        .replace(/^https?:\/\//, '')
        .split('/')[0]
        .replace(/^\[(::1)\](?::\d+)?$/, '$1');

    return value === '::1' ? value : value.replace(/:\d+$/, '');
}

function reportInvalidBroadcastConfig(reason) {
    console.error(`[Broadcast] ${reason}`);
    console.error('[Broadcast] Check VITE_REVERB_APP_KEY, VITE_REVERB_HOST, VITE_REVERB_PORT, and VITE_REVERB_SCHEME before rebuilding assets.');
}

function createReverbConfig() {
    const key = import.meta.env.VITE_REVERB_APP_KEY;
    const host = normalizeHost(import.meta.env.VITE_REVERB_HOST);
    const scheme = normalizeScheme(import.meta.env.VITE_REVERB_SCHEME);
    const forceTLS = isTlsScheme(scheme);
    const port = toPort(import.meta.env.VITE_REVERB_PORT, forceTLS ? 443 : 80);

    if (!key || !host) {
        reportInvalidBroadcastConfig('Reverb is missing a public app key or host.');
        return null;
    }

    if (import.meta.env.PROD && localHosts.has(host)) {
        reportInvalidBroadcastConfig(`Production Reverb host cannot be "${host}" because browser users would connect to their own machine.`);
        return null;
    }

    return {
        broadcaster: 'reverb',
        key,
        wsHost: host,
        wsPort: port,
        wssPort: port,
        forceTLS,
        enabledTransports: forceTLS ? ['wss'] : ['ws', 'wss'],
    };
}

function createPusherConfig() {
    const key = import.meta.env.VITE_PUSHER_APP_KEY;
    const cluster = import.meta.env.VITE_PUSHER_APP_CLUSTER;
    const host = normalizeHost(import.meta.env.VITE_PUSHER_HOST);
    const scheme = normalizeScheme(import.meta.env.VITE_PUSHER_SCHEME);
    const forceTLS = isTlsScheme(scheme);
    const port = toPort(import.meta.env.VITE_PUSHER_PORT, forceTLS ? 443 : 80);

    if (!key) {
        reportInvalidBroadcastConfig('No Reverb or Pusher public app key is configured.');
        return null;
    }

    if (import.meta.env.PROD && localHosts.has(host)) {
        reportInvalidBroadcastConfig(`Production Pusher host cannot be "${host}" because browser users would connect to their own machine.`);
        return null;
    }

    return {
        broadcaster: 'pusher',
        key,
        cluster,
        wsHost: host || undefined,
        wsPort: port,
        wssPort: port,
        forceTLS,
        enabledTransports: forceTLS ? ['wss'] : ['ws', 'wss'],
    };
}

const echoConfig = import.meta.env.VITE_REVERB_APP_KEY
    ? createReverbConfig()
    : createPusherConfig();

if (echoConfig) {
    window.Echo = new Echo(echoConfig);
}
