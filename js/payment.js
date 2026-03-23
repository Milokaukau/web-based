/* ═══════════════════════════════════════════════════════
   payment.js
   - Payment method toggle (card / TNG)
   - Card number formatting + active-field highlight
   - Expiry date formatting + future-date validation
   - CVV: digits only, max 3, with real-time feedback
═══════════════════════════════════════════════════════ */

// ─── Helper: apply / remove active styling on a field ─────────────────────────
function setActive(containerId, labelId, iconId, active) {
    const container = document.getElementById(containerId);
    const label     = document.getElementById(labelId);
    const icon      = document.getElementById(iconId);

    if (!container) return;

    if (active) {
        container.classList.add('active-field');
        if (label) label.classList.add('active-label');
        if (icon)  icon.classList.add('active-icon');
    } else {
        container.classList.remove('active-field');
        if (label) label.classList.remove('active-label');
        if (icon)  icon.classList.remove('active-icon');
    }
}

// ─── Helper: show / clear inline error OUTSIDE and BELOW the input-container ──
function setError(containerId, message) {
    const container = document.getElementById(containerId);
    if (!container) return;

    // Error lives as the next sibling of the container, outside the border
    let err = container.nextElementSibling;
    if (err && !err.classList.contains('field-error')) err = null;

    if (message) {
        container.style.borderColor = '#ef4444';
        if (!err) {
            err = document.createElement('p');
            err.className = 'field-error';
            container.insertAdjacentElement('afterend', err);
        }
        err.textContent = message;
    } else {
        container.style.borderColor = '';
        if (err) err.remove();
    }
}

// ══════════════════════════════════════════════════════════
// 1. Payment method toggle
// ══════════════════════════════════════════════════════════
document.querySelectorAll('.pay-method').forEach(method => {
    method.addEventListener('click', function () {
        document.querySelectorAll('.pay-method').forEach(m => m.classList.remove('active'));
        this.classList.add('active');

        const radio      = this.querySelector('input[type="radio"]');
        radio.checked    = true;
        const cardSection = document.getElementById('card-details-section');
        const tngSection  = document.getElementById('tng-details-section');

        if (radio.value === 'tng') {
            cardSection.style.display = 'none';
            tngSection.style.display  = 'block';
            cardSection.querySelectorAll('input').forEach(i => i.required = false);
        } else {
            cardSection.style.display = 'block';
            tngSection.style.display  = 'none';
            cardSection.querySelectorAll('input').forEach(i => i.required = true);
        }
    });
});

// ══════════════════════════════════════════════════════════
// 2. Card Number — format + active highlight when filled
// ══════════════════════════════════════════════════════════
const ccNum = document.getElementById('cc-num');

ccNum.addEventListener('input', function () {
    // Format: groups of 4 digits separated by spaces
    let value = this.value.replace(/\D/g, '');
    if (value.length > 16) value = value.slice(0, 16);
    const grouped = value.match(/.{1,4}/g);
    this.value = grouped ? grouped.join(' ') : value;

    // Highlight only when at least one digit has been entered
    setActive('cc-num-container', 'cc-num-label', 'cc-num-icon', value.length > 0);
});

// ══════════════════════════════════════════════════════════
// 3. Expiry Date — format MM/YY + must be future month
// ══════════════════════════════════════════════════════════
const ccExp = document.getElementById('cc-exp');

ccExp.addEventListener('input', function () {
    let value = this.value.replace(/\D/g, '');
    if (value.length > 4) value = value.slice(0, 4);

    if (value.length > 2) {
        this.value = value.substring(0, 2) + '/' + value.substring(2);
    } else {
        this.value = value;
    }

    // Only validate once we have a full MM/YY
    if (value.length === 4) {
        const inputMonth = parseInt(value.substring(0, 2), 10);
        const inputYear  = parseInt('20' + value.substring(2), 10);

        const now       = new Date();
        const nowMonth  = now.getMonth() + 1; // 1–12
        const nowYear   = now.getFullYear();  // e.g. 2026

        // Month must be 01–12
        if (inputMonth < 1 || inputMonth > 12) {
            setError('cc-exp-container', 'Enter a valid month (01–12).');
            return;
        }

        // Must be strictly in the future
        const isExpired = inputYear < nowYear ||
                          (inputYear === nowYear && inputMonth < nowMonth);

        if (isExpired) {
            setError('cc-exp-container', 'Your card is expired.');
        } else {
            setError('cc-exp-container', null);
        }
    } else {
        setError('cc-exp-container', null);
    }
});

