(function () {
    'use strict';

    var form = document.getElementById('parisii-contact-form');
    if (!form) return;

    var contactContent = document.getElementById('parisii-contact-content');
    var formWrapper = document.getElementById('parisii-contact-form-wrapper');
    var alertEl = document.getElementById('parisii-contact-alert');
    var submitBtn = document.getElementById('contact-submit');
    var startedAtInput = document.getElementById('contact-form-started-at');
    var config = window.parisii_contact_ajax || {};
    var errors = {};
    var touched = {};

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

    function createSuccessBlock(message) {
        var existingSuccess = document.getElementById('parisii-contact-success');
        if (existingSuccess) existingSuccess.remove();

        var successBlock = document.createElement('div');
        successBlock.id = 'parisii-contact-success';
        successBlock.className = 'w-full max-w-2xl mx-auto my-8 bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 md:p-8 text-center';
        successBlock.setAttribute('role', 'status');
        successBlock.setAttribute('aria-live', 'polite');
        successBlock.setAttribute('tabindex', '-1');

        var successMessageEl = document.createElement('p');
        successMessageEl.id = 'parisii-contact-success-message';
        successMessageEl.className = 'mb-6 rounded-lg p-4 bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-100 border border-green-300 dark:border-green-700';
        successMessageEl.textContent = message;

        var refreshBtn = document.createElement('button');
        refreshBtn.type = 'button';
        refreshBtn.id = 'parisii-contact-refresh-btn';
        refreshBtn.className = 'px-6 py-3 rounded-lg bg-main hover:bg-main-hover focus:bg-main-focus text-white font-medium focus:ring-2 focus:ring-main focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition-colors cursor-pointer';
        refreshBtn.textContent = (config.strings && config.strings.refresh_page) || 'Rafraîchir la page';
        refreshBtn.addEventListener('click', function () { window.location.reload(); });

        successBlock.appendChild(successMessageEl);
        successBlock.appendChild(refreshBtn);

        var target = contactContent || formWrapper || form;
        if (target && target.parentNode) {
            target.parentNode.insertBefore(successBlock, target);
        }

        return successBlock;
    }

    function showSuccess(message) {
        var successBlock = createSuccessBlock(message);

        if (contactContent) {
            contactContent.classList.add('hidden');
        } else if (formWrapper) {
            formWrapper.classList.add('hidden');
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
        successBlock.focus({ preventScroll: true });
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
                return '';
            case 'sujet':
                return value.length ? '' : (config.strings && config.strings.required_sujet) || 'Le sujet est obligatoire.';
            case 'message':
                return value.length ? '' : (config.strings && config.strings.required_message) || 'Le message est obligatoire.';
            default:
                return '';
        }
    }

    function validateForm(forceShowErrors) {
        var hasError = false;
        ['nom', 'prenom', 'email', 'tel', 'sujet', 'message'].forEach(function (name) {
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

    form.querySelectorAll('input, textarea').forEach(function (el) {
        el.addEventListener('blur', function () {
            var name = this.getAttribute('name');
            if (name) touched[name] = true;
            if (name) formatInput(this);
            setError(name, validateField(name, this.value));
            validateForm();
        });
        el.addEventListener('input', function () {
            var name = this.getAttribute('name');
            setError(name, validateField(name, this.value));
            validateForm();
        });
    });

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
                showSuccess(data.data.message);
            } else {
                if (data.data && data.data.errors) {
                    Object.keys(data.data.errors).forEach(function (field) {
                        touched[field] = true;
                        setError(field, data.data.errors[field]);
                    });
                }
                showAlert('error', (data.data && data.data.message) || 'Une erreur est survenue. Veuillez réessayer.');
            }
            submitBtn.disabled = !validateForm();
        };
        xhr.onerror = function () {
            showAlert('error', 'Erreur réseau. Veuillez réessayer.');
            submitBtn.disabled = false;
        };
        xhr.send(formData);
    });

    if (startedAtInput) startedAtInput.value = Math.floor(Date.now() / 1000).toString();
    validateForm();
})();
