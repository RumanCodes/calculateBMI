(function () {
    'use strict';

    const metricRadio = document.querySelector('input[name="unit"][value="metric"]');
    const imperialRadio = document.querySelector('input[name="unit"][value="imperial"]');
    const metricInputs = document.getElementById('metric-inputs');
    const imperialInputs = document.getElementById('imperial-inputs');
    const form = document.querySelector('#calculator-form form');
    const submitBtn = form ? form.querySelector('.btn-submit') : null;
    const livePreview = document.getElementById('live-preview');

    function toggleUnits() {
        const isMetric = metricRadio && metricRadio.checked;
        if (metricInputs) metricInputs.classList.toggle('hidden', !isMetric);
        if (imperialInputs) imperialInputs.classList.toggle('hidden', isMetric);

        if (isMetric) {
            document.querySelectorAll('#imperial-inputs input').forEach(input => {
                if (input.name !== 'unit') input.value = '';
            });
        } else {
            document.querySelectorAll('#metric-inputs input').forEach(input => {
                if (input.name !== 'unit') input.value = '';
            });
        }
        updateLivePreview();
    }

    function calculateBMIFromForm() {
        const isMetric = metricRadio && metricRadio.checked;
        if (isMetric) {
            const weight = parseFloat(document.getElementById('metric_weight')?.value);
            const heightCm = parseFloat(document.getElementById('metric_height')?.value);
            if (!weight || !heightCm || weight <= 0 || heightCm <= 0) return null;
            const heightM = heightCm / 100;
            return +(weight / (heightM * heightM)).toFixed(1);
        } else {
            const lbs = parseFloat(document.getElementById('imperial_weight')?.value);
            const feet = parseInt(document.getElementById('imperial_feet')?.value) || 0;
            const inches = parseInt(document.getElementById('imperial_inches')?.value) || 0;
            if (!lbs || (feet === 0 && inches === 0)) return null;
            const totalInches = (feet * 12) + inches;
            const heightM = totalInches * 0.0254;
            const weightKg = lbs * 0.453592;
            return +(weightKg / (heightM * heightM)).toFixed(1);
        }
    }

    function updateLivePreview() {
        if (!livePreview) return;
        const bmi = calculateBMIFromForm();
        if (bmi !== null && bmi > 0 && bmi < 100) {
            livePreview.textContent = `Estimated BMI: ${bmi}`;
        } else {
            livePreview.textContent = '';
        }
    }

    function animatePointer() {
        const pointer = document.querySelector('.scale-pointer');
        if (!pointer) return;
        const bmi = parseFloat(pointer.getAttribute('data-bmi'));
        if (isNaN(bmi)) return;

        const percentage = Math.min((bmi / 40) * 100, 100);
        requestAnimationFrame(() => {
            pointer.style.left = percentage + '%';
        });
    }

    function setupCalculateAgain() {
        const links = document.querySelectorAll('.calculate-again');
        links.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const formSection = document.getElementById('calculator-form');
                if (formSection) {
                    formSection.scrollIntoView({ behavior: 'smooth' });
                    setTimeout(() => {
                        const firstInput = formSection.querySelector('input[type="number"]:not([name="age"])');
                        if (firstInput) firstInput.focus();
                    }, 500);
                }
            });
        });
    }

    function setupSubmitGuard() {
        if (!form || !submitBtn) return;
        form.addEventListener('submit', function () {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Calculating...';
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Calculate BMI';
            }, 2000);
        });
    }

    if (metricRadio) metricRadio.addEventListener('change', toggleUnits);
    if (imperialRadio) imperialRadio.addEventListener('change', toggleUnits);

    const allInputs = form ? form.querySelectorAll('input[type="number"]') : [];
    allInputs.forEach(input => {
        input.addEventListener('input', updateLivePreview);
    });

    toggleUnits();

    if (document.querySelector('.scale-pointer')) {
        animatePointer();
    }
    setupCalculateAgain();
    setupSubmitGuard();
})();