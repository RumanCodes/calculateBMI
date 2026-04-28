<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$errors = [];
$results = null;
$unit = 'metric';

if (isset($_SESSION['bmi_results'])) {
    $results = $_SESSION['bmi_results'];
    $unit = $results['unit'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $unit = $_POST['unit'] ?? 'metric';
    $validation = validateInput($_POST);
    $errors = $validation['errors'];
    if (empty($errors) && $validation['data'] !== null) {
        $data = $validation['data'];
        try {
            $bmi = calculateBMI($data['weight_kg'], $data['height_m']);
            $category = getBMICategory($bmi);
            $idealRange = getIdealWeightRange($data['height_m']);
            $ageNote = '';
            if ($data['age'] !== null && $data['age'] < 18) {
                $ageNote = getBMIForAge($bmi, $data['age']);
            }
            $_SESSION['bmi_results'] = [
                'bmi'        => $bmi,
                'category'   => $category,
                'ideal_range'=> $idealRange,
                'age_note'   => $ageNote,
                'unit'       => $unit,
                'weight_kg'  => $data['weight_kg'],
                'height_m'   => $data['height_m'],
                'weight_lbs' => $data['weight_lbs'] ?? null,
                'height_ft'  => $data['height_ft'] ?? null,
                'height_in'  => $data['height_in'] ?? null,
            ];
            header('Location: index.php');
            exit();
        } catch (InvalidArgumentException $e) {
            $errors[] = $e->getMessage();
        }
    }
}

function pointerPos(float $bmi): float {
    if ($bmi <= 10)  return 0;
    if ($bmi < 18.5) return ($bmi - 10) / 8.5 * 20;
    if ($bmi < 25.0) return 20 + ($bmi - 18.5) / 6.5 * 27;
    if ($bmi < 30.0) return 47 + ($bmi - 25.0) / 5.0 * 22;
    return min(69 + ($bmi - 30.0) / 15.0 * 31, 97);
}

include __DIR__ . '/header.php';
?>

<!-- ── Promo: Top Leaderboard (Slider) ── -->
<div class="promo-container promo-container--top">
    <?php
    $ad_configs = [
        [
            'url' => 'https://suitepress.org/',
            'title' => 'SuitePress',
            'button_text' => 'Explore SuitePress',
            'features' => ['WP Plugins', 'Tech Tutorials', 'Expert Blogs'],
            'accent_color' => '#7dc242',
            'bg_color' => 'rgba(255,255,255,0.05)',
        ],
        [
            'url' => '#',
            'title' => 'Want to list your site? Contact (algorithmsunlocks@gmail.com)',
            'button_text' => 'Your website link',
            'features' => ['',''],
            'accent_color' => '#c346e5',
            'bg_color' => 'rgba(255,255,255,0.05)',
        ],
    ];
    include __DIR__ . '/ad-template.php';
    ?>
</div>

<section class="hero">
    <div class="hero-inner">

        <!-- LEFT: Form -->
        <div class="hero-form-side">
            <h1 class="hero-title">BMI Calculator: BMI Chart, Healthy Ranges, and Tips</h1>

            <form id="calculator-form" class="bmi-form" method="POST" action="">

                <div>
                    <div class="unit-toggle">
                        <label>
                            <input type="radio" name="unit" value="metric" <?php echo $unit === 'metric' ? 'checked' : ''; ?>>
                            Metric
                        </label>
                        <label>
                            <input type="radio" name="unit" value="imperial" <?php echo $unit === 'imperial' ? 'checked' : ''; ?>>
                            Imperial
                        </label>
                    </div>
                </div>

                <!-- Metric inputs -->
                <div id="metric-inputs" class="unit-inputs <?php echo $unit === 'metric' ? '' : 'hidden'; ?>">
                    <div class="form-grid">
                        <div class="form-field">
                            <label for="metric_weight">Weight (kg)</label>
                            <input type="number" step="0.1" id="metric_weight" name="metric_weight"
                                   placeholder="e.g. 70"
                                   value="<?php echo htmlspecialchars($_POST['metric_weight'] ?? ($results && $results['unit'] === 'metric' ? $results['weight_kg'] : '')); ?>">
                            <?php if (in_array('Weight must be between 1 and 700 kg.', $errors)): ?>
                                <span class="field-error">Weight must be between 1 and 700 kg</span>
                            <?php endif; ?>
                        </div>
                        <div class="form-field">
                            <label for="metric_height">Height (cm)</label>
                            <input type="number" step="0.1" id="metric_height" name="metric_height"
                                   placeholder="e.g. 175"
                                   value="<?php echo htmlspecialchars($_POST['metric_height'] ?? ($results && $results['unit'] === 'metric' ? round($results['height_m'] * 100, 1) : '')); ?>">
                            <?php if (in_array('Height must be between 30 and 300 cm.', $errors)): ?>
                                <span class="field-error">Height must be between 30 and 300 cm</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Imperial inputs -->
                <div id="imperial-inputs" class="unit-inputs <?php echo $unit === 'imperial' ? '' : 'hidden'; ?>">
                    <div class="form-grid">
                        <div class="form-field">
                            <label for="imperial_weight">Weight (lbs)</label>
                            <input type="number" step="0.1" id="imperial_weight" name="imperial_weight"
                                   placeholder="e.g. 154"
                                   value="<?php echo htmlspecialchars($_POST['imperial_weight'] ?? ($results ? $results['weight_lbs'] : '')); ?>">
                            <?php if (in_array('Weight must be between 2.2 and 1543 lbs.', $errors)): ?>
                                <span class="field-error">Weight must be between 2.2 and 1543 lbs</span>
                            <?php endif; ?>
                        </div>
                        <div class="form-field">
                            <label>Height</label>
                            <div class="height-pair">
                                <input type="number" id="imperial_feet" name="imperial_feet"
                                       placeholder="ft" min="1" max="9"
                                       value="<?php echo htmlspecialchars($_POST['imperial_feet'] ?? ($results ? $results['height_ft'] : '')); ?>">
                                <input type="number" id="imperial_inches" name="imperial_inches"
                                       placeholder="in" min="0" max="11"
                                       value="<?php echo htmlspecialchars($_POST['imperial_inches'] ?? ($results ? $results['height_in'] : '')); ?>">
                            </div>
                            <?php if (in_array('Height must be between 1 and 9 feet.', $errors) || in_array('Inches must be between 0 and 11.', $errors)): ?>
                                <span class="field-error">Valid height required (1&ndash;9 ft, 0&ndash;11 in)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Age (optional) -->
                <div class="form-field">
                    <label for="age">Age (optional)</label>
                    <input type="number" id="age" name="age" placeholder="e.g. 30" min="1" max="120"
                           value="<?php echo htmlspecialchars($_POST['age'] ?? ''); ?>">
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="form-errors">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <button type="submit" name="submit" class="btn-calculate">Calculate BMI</button>
                <div id="live-preview" class="live-preview"></div>
            </form>

            <?php if ($results !== null): ?>
            <div class="result-advice">
                <p class="advice-desc"><?php echo htmlspecialchars($results['category']['description']); ?></p>
                <p class="advice-text"><strong>Advice:</strong> <?php echo htmlspecialchars($results['category']['advice']); ?></p>
                <?php if ($results['age_note'] !== ''): ?>
                    <div class="age-note"><?php echo htmlspecialchars($results['age_note']); ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- RIGHT: Result Panel -->
        <div class="hero-result-side">
            <div class="result-panel">
                <p class="result-panel-title">Your BMI</p>

                <div class="bmi-ring-wrap">
                    <div class="bmi-ring-inner">
                        <span class="bmi-value" id="bmi-display"
                              style="color: <?php echo $results ? htmlspecialchars($results['category']['color']) : 'rgba(255,255,255,0.22)'; ?>">
                            <?php echo $results ? $results['bmi'] : '&mdash;'; ?>
                        </span>
                        <span class="bmi-value-sub">BMI</span>
                    </div>
                </div>

                <?php if ($results): ?>
                <div class="category-badge" id="category-badge"
                     style="background-color:<?php echo htmlspecialchars($results['category']['color']); ?>22;
                            color:<?php echo htmlspecialchars($results['category']['color']); ?>;
                            border-color:<?php echo htmlspecialchars($results['category']['color']); ?>44;">
                    <?php echo htmlspecialchars($results['category']['label']); ?>
                </div>
                <?php else: ?>
                <div class="category-badge" id="category-badge">Enter your details</div>
                <?php endif; ?>

                <div class="bmi-scale">
                    <div class="scale-bar">
                        <div class="scale-pointer" id="scale-pointer"
                             style="left: <?php echo $results ? pointerPos($results['bmi']) : '-20'; ?>%;
                                    opacity: <?php echo $results ? '1' : '0'; ?>;">
                        </div>
                    </div>
                    <div class="scale-labels">
                        <span>Under</span>
                        <span>Normal</span>
                        <span>Overweight</span>
                        <span>Obese</span>
                    </div>
                </div>

                <?php if ($results): ?>
                <div class="result-meta">
                    <div class="result-meta-row">
                        <span class="rml">Healthy range</span>
                        <span class="rmv">
                            <?php echo $results['ideal_range']['min']; ?>&ndash;<?php echo $results['ideal_range']['max']; ?> kg
                            <?php if ($results['unit'] === 'imperial'): ?>
                            <small><?php echo round($results['ideal_range']['min'] * 2.20462, 1); ?>&ndash;<?php echo round($results['ideal_range']['max'] * 2.20462, 1); ?> lbs</small>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                <div class="disclaimer-box">
                    BMI is a screening tool, not a medical diagnosis. Consult a healthcare professional for health decisions.
                </div>
                <a href="index.php" class="btn-recalc" id="recalc-btn">Recalculate</a>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>

<!-- ── What is BMI ── -->
<section class="info-sections">
    <div class="info-inner">
        <div class="bmi-explainer">
            <h2>What is BMI?</h2>
            <p>Body Mass Index (BMI) is a measure of body fat based on height and weight. While it is a useful screening tool, it does not account for muscle mass, bone density, or other factors that influence overall health.</p>
            <table class="bmi-table">
                <thead>
                    <tr><th>Category</th><th>BMI Range</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="category-dot" style="background:#60a5fa;"></span>Underweight</td>
                        <td>Below 18.5</td>
                    </tr>
                    <tr>
                        <td><span class="category-dot" style="background:#4ade80;"></span>Normal weight</td>
                        <td>18.5 &ndash; 24.9</td>
                    </tr>
                    <tr>
                        <td><span class="category-dot" style="background:#fbbf24;"></span>Overweight</td>
                        <td>25.0 &ndash; 29.9</td>
                    </tr>
                    <tr>
                        <td><span class="category-dot" style="background:#f87171;"></span>Obese</td>
                        <td>30.0 and above</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- ── Promo: After BMI Info ── -->
<!-- <div class="promo-container"> -->
    <?php
    // $ad_config = [
    //     'url' => 'https://suitepress.org/',
    //     'logo' => 'https://suitepress.org/wp-content/uploads/2024/12/cropped-suitepress-logo.png',
    //     'title' => 'SuitePress',
    //     'tagline' => 'All in one Web Solution',
    //     'description' => 'Everything you need to build, grow, and maintain exceptional WordPress sites. Discover the best plugins, tutorials, and tech insights for 2026.',
    //     'button_text' => 'Explore SuitePress',
    //     'features' => ['WP Plugins', 'Tech Tutorials', 'Expert Blogs'],
    //     'accent_color' => '#4F46E5',
    //     'bg_color' => 'rgba(79,70,229,0.05)',
    //     'light_mode' => true,
    // ];
    // include __DIR__ . '/ad-template.php';
    ?>
<!-- </div> -->

<?php include __DIR__ . '/footer.php'; ?>
