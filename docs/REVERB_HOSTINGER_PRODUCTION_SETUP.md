# Laravel Reverb Production Setup - Hostinger & General Guide

**Version:** 1.0  
**Last Updated:** May 2026  
**Project:** Ivatan Social Platform

---

## 1. Current Broadcasting/Reverb Status

### What's Currently Configured

| Component | Status | Details |
|-----------|--------|---------|
| `laravel/reverb` package | ✅ Installed | v1.5 in `composer.json` |
| `config/reverb.php` | ✅ Configured | Full configuration present |
| `config/broadcasting.php` | ✅ Configured | Has both reverb and pusher connections |
| Default Connection | Falls back to `reverb` | `BROADCAST_CONNECTION` env var decides actual |
| `.env` Pusher Config | ✅ Active | Currently using Pusher (`BROADCAST_CONNECTION=pusher`) |
| `.env` Reverb Config | ✅ Defined | Credentials present but connection commented out |
| `routes/channels.php` | ✅ Configured | `chat.{chatId}` private channel auth ready |
| Chat Events | ✅ Implemented | `MessageSent`, `MessageRead` broadcast on private channel |

### Current .env Settings

```env
# Active (currently using Pusher)
BROADCAST_CONNECTION=pusher
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=2090298
PUSHER_APP_KEY=1c97cfa884ecb61e0959
PUSHER_APP_SECRET=ec06b6dab6123675a382
PUSHER_APP_CLUSTER=ap2
PUSHER_SCHEME=https
PUSHER_PORT=443

# Reverb credentials defined but not active
REVERB_APP_ID=390027
REVERB_APP_KEY=cvtm70sqh0nz9ogq1cza
REVERB_APP_SECRET=qyi0dzrdssjrwubwmssg
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
```

---

## 2. Required ENV Variables for Reverb

### For Development
```env
BROADCAST_CONNECTION=reverb
BROADCAST_DRIVER=reverb
REVERB_APP_KEY=your_app_key
REVERB_APP_SECRET=your_app_secret
REVERB_APP_ID=your_app_id
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
```

### For Production (with subdomain)
```env
BROADCAST_CONNECTION=reverb
BROADCAST_DRIVER=reverb
REVERB_APP_KEY=your_app_key
REVERB_APP_SECRET=your_app_secret
REVERB_APP_ID=your_app_id
REVERB_HOST=reverb.yourdomain.com
REVERB_PORT=443
REVERB_SCHEME=https
```

### Frontend Vite Variables
```env
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

---

## 3. How to Switch from Pusher to Reverb

### Step 1: Update .env

```env
# Change from Pusher to Reverb
BROADCAST_CONNECTION=reverb
BROADCAST_DRIVER=reverb

# Comment out Pusher credentials (don't delete - may need later)
# PUSHER_APP_ID=2090298
# PUSHER_APP_KEY=...
```

### Step 2: Clear Config Cache

```bash
php artisan config:clear
php artisan cache:clear
```

### Step 3: For Shared Hosting (without CLI access)

If you cannot run CLI commands on Hostinger:
1. Keep Pusher as fallback
2. Or upgrade to VPS

### Step 4: Verify Configuration

```bash
php artisan tinker
# Then:
>>> config('broadcasting.default')
=> "reverb"
```

---

## 4. Laravel Echo Frontend Production Config

### In your app.js / main.js

```javascript
// For Production (using Reverb)
const echoConfig = {
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT || 443,
    wssPort: import.meta.env.VITE_REVERB_PORT || 443,
    forceTLS: import.meta.env.VITE_REVERB_SCHEME === 'https',
    enabledTransports: ['ws', 'wss'],
};

// Fallback to Pusher if Reverb fails
if (!import.meta.env.VITE_REVERB_APP_KEY) {
    echoConfig.broadcaster = 'pusher';
    echoConfig.key = import.meta.env.VITE_PUSHER_APP_KEY;
    echoConfig.cluster = import.meta.env.VITE_PUSHER_APP_CLUSTER;
    echoConfig.forceTLS = true;
}

