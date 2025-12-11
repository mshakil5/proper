<div id="cookie-banner">
    <div class="cookie-container">
        <div class="cookie-content">
            <div class="cookie-icon">🍪</div>
            <div class="cookie-text">
                <h3>Cookie Preferences</h3>
                <p>We use cookies to improve your experience, analyze traffic, and support marketing.</p>
                <div class="cookie-links">
                    <a onclick="openSettings()">Customize</a>
                </div>
            </div>
        </div>
        <div class="cookie-buttons">
            <button class="btn btn-reject" onclick="rejectAll()">Reject</button>
            <button class="btn btn-accept" onclick="acceptAll()">Accept All</button>
        </div>
    </div>
</div>

<!-- Settings Modal -->
<div id="settings-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Cookie Settings</h2>
            <button class="close-btn" onclick="closeModal('settings-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="cookie-options">
                <div class="cookie-option">
                    <input type="checkbox" id="essential" checked disabled>
                    <label for="essential">
                        <span class="option-title">Essential Cookies (Required)</span>
                        <span class="option-desc">Always enabled - required for functionality</span>
                    </label>
                </div>
                <div class="cookie-option">
                    <input type="checkbox" id="analytics">
                    <label for="analytics">
                        <span class="option-title">Analytics Cookies</span>
                        <span class="option-desc">Help us understand how you use our site</span>
                    </label>
                </div>
                <div class="cookie-option">
                    <input type="checkbox" id="marketing">
                    <label for="marketing">
                        <span class="option-title">Marketing Cookies</span>
                        <span class="option-desc">Used for targeted advertising</span>
                    </label>
                </div>
                <div class="cookie-option">
                    <input type="checkbox" id="preferences">
                    <label for="preferences">
                        <span class="option-title">Preference Cookies</span>
                        <span class="option-desc">Remember your settings</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-reject" onclick="rejectAll()">Reject All</button>
            <button class="btn btn-accept" onclick="saveSettings()">Save Settings</button>
        </div>
    </div>
</div>

<style>
#cookie-banner {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    z-index: 9998;
    background: linear-gradient(135deg, #fff8f0, #fff3e7);
    padding: 25px 20px;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08);
    transform: translateY(100%);
    transition: transform 0.5s cubic-bezier(0.2, 0.9, 0.2, 1);
    border-top: 2px solid rgba(255, 122, 0, 0.1);
}

#cookie-banner.active { transform: translateY(0); }

.cookie-container {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 30px;
    align-items: center;
}

.cookie-content { display: flex; align-items: center; gap: 20px; }

.cookie-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #ff8a00, #ff5a00);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    flex-shrink: 0;
}

.cookie-text h3 { font-size: 18px; font-weight: 800; margin-bottom: 6px; color: #222; margin: 0; }
.cookie-text p { font-size: 14px; color: #7a7a7a; margin-bottom: 10px; line-height: 1.5; margin: 0 0 10px 0; }

.cookie-links a {
    color: #ff8a00;
    text-decoration: none;
    margin-right: 20px;
    font-weight: 600;
    cursor: pointer;
    font-size: 13px;
    transition: color 0.3s ease;
}

.cookie-links a:hover { color: #ff5a00; }

.cookie-buttons { display: flex; gap: 12px; white-space: nowrap; }

.btn-reject {
    background: rgba(255, 122, 0, 0.1);
    color: #ff8a00;
    border: 1px solid rgba(255, 122, 0, 0.2);
}

.btn-reject:hover { background: rgba(255, 122, 0, 0.15); transform: translateY(-2px); }

.btn-accept {
    background: linear-gradient(90deg, #ff8a00, #ff5a00);
    color: white;
    box-shadow: 0 6px 20px rgba(255, 122, 0, 0.15);
}

.btn-accept:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255, 122, 0, 0.25); }

.modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center; padding: 20px; }
.modal.active { display: flex; }

.modal-content {
    background: white;
    border-radius: 16px;
    max-width: 650px;
    width: 100%;
    max-height: 85vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0,0,0,0.12);
}