// ══════════════════════════════════════════════════════════
// 4. CVV — digits only, max 3, block symbols & letters
// ══════════════════════════════════════════════════════════
const cvvInput = document.querySelector('input[name="cvv"]');

// Block any non-digit key before it is typed
cvvInput.addEventListener('keydown', function (e) {
    const allowed = [
        'Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'Home', 'End'
    ];
    if (allowed.includes(e.key)) return;          // navigation keys — allow
    if (e.ctrlKey || e.metaKey) return;           // ctrl+c/v/a — allow
    if (!/^\d$/.test(e.key)) e.preventDefault();  // non-digit — block
});

// Sanitise on paste (strip non-digits, cap at 3)
cvvInput.addEventListener('paste', function (e) {
    e.preventDefault();
    const pasted = (e.clipboardData || window.clipboardData).getData('text');
    const digits = pasted.replace(/\D/g, '').slice(0, 3);
    this.value   = digits;
    validateCvv();
});

// Enforce max-length and show error on input
cvvInput.addEventListener('input', function () {
    this.value = this.value.replace(/\D/g, '').slice(0, 3);
    validateCvv();
});

function validateCvv() {
    const val = cvvInput.value;
    const container = cvvInput.closest('.input-container');
    if (!container) return;

    // Error lives as the next sibling of the container, outside the border
    let err = container.nextElementSibling;
    if (err && !err.classList.contains('cvv-error')) err = null;

    if (val.length > 0 && val.length < 3) {
        container.style.borderColor = '#ef4444'; // red
        if (!err) {
            err = document.createElement('p');
            err.className = 'cvv-error';
            container.insertAdjacentElement('afterend', err);
        }
        err.style.color = '#ef4444';
        err.textContent = 'CVV must be exactly 3 digits.';
    } else if (val.length === 3) {
        container.style.borderColor = '';
        if (err) err.remove();
    } else {
        container.style.borderColor = '';
        if (err) err.remove();
    }
}

// ══════════════════════════════════════════════════════════
// 5. Form submit — run all validations before sending
// ══════════════════════════════════════════════════════════
document.querySelector('.payment-form').addEventListener('submit', function (e) {
    let hasError = false;

    // --- Expiry validation ---
    const expVal = ccExp.value.replace(/\D/g, '');
    if (expVal.length === 4) {
        const inputMonth = parseInt(expVal.substring(0, 2), 10);
        const inputYear  = parseInt('20' + expVal.substring(2), 10);
        const now        = new Date();
        const expired    = inputYear < now.getFullYear() ||
                           (inputYear === now.getFullYear() && inputMonth < now.getMonth() + 1);

        if (inputMonth < 1 || inputMonth > 12 || expired) {
            setError('cc-exp-container', 'Your card is expired.');
            hasError = true;
        }
    } else if (ccExp.required && expVal.length > 0) {
        setError('cc-exp-container', 'Enter a complete expiry date (MM/YY).');
        hasError = true;
    }

    // --- CVV validation ---
    if (cvvInput.value.length !== 3 && cvvInput.required) {
        const container = cvvInput.closest('.input-container');
        container.style.borderColor = '#ef4444';
        let err = container.nextElementSibling;
        if (err && !err.classList.contains('cvv-error')) err = null;
        if (!err) {
            err = document.createElement('p');
            err.className = 'cvv-error';
            container.insertAdjacentElement('afterend', err);
        }
        err.style.color = '#ef4444';
        err.textContent = 'CVV must be exactly 3 digits.';
        hasError = true;
    }

    if (hasError) e.preventDefault();
});