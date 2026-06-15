/**
 * Admin Dashboard JavaScript
 * Matching Client Layout Functionality
 */

(function () {
    'use strict';

    // Configuration
    const STORAGE_KEYS = {
        siteNotifications: 'admin_site_notifications_enabled',
        deviceNotifications: 'admin_device_notifications_enabled',
        theme: 'admin_theme_mode',
        notifications: 'admin_site_notifications'
    };
    const DEFAULT_TITLE = document.title;
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Server notifications (to be populated from server via window.adminNotifications)
    const serverNotifications = window.adminNotifications || [];
    const serverNotificationIds = new Set(serverNotifications.map((n) => String(n.id)));
    let notifications = [];

    // Flash notifications - will be populated by the layout
    let flashNotifications = window.adminFlashNotifications || [];

    /**
     * Read boolean from localStorage
     */
    function readBool(key, defaultValue) {
        const value = localStorage.getItem(key);
        if (value === null) return defaultValue;
        return value === 'true';
    }

    /**
     * Write boolean to localStorage
     */
    function writeBool(key, value) {
        localStorage.setItem(key, value ? 'true' : 'false');
    }

    /**
     * Get current theme
     */
    function getTheme() {
        return readBool(STORAGE_KEYS.theme, 'light');
    }

    /**
     * Set theme
     */
    function setTheme(mode) {
        const theme = mode === 'dark' ? 'dark' : 'light';
        localStorage.setItem(STORAGE_KEYS.theme, theme);
        document.body.classList.remove('admin-theme-light', 'admin-theme-dark');
        document.body.classList.add('admin-theme-' + theme);
    }

    /**
     * Get stored notifications from localStorage
     */
    function getStoredNotifications() {
        try {
            const raw = localStorage.getItem(STORAGE_KEYS.notifications);
            const parsed = raw ? JSON.parse(raw) : [];
            return Array.isArray(parsed) ? parsed : [];
        } catch (e) {
            return [];
        }
    }

    /**
     * Save notifications to localStorage
     */
    function saveStoredNotifications() {
        const adminOnly = notifications.filter((n) => !serverNotificationIds.has(String(n.id)));
        localStorage.setItem(STORAGE_KEYS.notifications, JSON.stringify(adminOnly.slice(0, 30)));
    }

    /**
     * Merge server and local notifications
     */
    function mergeNotificationSources() {
        const stored = getStoredNotifications();
        const byId = new Map();

        serverNotifications.forEach((n) => byId.set(String(n.id), { ...n, id: String(n.id) }));

        stored.forEach((n, idx) => {
            const id = String(n.id || `admin-${Date.now()}-${idx}`);
            const existing = byId.get(id);
            if (existing) {
                byId.set(id, { ...existing, read: Boolean(existing.read || n.read) });
            } else {
                byId.set(id, {
                    id,
                    title: n.title || 'Notification',
                    message: n.message || '',
                    type: n.type || 'info',
                    time: n.time || new Date().toLocaleString('fr-FR'),
                    read: Boolean(n.read),
                    url: n.url || null,
                    action_label: n.action_label || null
                });
            }
        });

        notifications = [...flashNotifications, ...Array.from(byId.values())];
    }

    /**
     * Update browser badge with unread count
     */
    function updateBrowserBadge(unreadCount) {
        document.title = unreadCount > 0 ? `(${unreadCount}) ${DEFAULT_TITLE}` : DEFAULT_TITLE;

        if ('setAppBadge' in navigator) {
            if (unreadCount > 0) {
                navigator.setAppBadge(unreadCount).catch(() => {});
            } else if ('clearAppBadge' in navigator) {
                navigator.clearAppBadge().catch(() => {});
            }
        }
    }

    /**
     * Get notification icon class
     */
    function getNotificationIcon(type) {
        if (type === 'success') return 'bi-check-circle';
        if (type === 'warning') return 'bi-exclamation-triangle';
        if (type === 'danger') return 'bi-exclamation-octagon';
        return 'bi-info-circle';
    }

    /**
     * Get empty notification HTML
     */
    function getEmptyNotificationHtml(message) {
        return `
            <div class="admin-notif-empty">
                <i class="bi bi-bell-slash"></i>
                <p>${message}</p>
            </div>
        `;
    }

    /**
     * Render notifications in dropdown
     */
    function renderNotifications() {
        const list = document.getElementById('adminNotifList');
        const badge = document.getElementById('adminNotifBadge');
        const markAllBtn = document.getElementById('adminNotifMarkAllRead');
        const clearBtn = document.getElementById('adminNotifClearAll');

        if (!list || !badge) return;

        if (!readBool(STORAGE_KEYS.siteNotifications, true)) {
            list.innerHTML = getEmptyNotificationHtml('Notifications du site désactivées');
            badge.classList.add('d-none');
            if (markAllBtn) markAllBtn.disabled = true;
            if (clearBtn) clearBtn.disabled = true;
            updateBrowserBadge(0);
            return;
        }

        const unreadCount = notifications.filter((n) => !n.read).length;

        if (notifications.length === 0) {
            list.innerHTML = getEmptyNotificationHtml('Aucune notification');
            badge.classList.add('d-none');
            if (markAllBtn) markAllBtn.disabled = true;
            if (clearBtn) clearBtn.disabled = true;
            updateBrowserBadge(0);
            return;
        }

        if (markAllBtn) markAllBtn.disabled = unreadCount === 0;
        if (clearBtn) clearBtn.disabled = false;

        list.innerHTML = notifications.slice(0, 12).map((n) => `
            <div class="admin-notif-item ${n.read ? '' : 'unread'}" data-notif-id="${n.id}">
                <div class="admin-notif-icon ${n.type || 'info'}"><i class="bi ${getNotificationIcon(n.type)}"></i></div>
                <div class="admin-notif-content">
                    <div class="admin-notif-title">${n.title || 'Notification'}</div>
                    <div class="admin-notif-message">${n.message || ''}</div>
                    <div class="admin-notif-meta">
                        <div class="admin-notif-time">${n.time || ''}</div>
                        ${n.read ? '' : '<button type="button" class="admin-notif-read-btn" data-action="mark-read">Marquer lu</button>'}
                    </div>
                    ${n.url ? `<a class="small text-decoration-none" href="${n.url}">${n.action_label || 'Voir'}</a>` : ''}
                </div>
            </div>
        `).join('');

        if (unreadCount > 0) {
            badge.textContent = unreadCount > 99 ? '99+' : String(unreadCount);
            badge.classList.remove('d-none');
        } else {
            badge.classList.add('d-none');
        }

        updateBrowserBadge(unreadCount);
        saveStoredNotifications();
    }

    /**
     * Push new notification
     */
    function pushNotification(title, message, type, withDeviceNotification) {
        if (!readBool(STORAGE_KEYS.siteNotifications, true)) return;

        notifications.unshift({
            id: 'admin-' + Date.now() + '-' + Math.floor(Math.random() * 100000),
            title: title || 'Notification',
            message: message || '',
            type: type || 'info',
            time: new Date().toLocaleString('fr-FR'),
            read: false
        });

        renderNotifications();

        if (
            withDeviceNotification &&
            readBool(STORAGE_KEYS.deviceNotifications, false) &&
            'Notification' in window &&
            Notification.permission === 'granted'
        ) {
            new Notification(title || 'KAFYKA Admin', { body: message || '' });
        }
    }

    /**
     * Post JSON to URL
     */
    async function postJson(url) {
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            return response.ok;
        } catch (e) {
            console.error('Error posting JSON:', e);
            return false;
        }
    }

    /**
     * Initialize sidebar toggle
     */
    function initSidebar() {
        const menuToggle = document.getElementById('adminMenuToggle');
        const sidebar = document.getElementById('adminSidebar');
        const sidebarOverlay = document.getElementById('adminSidebarOverlay');
        const sidebarClose = document.getElementById('adminSidebarClose');

        if (menuToggle && sidebar) {
            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('open');
                if (sidebarOverlay) sidebarOverlay.classList.toggle('active');
            });
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', () => {
                sidebar.classList.remove('open');
                sidebarOverlay.classList.remove('active');
            });
        }

        if (sidebarClose) {
            sidebarClose.addEventListener('click', () => {
                sidebar.classList.remove('open');
                if (sidebarOverlay) sidebarOverlay.classList.remove('active');
            });
        }
    }

    /**
     * Initialize notification actions
     */
    function initNotificationActions() {
        const notifList = document.getElementById('adminNotifList');
        const markAllBtn = document.getElementById('adminNotifMarkAllRead');
        const clearBtn = document.getElementById('adminNotifClearAll');

        if (notifList) {
            notifList.addEventListener('click', async function (event) {
                const btn = event.target.closest('[data-action="mark-read"]');
                if (!btn) return;

                const item = btn.closest('[data-notif-id]');
                const notifId = item?.getAttribute('data-notif-id');
                if (!notifId) return;

                // If it's a server notification, mark as read on server
                if (serverNotificationIds.has(String(notifId))) {
                    const ok = await postJson(`/admin/notifications/${notifId}/read`);
                    if (!ok) return;
                }

                notifications = notifications.map((n) => String(n.id) === String(notifId) ? { ...n, read: true } : n);
                renderNotifications();
            });
        }

        if (markAllBtn) {
            markAllBtn.addEventListener('click', async function () {
                const ok = await postJson('/admin/notifications/read-all');
                if (!ok) return;
                notifications = notifications.map((n) => ({ ...n, read: true }));
                renderNotifications();
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', async function () {
                const ok = await postJson('/admin/notifications/clear');
                if (!ok) return;
                notifications = [];
                renderNotifications();
            });
        }
    }

    /**
     * Initialize preference switches
     */
    async function initPreferenceSwitches() {
        const siteSwitch = document.getElementById('adminSwitchSiteNotif');
        const deviceSwitch = document.getElementById('adminSwitchDeviceNotif');

        if (siteSwitch) {
            siteSwitch.checked = readBool(STORAGE_KEYS.siteNotifications, true);
            siteSwitch.addEventListener('change', function () {
                writeBool(STORAGE_KEYS.siteNotifications, siteSwitch.checked);
                renderNotifications();
                if (siteSwitch.checked) {
                    pushNotification('Notifications activées', 'Les notifications du site sont maintenant actives.', 'success', false);
                }
            });
        }

        if (deviceSwitch) {
            deviceSwitch.checked = readBool(STORAGE_KEYS.deviceNotifications, false);
            deviceSwitch.addEventListener('change', async function () {
                if (!deviceSwitch.checked) {
                    writeBool(STORAGE_KEYS.deviceNotifications, false);
                    pushNotification('Notifications appareil désactivées', 'Les notifications natives sont désactivées.', 'warning', false);
                    return;
                }

                if (!('Notification' in window)) {
                    alert('Votre navigateur ne supporte pas les notifications appareil.');
                    deviceSwitch.checked = false;
                    writeBool(STORAGE_KEYS.deviceNotifications, false);
                    return;
                }

                const permission = await Notification.requestPermission();
                if (permission === 'granted') {
                    writeBool(STORAGE_KEYS.deviceNotifications, true);
                    pushNotification('Notifications appareil activées', 'Les notifications natives sont activées.', 'success', true);
                } else {
                    deviceSwitch.checked = false;
                    writeBool(STORAGE_KEYS.deviceNotifications, false);
                    pushNotification('Notifications appareil refusées', 'Le navigateur a refusé les notifications natives.', 'warning', false);
                }
            });
        }
    }

    /**
     * Initialize operation tracking
     */
    function initOperationTracking() {
        document.querySelectorAll('.admin-page-content form').forEach((form) => {
            form.addEventListener('submit', function () {
                pushNotification('Opération lancée', 'Votre action est en cours de traitement.', 'info', false);
            });
        });

        document.querySelectorAll('.admin-sidebar .admin-nav-item, .admin-breadcrumb a').forEach((link) => {
            link.addEventListener('click', function () {
                const label = (link.textContent || '').trim();
                if (!label) return;
                pushNotification('Navigation', `Ouverture de "${label}".`, 'info', false);
            });
        });
    }

    /**
     * Initialize dashboard
     */
    function init() {
        setTheme(getTheme());
        mergeNotificationSources();
        initSidebar();
        initNotificationActions();
        initPreferenceSwitches();
        initOperationTracking();
        renderNotifications();

        // Expose pushNotification globally
        window.adminPushNotification = pushNotification;
    }

    // Run on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
