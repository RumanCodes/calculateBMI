(function () {
    'use strict';

    const metricRadio   = document.querySelector('input[name="unit"][value="metric"]');
    const imperialRadio = document.querySelector('input[name="unit"][value="imperial"]');
    const metricInputs  = document.getElementById('metric-inputs');
    const imperialInputs= document.getElementById('imperial-inputs');
    const form          = document.getElementById('calculator-form');
    const submitBtn     = form ? form.querySelector('.btn-calculate') : null;
    const livePreview   = document.getElementById('live-preview');
    const bmiDisplay    = document.getElementById('bmi-display');
    const scalePointer  = document.getElementById('scale-pointer');
    const categoryBadge = document.getElementById('category-badge');
    const recalcBtn     = document.getElementById('recalc-btn');

    function calcBMI() {
        const isMetric = metricRadio && metricRadio.checked;
        if (isMetric) {
            const w  = parseFloat(document.getElementById('metric_weight')?.value);
            const hc = parseFloat(document.getElementById('metric_height')?.value);
            if (!w || !hc || w <= 0 || hc <= 0) return null;
            const hm = hc / 100;
            return +(w / (hm * hm)).toFixed(1);
        } else {
            const lbs  = parseFloat(document.getElementById('imperial_weight')?.value);
            const feet = parseInt(document.getElementById('imperial_feet')?.value)   || 0;
            const ins  = parseInt(document.getElementById('imperial_inches')?.value) || 0;
            if (!lbs || (feet === 0 && ins === 0)) return null;
            const hm  = (feet * 12 + ins) * 0.0254;
            const wkg = lbs * 0.453592;
            return +(wkg / (hm * hm)).toFixed(1);
        }
    }

    function pointerPos(bmi) {
        if (bmi <= 10)  return 0;
        if (bmi < 18.5) return (bmi - 10) / 8.5 * 20;
        if (bmi < 25.0) return 20 + (bmi - 18.5) / 6.5 * 27;
        if (bmi < 30.0) return 47 + (bmi - 25.0) / 5.0 * 22;
        return Math.min(69 + (bmi - 30.0) / 15.0 * 31, 97);
    }

    function bmiColor(bmi) {
        if (bmi < 18.5) return '#60a5fa';
        if (bmi < 25.0) return '#4ade80';
        if (bmi < 30.0) return '#fbbf24';
        return '#f87171';
    }

    function bmiLabel(bmi) {
        if (bmi < 18.5) return 'Underweight';
        if (bmi < 25.0) return 'Normal';
        if (bmi < 30.0) return 'Overweight';
        return 'Obese';
    }

    function updatePanel() {
        const bmi = calcBMI();
        if (bmi !== null && bmi > 5 && bmi < 100) {
            const color = bmiColor(bmi);
            const label = bmiLabel(bmi);
            const pos   = pointerPos(bmi);
            if (bmiDisplay) { bmiDisplay.textContent = bmi; bmiDisplay.style.color = color; }
            if (scalePointer) { scalePointer.style.left = pos + '%'; scalePointer.style.opacity = '1'; }
            if (categoryBadge) {
                categoryBadge.textContent      = label;
                categoryBadge.style.color      = color;
                categoryBadge.style.borderColor= color + '44';
                categoryBadge.style.background = color + '22';
            }
            if (livePreview) livePreview.textContent = '';
        } else {
            if (bmiDisplay) { bmiDisplay.textContent = '—'; bmiDisplay.style.color = 'rgba(255,255,255,0.22)'; }
            if (scalePointer) scalePointer.style.opacity = '0';
            if (livePreview) livePreview.textContent = '';
        }
    }

    function toggleUnits() {
        const isMetric = metricRadio && metricRadio.checked;
        if (metricInputs)   metricInputs.classList.toggle('hidden', !isMetric);
        if (imperialInputs) imperialInputs.classList.toggle('hidden', isMetric);
        if (isMetric) {
            document.querySelectorAll('#imperial-inputs input').forEach(i => { if (i.name !== 'unit') i.value = ''; });
        } else {
            document.querySelectorAll('#metric-inputs input').forEach(i => { if (i.name !== 'unit') i.value = ''; });
        }
        updatePanel();
    }

    function setupSubmitGuard() {
        if (!form || !submitBtn) return;
        form.addEventListener('submit', function () {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Calculating…';
            setTimeout(() => { submitBtn.disabled = false; submitBtn.textContent = 'Calculate BMI'; }, 3000);
        });
    }

    if (recalcBtn) {
        recalcBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (form) form.scrollIntoView({ behavior: 'smooth', block: 'start' });
            setTimeout(() => { const f = form ? form.querySelector('input[type="number"]') : null; if (f) f.focus(); }, 500);
        });
    }

    if (window.innerWidth < 960 && scalePointer && parseFloat(scalePointer.style.opacity) === 1) {
        setTimeout(() => { const p = document.querySelector('.result-panel'); if (p) p.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 300);
    }

    if (metricRadio)   metricRadio.addEventListener('change', toggleUnits);
    if (imperialRadio) imperialRadio.addEventListener('change', toggleUnits);
    if (form) form.querySelectorAll('input[type="number"]').forEach(el => el.addEventListener('input', updatePanel));

    toggleUnits();
    setupSubmitGuard();
})();