window.Echo = new Echo(echoConfig);
```

### For Pusher Fallback (keeping existing)

```javascript
// Still works with Pusher if you keep Pusher credentials
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});
```

---

## 5. Domain/Subdomain & Port Setup

### Option A: Reverb on Same Domain (Production)

1. Create subdomain: `reverb.yourdomain.com`
2. Point to same document root as main app
3. Reverb server runs on that subdomain

```env
REVERB_HOST=reverb.yourdomain.com
REVERB_SCHEME=https
REVERB_PORT=443
```

### Option B: Reverb on Separate Port (VPS)

```env
REVERB_HOST=your-server-ip
REVERB_PORT=8080
REVERB_SCHEME=http
```

### Port Requirements

| Port | Protocol | Purpose |
|------|----------|---------|
| 80/443 | HTTP/HTTPS | Main Laravel app |
| 8080 (or custom) | WS/WSS | Reverb WebSocket |
| 6379 | - | Redis (optional, for scaling) |

---

## 6. SSL/HTTPS and WSS Setup

### For Production

1. **Enable SSL** on your main domain via Hostinger Panel (Let's Encrypt - free)
2. For Reverb subdomain, also enable SSL
3. Set `REVERB_SCHEME=https`
4. Frontend will automatically use `wss://` (WebSocket Secure)

### Verify SSL

```bash
curl -I https://reverb.yourdomain.com
```

Should return 200 or proper response.

---

## 7. Queue Worker Setup

Laravel Reverb can work without queues (synchronous), but for production:

### If Using Queues for Broadcasting

1. **Configure Queue Connection** in `.env`:
```env
QUEUE_CONNECTION=redis
```

2. **Create Supervisor Config** (on VPS):
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/your-app/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/worker.log
stopwaitsecs=3600
```

3. **For Shared Hosting:**
   - Use "schedule" feature if available
   - Or skip queue (Reverb will broadcast synchronously - acceptable for small apps)

---

## 8. Reverb Process Command

### Starting Reverb Server

```bash
# Start Reverb (development)
php artisan reverb:start

# Start on custom port/host
php artisan reverb:start --host=0.0.0.0 --port=8080
```

### For Production (Should Run as Daemon)

```bash
# Using Supervisor (recommended - see next section)
# Or using nohup (not recommended for production)
nohup php artisan reverb:start > /var/log/reverb.log 2>&1 &
```

---

## 9. Supervisor Configuration (VPS)

Create `/etc/supervisor/conf.d/reverb.conf`:

```ini
[program:laravel-reverb]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/your-domain.com/artisan reverb:start --host=0.0.0.0 --port=8080
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/laravel-reverb.log
stopwaitsecs=3600
```

Then run:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-reverb
```

---

## 10. Hostinger Shared Hosting Limitations