.modal-header {
    background: linear-gradient(135deg, #fff8f0, #fff3e7);
    padding: 25px;
    border-bottom: 1px solid rgba(255, 122, 0, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 16px 16px 0 0;
}

.modal-header h2 { margin: 0; font-size: 22px; color: #222; font-weight: 800; }

.close-btn {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #7a7a7a;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    transition: all 0.3s ease;
    padding: 0;
}

.close-btn:hover { background: rgba(255, 122, 0, 0.1); color: #ff8a00; }

.modal-body { padding: 25px; }

.cookie-options { display: flex; flex-direction: column; gap: 12px; }

.cookie-option {
    display: flex;
    align-items: center;
    padding: 14px;
    background: #fbfbfb;
    border-radius: 12px;
    gap: 12px;
    border: 1px solid rgba(255, 122, 0, 0.05);
    transition: all 0.3s ease;
}

.cookie-option:hover { background: rgba(255, 122, 0, 0.05); border-color: rgba(255, 122, 0, 0.1); }

.cookie-option input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #ff8a00;
    flex-shrink: 0;
}

.cookie-option label { flex: 1; cursor: pointer; margin: 0; }
.option-title { font-weight: 600; color: #222; display: block; font-size: 14px; }
.option-desc { font-size: 12px; color: #7a7a7a; display: block; margin-top: 3px; }

.modal-footer { padding: 15px 25px; border-top: 1px solid rgba(0,0,0,0.04); display: flex; gap: 12px; }
.modal-footer .btn { flex: 1; }

@media (max-width: 768px) {
    .cookie-container { grid-template-columns: 1fr; gap: 15px; }
    .cookie-content { flex-direction: column; text-align: center; }
    .cookie-buttons { width: 100%; flex-direction: column; }
    .btn { width: 100%; }
    .cookie-icon { width: 44px; height: 44px; font-size: 20px; }
    .cookie-text h3 { font-size: 16px; }
    .cookie-text p { font-size: 13px; }
    .cookie-links a { display: inline-block; margin-right: 12px; margin-bottom: 8px; }
    .modal-footer { flex-direction: column; }
    #cookie-banner { padding: 20px; }
}
</style>

<script>
function setCookie(name, value, days = 365) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    document.cookie = name + "=" + value + "; expires=" + date.toUTCString() + "; path=/; SameSite=Lax";
}

function getCookie(name) {
    const nameEQ = name + "=";
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        const c = ca[i].trim();
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length);
    }
    return null;
}

function showBanner() { document.getElementById('cookie-banner').classList.add('active'); }
function hideBanner() { document.getElementById('cookie-banner').classList.remove('active'); }
function openSettings() { document.getElementById('settings-modal').classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }

function acceptAll() {
    setCookie('cookie_consent', JSON.stringify({essential: true, analytics: true, marketing: true, preferences: true}));
    hideBanner();
}

function rejectAll() {
    setCookie('cookie_consent', JSON.stringify({essential: true, analytics: false, marketing: false, preferences: false}));
    hideBanner();
    closeModal('settings-modal');
}

function saveSettings() {
    const consent = {
        essential: true,
        analytics: document.getElementById('analytics').checked,
        marketing: document.getElementById('marketing').checked,
        preferences: document.getElementById('preferences').checked
    };
    setCookie('cookie_consent', JSON.stringify(consent));
    hideBanner();
    closeModal('settings-modal');
}

document.getElementById('settings-modal').onclick = function(e) {
    if (e.target === this) closeModal('settings-modal');
};

document.addEventListener('DOMContentLoaded', function() {
    showBanner();
    const existingConsent = getCookie('cookie_consent');
    if (existingConsent) {
        hideBanner();
    }
});
</script>