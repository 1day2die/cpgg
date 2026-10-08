/*
 * Aurora color mode controller.
 * The initial mode is resolved inline in aurora/head.blade.php to avoid a flash;
 * this script keeps it in sync with user choices, other tabs and the OS setting.
 */
(function () {
    'use strict';

    var STORAGE_KEY = 'aurora-theme';
    var MODES = ['light', 'dark', 'system'];
    var root = document.documentElement;
    var media = window.matchMedia('(prefers-color-scheme: dark)');

    function currentMode() {
        var mode = root.getAttribute('data-theme-mode');
        return MODES.indexOf(mode) === -1 ? 'dark' : mode;
    }

    function store(mode) {
        try {
            localStorage.setItem(STORAGE_KEY, mode);
        } catch (e) {}
    }

    function syncControls(mode) {
        document.querySelectorAll('[data-aurora-mode]').forEach(function (item) {
            var active = item.getAttribute('data-aurora-mode') === mode;
            item.classList.toggle('active', active);
            item.setAttribute('aria-checked', active ? 'true' : 'false');
        });
    }

    function apply(mode) {
        var dark = mode === 'dark' || (mode === 'system' && media.matches);
        root.setAttribute('data-theme-mode', mode);
        root.setAttribute('data-theme', dark ? 'aurora-dark' : 'aurora-light');
        // Aurora's tokens replace AdminLTE's dark-mode class, which some views still hardcode.
        document.body.classList.remove('dark-mode');
        syncControls(mode);
    }

    function setMode(mode) {
        if (MODES.indexOf(mode) === -1) {
            return;
        }
        store(mode);
        apply(mode);
    }

    document.addEventListener('click', function (event) {
        var item = event.target.closest('[data-aurora-mode]');
        if (!item) {
            return;
        }
        event.preventDefault();
        setMode(item.getAttribute('data-aurora-mode'));
    });

    media.addEventListener('change', function () {
        if (currentMode() === 'system') {
            apply('system');
        }
    });

    window.addEventListener('storage', function (event) {
        if (event.key === STORAGE_KEY && MODES.indexOf(event.newValue) !== -1) {
            apply(event.newValue);
        }
    });

    // Badges with admin-defined colors (roles) get dark or light text, whichever contrasts more.
    function relativeLuminance(color) {
        var match = color.match(/rgba?\(([^)]+)\)/);
        if (!match) {
            return null;
        }
        var channels = match[1].split(',').slice(0, 3).map(function (value) {
            var c = parseFloat(value) / 255;
            return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
        });
        return 0.2126 * channels[0] + 0.7152 * channels[1] + 0.0722 * channels[2];
    }

    function tuneBadges(scope) {
        (scope || document).querySelectorAll('.badge[style*="background"]').forEach(function (badge) {
            var luminance = relativeLuminance(getComputedStyle(badge).backgroundColor);
            if (luminance !== null) {
                // 0.2 is where dark text (oklch 20%) starts to beat white
                badge.classList.toggle('aurora-badge-on-light', luminance > 0.2);
            }
        });
    }

    if (window.jQuery) {
        window.jQuery(document).on('draw.dt', function (event) {
            tuneBadges(event.target);
        });
    }

    window.Aurora = {
        setMode: setMode,
        getMode: currentMode,
        tuneBadges: tuneBadges
    };

    apply(currentMode());
    tuneBadges();
})();