### What Hostinger Shared Hosting Supports ✅
- Laravel application deployment
- MySQL database
- PHP 8.x
- SSL certificates (Let's Encrypt)
- Basic queue workers (via cron often)

### What Hostinger Shared Hosting Does NOT Support ❌
- **Long-running processes** - Cannot run `php artisan reverb:start` persistently
- **Custom ports** - Usually blocked, only 80/443 allowed
- **SSH access** - Limited on some plans
- **Supervisor/systemd** - Not available on shared hosting

### Recommended Solution for Hostinger

**Option A: Keep Pusher (Easiest)**
- Keep existing Pusher configuration
- No changes needed to deployment
- Pay for Pusher messages (free tier available)

**Option B: Hybrid Approach**
- Host Laravel on Hostinger (shared)
- Use Pusher for real-time (works immediately)
- No Reverb server needed

**Option C: Upgrade to VPS**
- Move to Hostinger VPS or other provider
- Then you can run Reverb server
- See Recommended Architecture below

---

## 11. Recommended Production Architecture

### Architecture A: Keep Pusher on Shared Hosting (Simplest)

```
┌─────────────────┐     ┌──────────┐
│  User Browser   │────▶│  Laravel │
│  (Vue/React)    │◀────│  App     │
└─────────────────┘     │(Hostinger)│
                        └─────┬──────┘
                              │
                              ▼
                        ┌───────────┐
                        │  Pusher   │ (Managed service)
                        │  (Cloud)  │
                        └───────────┘
```

**Pros:** No server changes, works immediately  
**Cons:** Monthly cost for Pusher (after free tier)

---

### Architecture B: VPS for Reverb

```
┌─────────────────┐     ┌──────────────────┐
│  User Browser   │────▶│  Laravel App     │
│  (Vue/React)    │◀────│  (Hostinger/VPS)  │
└─────────────────┘     └────────┬─────────┘
                                   │
          ┌────────────────────────┼────────────────────────┐
          │                        │                        │
          ▼                        ▼                        ▼
   ┌──────────────┐       ┌──────────────┐       ┌──────────────┐
   │   Reverb     │       │    Queue     │       │   Database    │
   │   Server     │       │   Worker     │       │   (MySQL)     │
   │  (VPS:8080)  │       │   (VPS)       │       │  (Hostinger)  │
   └──────────────┘       └──────────────┘       └──────────────┘
```

**Pros:** Full control, no per-message costs  
**Cons:** More complex setup, needs VPS

---

### Architecture C: Separate Reverb Subdomain (Recommended for VPS)

```
Main App:    https://www.ivatan.in
Reverb API:  wss://reverb.ivatan.in
```

1. Create `reverb.yourdomain.com` subdomain
2. Point to same Laravel installation
3. Configure `REVERB_HOST=reverb.yourdomain.com`
4. Install SSL on subdomain

---

## 12. Deployment Checklist

### Pre-Deployment
- [ ] Test locally with `php artisan serve`
- [ ] Verify Reverb works locally: `php artisan reverb:start`
- [ ] Test Echo connection in browser console

### Shared Hosting Deployment
- [ ] Keep `BROADCAST_CONNECTION=pusher`
- [ ] Test Pusher connection works
- [ ] Deploy code to Hostinger
- [ ] Run `php artisan migrate`
- [ ] Clear cache: `php artisan cache:clear`

### VPS Deployment (With Reverb)
- [ ] Configure `.env` with Reverb credentials
- [ ] Install Supervisor
- [ ] Configure Supervisor for Reverb
- [ ] Start Reverb server via Supervisor
- [ ] Configure firewall (allow port 8080)
- [ ] Test WebSocket connection: `wss://reverb.yourdomain.com/app`
- [ ] Deploy frontend with correct VITE_ variables

### Post-Deployment
- [ ] Test login
- [ ] Test sending a message
- [ ] Verify real-time events in browser DevTools
- [ ] Check Laravel log for broadcasting errors

---

## 13. Troubleshooting Checklist

### Issue: Real-time not working

**Diagnosis Steps:**
1. Check browser console for connection errors
2. Check Network tab for WebSocket connection (WS)
3. Check Laravel log: `storage/logs/laravel.log`

**Common Fixes:**
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Issue: "Connection refused" error

- Check if Reverb server is running: `php artisan reverb:start`
- Check firewall: `sudo ufw allow 8080/tcp`
- Verify port in `.env` matches command

### Issue: Channel authorization failed

- Check `routes/channels.php` - ensure participant check works
- Check user is still in participants table
- Check user is not banned

### Issue: Pusher fallback not working

- Verify Pusher credentials in `.env`
- Check Pusher dashboard for app status
- Verify key matches in both .env and frontend

### Issue: SSL certificate error

- Install certificate on main domain
- For subdomain: create separate certificate or use wildcard
- Use Let's Encrypt (free) via Hostinger panel

---

## 14. Quick Decision Guide

| Hosting | Recommendation |
|---------|----------------|
| Hostinger Starter/Premium | Keep Pusher - works out of box |
| Hostinger Business (with SSH) | Consider VPS for Reverb |
| Other Shared Hosting | Keep Pusher or switch to VPS |
| VPS (DigitalOcean, Linode, Hetzner) | Use Reverb with Supervisor |
| Dedicated Server | Use Reverb - full control |

---

## 15. Summary

| Item | Recommendation |
|------|----------------|
| **Current Status** | Pusher is active, Reverb configured but not active |
| **Easiest Path** | Keep Pusher, no deployment changes |
| **Best for Scale** | Reverb on VPS with Supervisor |
| **Hostinger Specific** | Use Pusher (shared hosting can't run Reverb daemon) |
| **Cost** | Pusher has free tier, Reverb is free but needs VPS |

**For now with Hostinger shared hosting:** Keep Pusher, it works reliably and requires zero server changes.

---

*End of Production Setup Documentation*