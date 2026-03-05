(function () {
    'use strict';

    var form = document.getElementById('parisii-contact-form');
    if (!form) return;

    var formWrapper = document.getElementById('parisii-contact-form-wrapper');
    var successBlock = document.getElementById('parisii-contact-success');
    var successMessageEl = document.getElementById('parisii-contact-success-message');
    var refreshBtn = document.getElementById('parisii-contact-refresh-btn');
    var alertEl = document.getElementById('parisii-contact-alert');
    var submitBtn = document.getElementById('contact-submit');
    var captchaCodeEl = document.getElementById('contact-captcha-code');
    var captchaInput = document.getElementById('contact-captcha');
    var captchaKeyInput = document.getElementById('contact-captcha-key');
    var captchaRefreshBtn = document.getElementById('contact-captcha-refresh');
    var config = window.parisii_contact_ajax || {};
    var errors = {};
    var touched = {};
    var captchaKey = '';

    function generateKey() {
        return 'c' + Date.now().toString(36) + Math.random().toString(36).slice(2);
    }

    function setError(field, message, forceShow) {
        errors[field] = message || '';
        var show = !!message && (!!touched[field] || !!forceShow);
        var errEl = form.querySelector('.contact-field-error[data-for="' + field + '"]');
        var input = form.querySelector('[name="' + field + '"]');
        if (errEl) {
            errEl.textContent = message || '';
            errEl.classList.toggle('hidden', !show);
        }
        if (input) {
            input.setAttribute('aria-invalid', show ? 'true' : 'false');
        }
    }

    function getError(field) {
        return errors[field] || '';
    }

    function showAlert(type, message) {
        if (!alertEl) return;
        alertEl.className = 'rounded-lg p-4 mb-6 ' + (type === 'success' ? 'bg-green-200 dark:bg-green-800/50 text-green-900 dark:text-green-100 border border-green-400 dark:border-green-600' : 'bg-red-200 dark:bg-red-800/50 text-red-900 dark:text-red-100 border border-red-400 dark:border-red-600');
        alertEl.textContent = message;
        alertEl.classList.remove('hidden');
    }

    function hideAlert() {
        if (alertEl) alertEl.classList.add('hidden');
    }

    function validateEmail(value) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value || '');
    }

    function validateField(name, value) {
        value = (value || '').trim();
        switch (name) {
            case 'nom':
                return value.length ? '' : (config.strings && config.strings.required_nom) || 'Le nom est obligatoire.';
            case 'prenom':
                return value.length ? '' : (config.strings && config.strings.required_prenom) || 'Le prénom est obligatoire.';
            case 'email':
                if (!value) return (config.strings && config.strings.required_email) || 'L\'email est obligatoire.';
                return validateEmail(value) ? '' : (config.strings && config.strings.invalid_email) || 'Email invalide.';
            case 'tel':
                return value.length ? '' : (config.strings && config.strings.required_tel) || 'Le téléphone est obligatoire.';
            case 'sujet':
                return value.length ? '' : (config.strings && config.strings.required_sujet) || 'Le sujet est obligatoire.';
            case 'message':
                return value.length ? '' : (config.strings && config.strings.required_message) || 'Le message est obligatoire.';
            case 'captcha':
                return value.length ? '' : (config.strings && config.strings.required_captcha) || 'Veuillez recopier le code.';
            default:
                return '';
        }
    }

    function validateForm(forceShowErrors) {
        var hasError = false;
        ['nom', 'prenom', 'email', 'tel', 'sujet', 'message', 'captcha'].forEach(function (name) {
            var input = form.querySelector('[name="' + name + '"]');
            var val = input ? input.value : '';
            var msg = validateField(name, val);
            if (msg && forceShowErrors) touched[name] = true;
            errors[name] = msg;
            setError(name, msg, forceShowErrors);
            if (msg) hasError = true;
        });
        submitBtn.disabled = hasError;
        return !hasError;
    }

    function formatInput(input) {
        if (!input) return;
        var format = input.getAttribute('data-format');
        var v = input.value;
        if (format === 'uppercase') input.value = v.toUpperCase();
        if (format === 'capitalize') {
            input.value = v.replace(/(^|\s)\S/g, function (c) { return c.toUpperCase(); });
        }
    }

    function loadCaptcha() {
        captchaKey = generateKey();
        if (captchaKeyInput) captchaKeyInput.value = captchaKey;
        if (!config.ajax_url || !config.nonce) {
            if (captchaCodeEl) captchaCodeEl.textContent = '…';
            return;
        }
        var xhr = new XMLHttpRequest();
        xhr.open('POST', config.ajax_url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            var data;
            try { data = JSON.parse(xhr.responseText); } catch (e) { data = {}; }
            if (data.success && data.data && data.data.code) {
                if (captchaCodeEl) captchaCodeEl.textContent = data.data.code;
                if (captchaInput) captchaInput.value = '';
                setError('captcha', '');
            } else {
                if (captchaCodeEl) captchaCodeEl.textContent = '?';
            }
            validateForm();
        };
        xhr.send('action=parisii_contact_captcha&nonce=' + encodeURIComponent(config.nonce) + '&key=' + encodeURIComponent(captchaKey));
    }

    form.querySelectorAll('input, textarea').forEach(function (el) {
        el.addEventListener('blur', function () {
            var name = this.getAttribute('name');
            if (name) touched[name] = true;
            if (name && name !== 'captcha') formatInput(this);
            setError(name, validateField(name, this.value));
            validateForm();
        });
        el.addEventListener('input', function () {
            var name = this.getAttribute('name');
            setError(name, validateField(name, this.value));
            validateForm();
        });
    });

    if (captchaRefreshBtn) captchaRefreshBtn.addEventListener('click', loadCaptcha);
    if (refreshBtn) refreshBtn.addEventListener('click', function () { window.location.reload(); });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        hideAlert();
        if (!validateForm(true)) return;
        if (!config.ajax_url || !config.nonce) {
            showAlert('error', 'Configuration manquante.');
            return;
        }
        submitBtn.disabled = true;
        var formData = new FormData(form);
        formData.append('action', 'parisii_contact_submit');
        formData.append('nonce', config.nonce);
        var xhr = new XMLHttpRequest();
        xhr.open('POST', config.ajax_url, true);
        xhr.onload = function () {
            var data;
            try { data = JSON.parse(xhr.responseText); } catch (err) { data = {}; }
            if (data.success && data.data && data.data.message) {
                if (formWrapper) formWrapper.classList.add('hidden');
                if (successBlock) {
                    if (successMessageEl) successMessageEl.textContent = data.data.message;
                    successBlock.classList.remove('hidden');
                }
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                if (data.data && data.data.errors) {
                    Object.keys(data.data.errors).forEach(function (field) {
                        touched[field] = true;
                        setError(field, data.data.errors[field]);
                    });
                }
                showAlert('error', (data.data && data.data.message) || 'Une erreur est survenue. Veuillez réessayer.');
                loadCaptcha();
            }
            submitBtn.disabled = !validateForm();
        };
        xhr.onerror = function () {
            showAlert('error', 'Erreur réseau. Veuillez réessayer.');
            submitBtn.disabled = false;
        };
        xhr.send(formData);
    });

    loadCaptcha();
    validateForm();
})();
