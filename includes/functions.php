<?php
declare(strict_types=1);

/**
 * Renders a promo card HTML
 * @param array $cfg Promo configuration array
 */
function renderPromoCard(array $cfg): void {
    $light = !empty($cfg['light_mode']);
    $badge_style = $light
        ? 'background:' . htmlspecialchars($cfg['accent_color']) . '15; color:' . htmlspecialchars($cfg['accent_color']) . '; border-color:' . htmlspecialchars($cfg['accent_color']) . '33;'
        : '';
    $title_color = $light ? 'color:var(--text-dark);' : 'color:var(--white);';
    $tagline_color = $light ? 'color:var(--text-muted);' : 'color:rgba(255,255,255,0.52);';
    $desc_color = $light ? 'color:var(--text-dark);' : 'color:rgba(255,255,255,0.92);';
    ?>
    <div class="promo-card<?php echo $light ? ' promo-card--light' : ''; ?>" style="--ad-accent: <?php echo htmlspecialchars($cfg['accent_color']); ?>; --ad-bg: <?php echo htmlspecialchars($cfg['bg_color']); ?>;">
        <div class="promo-card-inner">
            <div class="promo-card-content">
                <h3 class="promo-card-title" style="<?php echo $title_color; ?>"><?php echo htmlspecialchars($cfg['title']); ?></h3>
                <div class="promo-card-features">
                    <?php foreach ($cfg['features'] as $feature): ?>
                        <span class="promo-card-feature" style="color:<?php echo htmlspecialchars($cfg['accent_color']); ?>; background:<?php echo htmlspecialchars($cfg['accent_color']); ?>15; border-color:<?php echo htmlspecialchars($cfg['accent_color']); ?>33;">
                            <?php echo htmlspecialchars($feature); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
             <a href="<?php echo htmlspecialchars($cfg['url']); ?>" class="promo-card-btn" target="_blank" rel="noopener noreferrer" style="background:<?php echo htmlspecialchars($cfg['accent_color']); ?>; color:#fff;">
                    <?php echo htmlspecialchars($cfg['button_text']); ?>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
        </div>
    </div>
    <?php
}

/**
 * Calculates BMI using WHO standard formula: weight(kg) / height(m)²
 * @param float $weightKg Weight in kilograms
 * @param float $heightM Height in metres
 * @return float BMI rounded to 1 decimal place
 * @throws InvalidArgumentException If weight or height are out of physiological bounds
 */
function calculateBMI(float $weightKg, float $heightM): float {
    if ($weightKg < 1 || $weightKg > 700) {
        throw new InvalidArgumentException('Weight must be between 1 and 700 kg');
    }
    if ($heightM < 0.3 || $heightM > 3.0) {
        throw new InvalidArgumentException('Height must be between 0.3 and 3.0 metres');
    }
    $bmi = $weightKg / ($heightM * $heightM);
    return round($bmi, 1);
}

/**
 * Returns BMI category information based on WHO standards
 * @param float $bmi Calculated BMI value
 * @return array ['label' => string, 'color' => string, 'description' => string, 'advice' => string]
 */
function getBMICategory(float $bmi): array {
    if ($bmi < 18.5) {
        return [
            'label' => 'Underweight',
            'color' => '#3B82F6',
            'description' => 'Your BMI is below the healthy range.',
            'advice' => 'Consider consulting a nutritionist to develop a balanced weight gain plan.'
        ];
    } elseif ($bmi < 25.0) {
        return [
            'label' => 'Normal',
            'color' => '#22C55E',
            'description' => 'Your BMI is within the healthy range.',
            'advice' => 'Maintain your current healthy lifestyle with balanced diet and regular exercise.'
        ];
    } elseif ($bmi < 30.0) {
        return [
            'label' => 'Overweight',
            'color' => '#F59E0B',
            'description' => 'Your BMI is above the healthy range.',
            'advice' => 'Small, sustainable changes to diet and activity can help reach a healthy weight.'
        ];
    } else {
        return [
            'label' => 'Obese',
            'color' => '#EF4444',
            'description' => 'Your BMI is significantly above the healthy range.',
            'advice' => 'Consult a healthcare professional to create a safe, personalized weight management plan.'
        ];
    }
}

