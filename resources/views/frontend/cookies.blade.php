<div id="cookie-banner">
    <div class="ck-container">
        <div class="ck-content">
            <div class="ck-icon">🍪</div>
            <div class="ck-text">
                <h3>Cookie Preferences</h3>
                <p>We use cookies to improve your experience, analyze traffic, and support marketing.</p>
                <div class="ck-links">
                    <a onclick="openSettings()">Customize</a>
                </div>
            </div>
        </div>
        <div class="ck-buttons">
            <button class="ck-btn ck-btn-reject" onclick="rejectAll()">Reject</button>
            <button class="ck-btn ck-btn-accept" onclick="acceptAll()">Accept All</button>
        </div>
    </div>
</div>

<div id="settings-modal" class="ck-modal">
    <div class="ck-modal-content">
        <div class="ck-modal-header">
            <h2>Cookie Settings</h2>
            <button class="ck-close-btn" onclick="closeModal('settings-modal')">&times;</button>
        </div>
        <div class="ck-modal-body">
            <div class="ck-options">
                <div class="ck-option">
                    <input type="checkbox" id="ck-essential" checked disabled>
                    <label for="ck-essential">
                        <span class="ck-option-title">Essential Cookies <span class="ck-required-badge">Required</span></span>
                        <span class="ck-option-desc">Always enabled - required for core functionality</span>
                    </label>
                </div>
                <div class="ck-option">
                    <input type="checkbox" id="ck-analytics">
                    <label for="ck-analytics">
                        <span class="ck-option-title">Analytics Cookies</span>
                        <span class="ck-option-desc">Help us understand how you use our site</span>
                    </label>
                </div>
                <div class="ck-option">
                    <input type="checkbox" id="ck-marketing">
                    <label for="ck-marketing">
                        <span class="ck-option-title">Marketing Cookies</span>
                        <span class="ck-option-desc">Used for targeted advertising</span>
                    </label>
                </div>
                <div class="ck-option">
                    <input type="checkbox" id="ck-preferences">
                    <label for="ck-preferences">
                        <span class="ck-option-title">Preference Cookies</span>
                        <span class="ck-option-desc">Remember your settings and choices</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="ck-modal-footer">
            <button class="ck-btn ck-btn-reject" onclick="rejectAll()">Reject All</button>
            <button class="ck-btn ck-btn-accept" onclick="saveSettings()">Save Settings</button>
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
        padding: 20px;
        box-shadow: 0 -4px 30px rgba(255, 90, 0, 0.1);
        transform: translateY(100%);
        transition: transform 0.5s cubic-bezier(0.2, 0.9, 0.2, 1);
        border-top: 2px solid rgba(255, 122, 0, 0.15);
        box-sizing: border-box;
    }

    #cookie-banner.active {
        transform: translateY(0);
    }

    .ck-container {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 30px;
        align-items: center;
    }

    .ck-content {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .ck-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #ff8a00, #ff5a00);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(255, 90, 0, 0.3);
    }

    .ck-text h3 {
        font-size: 16px;
        font-weight: 800;
        color: #222;
        margin: 0 0 4px 0;
    }

    .ck-text p {
        font-size: 13px;
        color: #777;
        margin: 0 0 8px 0;
        line-height: 1.5;
    }

    .ck-links a {
        color: #ff8a00;
        text-decoration: none;
        font-weight: 700;
        cursor: pointer;
        font-size: 13px;
        transition: color 0.2s ease;
        border-bottom: 1px dashed rgba(255,138,0,0.4);
        padding-bottom: 1px;
    }

    .ck-links a:hover {
        color: #ff5a00;
        border-bottom-color: #ff5a00;
    }

    .ck-buttons {
        display: flex;
        gap: 10px;
        white-space: nowrap;
        align-items: center;
    }

    .ck-btn {
        padding: 10px 22px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        border: none;
        transition: all 0.25s ease;
        letter-spacing: 0.2px;
        outline: none;
    }

    .ck-btn-reject {
        background: #fff;
        color: #ff5a00;
        border: 2px solid rgba(255, 90, 0, 0.25);
    }

    .ck-btn-reject:hover {
        background: #fff5f0;
        border-color: #ff5a00;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 90, 0, 0.12);
    }

    .ck-btn-accept {
        background: linear-gradient(135deg, #ff8a00, #ff5a00);
        color: #fff;
        border: 2px solid transparent;
        box-shadow: 0 4px 15px rgba(255, 90, 0, 0.35);
    }

    .ck-btn-accept:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(255, 90, 0, 0.45);
    }

    .ck-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.45);
        z-index: 10000;
        align-items: center;
        justify-content: center;
        padding: 20px;
        box-sizing: border-box;
        backdrop-filter: blur(3px);
    }

    .ck-modal.active {
        display: flex;
    }

    .ck-modal-content {
        background: #fff;
        border-radius: 18px;
        max-width: 550px;
        width: 100%;
        max-height: 85vh;
        overflow-y: auto;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
    }

    .ck-modal-header {
        background: linear-gradient(135deg, #fff8f0, #fff3e7);
        padding: 22px 25px;
        border-bottom: 1px solid rgba(255, 122, 0, 0.12);
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-radius: 18px 18px 0 0;
    }

    .ck-modal-header h2 {
        margin: 0;
        font-size: 20px;
        color: #222;
        font-weight: 800;
    }

    .ck-close-btn {
        background: rgba(255,90,0,0.08);
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #ff5a00;
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s ease;
        padding: 0;
        line-height: 1;
    }

    .ck-close-btn:hover {
        background: rgba(255, 90, 0, 0.18);
    }

    .ck-modal-body {
        padding: 20px 25px;
    }

    .ck-options {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .ck-option {
        display: flex;
        align-items: center;
        padding: 14px 16px;
        background: #fafafa;
        border-radius: 12px;
        gap: 14px;
        border: 1.5px solid #f0f0f0;
        transition: all 0.2s ease;
    }

    .ck-option:hover {
        background: #fff5f0;
        border-color: rgba(255, 90, 0, 0.2);
    }

    .ck-option input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #ff5a00;
        flex-shrink: 0;
    }

    .ck-option label {
        flex: 1;
        cursor: pointer;
        margin: 0;
    }

    .ck-option-title {
        font-weight: 700;
        color: #222;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }

    .ck-required-badge {
        background: rgba(255,90,0,0.1);
        color: #ff5a00;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 20px;
        border: 1px solid rgba(255,90,0,0.2);
    }

    .ck-option-desc {
        font-size: 12px;
        color: #888;
        display: block;
        margin-top: 3px;
    }

    .ck-modal-footer {
        padding: 16px 25px;
        border-top: 1px solid #f0f0f0;
        display: flex;
        gap: 10px;
        border-radius: 0 0 18px 18px;
    }

    .ck-modal-footer .ck-btn {
        flex: 1;
        padding: 12px;
    }

    @media (max-width: 768px) {
        .ck-container {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .ck-content {
            flex-direction: column;
            text-align: center;
        }

        .ck-links {
            justify-content: center;
            display: flex;
        }

        .ck-buttons {
            width: 100%;
        }

        .ck-btn {
            flex: 1;
        }

        .ck-modal-footer {
            flex-direction: column;
        }
    }
    @media (max-width: 480px) {
    #cookie-banner {
        padding: 14px;
    }

    .ck-content {
        flex-direction: row;
        text-align: left;
        align-items: flex-start;
    }

    .ck-icon {
        width: 38px;
        height: 38px;
        font-size: 18px;
        border-radius: 10px;
    }

    .ck-text h3 {
        font-size: 14px;
    }

    .ck-text p {
        font-size: 12px;
        margin-bottom: 6px;
    }

    .ck-buttons {
        flex-direction: row;
        width: 100%;
    }

    .ck-btn {
        flex: 1;
        padding: 9px 10px;
        font-size: 13px;
    }
}
</style>

<script>
    function setCookie(name, value, days = 365) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = name + "=" + encodeURIComponent(value) +
            "; expires=" + date.toUTCString() +
            "; path=/; SameSite=Lax";
    }

    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            const c = ca[i].trim();
            if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length));
        }
        return null;
    }

    function showBanner() { document.getElementById('cookie-banner').classList.add('active'); }
    function hideBanner() { document.getElementById('cookie-banner').classList.remove('active'); }
    function openSettings() { document.getElementById('settings-modal').classList.add('active'); }
    function closeModal(id) { document.getElementById(id).classList.remove('active'); }

    function storeSafeData() {
        let views = parseInt(getCookie('page_views') || 0);
        setCookie('page_views', views + 1);
        setCookie('last_visit', new Date().toISOString());
        if (!getCookie('language')) setCookie('language', navigator.language || 'en');
    }

    function acceptAll() {
        setCookie('cookie_consent', JSON.stringify({ essential: true, analytics: true, marketing: false, preferences: true }));
        storeSafeData();
        hideBanner();
    }

    function rejectAll() {
        setCookie('cookie_consent', JSON.stringify({ essential: true, analytics: false, marketing: false, preferences: false }));
        hideBanner();
        closeModal('settings-modal');
    }

    function saveSettings() {
        const consent = {
            essential: true,
            analytics: document.getElementById('ck-analytics').checked,
            marketing: document.getElementById('ck-marketing').checked,
            preferences: document.getElementById('ck-preferences').checked
        };
        setCookie('cookie_consent', JSON.stringify(consent));
        if (consent.preferences) storeSafeData();
        hideBanner();
        closeModal('settings-modal');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const consent = getCookie('cookie_consent');
        if (!consent) { showBanner(); return; }
        let prefs;
        try { prefs = JSON.parse(consent); } catch (e) { setCookie('cookie_consent', '', -1); showBanner(); return; }
        if (prefs.preferences) storeSafeData();
    });

    document.getElementById('settings-modal').onclick = function (e) {
        if (e.target === this) closeModal('settings-modal');
    };
</script>