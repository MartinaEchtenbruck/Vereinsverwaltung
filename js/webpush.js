(function () {
    'use strict';

    const enableButton = document.getElementById('webpush-enable');
    const disableButton = document.getElementById('webpush-disable');
    const testButton = document.getElementById('webpush-test');
    const status = document.getElementById('webpush-status');
    const csrf = document.querySelector('meta[name="webpush-csrf"]')?.content || '';
    if (!enableButton || !status) return;

    const apiBase = '/intern/api/webpush/';

    function setStatus(message, kind) {
        status.textContent = message;
        status.classList.toggle('is-error', kind === 'error');
        status.classList.toggle('is-success', kind === 'success');
    }

    function setBusy(busy) {
        [enableButton, disableButton, testButton].forEach(button => {
            if (button) button.disabled = busy;
        });
    }

    async function api(url, options = {}) {
        const response = await fetch(apiBase + url, {
            credentials: 'same-origin',
            ...options,
            headers: {
                'Accept': 'application/json',
                ...(options.body ? { 'Content-Type': 'application/json' } : {}),
                'X-CSRF-Token': csrf,
                ...(options.headers || {})
            }
        });
        let data;
        try { data = await response.json(); }
        catch (_) { throw new Error('Der Server hat keine gültige JSON-Antwort geliefert.'); }
        if (!response.ok || !data.success) throw new Error(data.error || 'Die Anfrage ist fehlgeschlagen.');
        return data;
    }

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const raw = atob(base64);
        return Uint8Array.from(raw, char => char.charCodeAt(0));
    }

    async function currentSubscription() {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) return null;
        const registration = await navigator.serviceWorker.getRegistration('/intern/');
        if (!registration) return null;
        return registration.pushManager.getSubscription();
    }

    async function refresh() {
        if (!window.isSecureContext || !('serviceWorker' in navigator) || !('PushManager' in window) || !('Notification' in window)) {
            enableButton.disabled = true;
            setStatus('Dieser Browser unterstützt Web Push hier nicht. Bitte einen aktuellen Browser über HTTPS verwenden.', 'error');
            return;
        }
        try {
            const sub = await currentSubscription();
            enableButton.hidden = !!sub;
            disableButton.hidden = !sub;
            testButton.hidden = !sub;
            if (sub) setStatus('Push ist auf diesem Gerät aktiviert.', 'success');
            else if (Notification.permission === 'denied') setStatus('Der Browser blockiert Benachrichtigungen. Ändere die Berechtigung in den Website-Einstellungen des Browsers.', 'error');
            else setStatus('Push ist auf diesem Gerät noch nicht aktiviert.');
        } catch (error) {
            setStatus(error.message || 'Der Push-Status konnte nicht geprüft werden.', 'error');
        }
    }

    enableButton.addEventListener('click', async function () {
        setBusy(true);
        try {
            if (Notification.permission === 'denied') {
                throw new Error('Benachrichtigungen sind im Browser blockiert. Erlaube sie zuerst in den Website-Einstellungen.');
            }
            const permission = await Notification.requestPermission();
            if (permission !== 'granted') throw new Error('Die Berechtigung wurde nicht erteilt.');

            const keyData = await api('public-key.php');
            const registration = await navigator.serviceWorker.register('/intern/sw.js', { scope: '/intern/' });
            await navigator.serviceWorker.ready;
            let subscription = await registration.pushManager.getSubscription();
            if (!subscription) {
                subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(keyData.publicKey)
                });
            }
            await api('subscribe.php', { method: 'POST', body: JSON.stringify(subscription.toJSON()) });
            setStatus('Push wurde für dieses Gerät aktiviert.', 'success');
            await refresh();
        } catch (error) {
            setStatus(error.message || 'Push konnte nicht aktiviert werden.', 'error');
        } finally {
            setBusy(false);
        }
    });

    disableButton.addEventListener('click', async function () {
        setBusy(true);
        try {
            const sub = await currentSubscription();
            if (sub) {
                await api('unsubscribe.php', { method: 'POST', body: JSON.stringify({ endpoint: sub.endpoint }) });
                await sub.unsubscribe();
            }
            setStatus('Push wurde auf diesem Gerät deaktiviert.', 'success');
            await refresh();
        } catch (error) {
            setStatus(error.message || 'Push konnte nicht deaktiviert werden.', 'error');
        } finally {
            setBusy(false);
        }
    });

    testButton.addEventListener('click', async function () {
        setBusy(true);
        try {
            const result = await api('test-send.php', { method: 'POST', body: JSON.stringify({}) });
            if (result.sent > 0) setStatus('Testnachricht wurde an den Push-Dienst übergeben. Sie sollte gleich erscheinen.', 'success');
            else setStatus(result.message || 'Es konnte keine Testnachricht zugestellt werden.', 'error');
        } catch (error) {
            setStatus(error.message || 'Testversand fehlgeschlagen.', 'error');
        } finally {
            setBusy(false);
        }
    });

    refresh();
})();