/**
 * Converts pounds to kilograms
 * @param float $lbs Weight in pounds
 * @return float Weight in kilograms, rounded to 2 decimal places
 */
function lbsToKg(float $lbs): float {
    return round($lbs * 0.453592, 2);
}

/**
 * Converts feet and inches to metres
 * @param int $feet Number of feet
 * @param int $inches Number of inches
 * @return float Height in metres, rounded to 4 decimal places
 */
function feetInchesToM(int $feet, int $inches): float {
    $totalInches = ($feet * 12) + $inches;
    return round($totalInches * 0.0254, 4);
}

/**
 * Calculates ideal weight range for a given height based on WHO healthy BMI range (18.5–24.9)
 * @param float $heightM Height in metres
 * @return array ['min' => float, 'max' => float] Rounded to 1 decimal place
 */
function getIdealWeightRange(float $heightM): array {
    $heightSq = $heightM * $heightM;
    return [
        'min' => round(18.5 * $heightSq, 1),
        'max' => round(24.9 * $heightSq, 1)
    ];
}

/**
 * Returns age-specific note for users under 18
 * @param float $bmi Calculated BMI (unused for calculation, context only)
 * @param int $age User's age
 * @return string Note if user is under 18, empty string otherwise
 */
function getBMIForAge(float $bmi, int $age): string {
    if ($age < 18) {
        return 'BMI ranges for children and teens are age and sex-specific. Consult a pediatrician for accurate assessment.';
    }
    return '';
}

/**
 * Sanitizes and validates a float value
 * @param mixed $val Input value to sanitize
 * @return float Validated float value
 * @throws InvalidArgumentException If value is not a valid float
 */
function sanitizeFloat(mixed $val): float {
    $filtered = filter_var($val, FILTER_VALIDATE_FLOAT);
    if ($filtered === false) {
        throw new InvalidArgumentException('Invalid numeric value provided');
    }
    return (float) $filtered;
}

/**
 * Validates form input and returns errors and sanitized data
 * @param array $post POST array
 * @return array ['errors' => string[], 'data' => array|null]
 */
function validateInput(array $post): array {
    $errors = [];
    $data = null;

    $unit = $post['unit'] ?? '';
    if (!in_array($unit, ['metric', 'imperial'])) {
        $errors[] = 'Please select a valid unit system.';
        return ['errors' => $errors, 'data' => null];
    }

    try {
        if ($unit === 'metric') {
            $weight = sanitizeFloat($post['metric_weight'] ?? '');
            $heightCm = sanitizeFloat($post['metric_height'] ?? '');
            $heightM = $heightCm / 100;

            if ($weight < 1 || $weight > 700) $errors[] = 'Weight must be between 1 and 700 kg.';
            if ($heightCm < 30 || $heightCm > 300) $errors[] = 'Height must be between 30 and 300 cm.';

            $data = [
                'unit' => 'metric',
                'weight_kg' => $weight,
                'height_m' => $heightM,
                'age' => (isset($post['age']) && $post['age'] !== '') ? (int) $post['age'] : null
            ];
        } else {
            $lbs = sanitizeFloat($post['imperial_weight'] ?? '');
            $feet = (int) ($post['imperial_feet'] ?? 0);
            $inches = (int) ($post['imperial_inches'] ?? 0);
            $weightKg = lbsToKg($lbs);
            $heightM = feetInchesToM($feet, $inches);

            if ($lbs < 2.2 || $lbs > 1543) $errors[] = 'Weight must be between 2.2 and 1543 lbs.';
            if ($feet < 1 || $feet > 9) $errors[] = 'Height must be between 1 and 9 feet.';
            if ($inches < 0 || $inches > 11) $errors[] = 'Inches must be between 0 and 11.';

            $data = [
                'unit' => 'imperial',
                'weight_kg' => $weightKg,
                'height_m' => $heightM,
                'weight_lbs' => $lbs,
                'height_ft' => $feet,
                'height_in' => $inches,
                'age' => (isset($post['age']) && $post['age'] !== '') ? (int) $post['age'] : null
            ];
        }
    } catch (InvalidArgumentException $e) {
        $errors[] = $e->getMessage();
    }

    return ['errors' => $errors, 'data' => $data];
}
?>