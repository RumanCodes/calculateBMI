<?php
/**
 * Ad Template - Reusable ad component
 * 
 * Usage for single ad: include with $ad_config (single config array)
 * Usage for slider: include with $ad_configs (array of config arrays)
 * 
 * $ad_config = [
 *     'url' => 'https://example.com',
 *     'logo' => 'https://example.com/logo.png',
 *     'title' => 'Site Title',
 *     'tagline' => 'Short tagline',
 *     'description' => 'Longer description text',
 *     'button_text' => 'Visit Site',
 *     'features' => ['Feature 1', 'Feature 2', 'Feature 3'],
 *     'accent_color' => '#hexcolor',
 *     'bg_color' => '#hexcolor',
 *     'light_mode' => false,  // Set true for light backgrounds (cream/white sections)
 * ];
 */

// Check if we have multiple ads for slider
if (isset($ad_configs) && is_array($ad_configs) && count($ad_configs) > 1):
    $slider_id = 'promo-slider-' . uniqid();
?>
<div class="promo-slider" id="<?php echo $slider_id; ?>">
    <div class="promo-slider-track">
        <?php foreach ($ad_configs as $idx => $cfg): ?>
        <div class="promo-slide">
            <?php renderPromoCard($cfg); ?>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="promo-slider-dots">
        <?php foreach ($ad_configs as $idx => $cfg): ?>
            <button class="promo-dot <?php echo $idx === 0 ? 'active' : ''; ?>" data-slide="<?php echo $idx; ?>" aria-label="Slide <?php echo $idx + 1; ?>"></button>
        <?php endforeach; ?>
    </div>
</div>
<script>
(function() {
    const slider = document.getElementById('<?php echo $slider_id; ?>');
    if (!slider) return;
    const track = slider.querySelector('.promo-slider-track');
    const slides = slider.querySelectorAll('.promo-slide');
    const dots = slider.querySelectorAll('.promo-dot');
    let current = 0;
    const total = slides.length;
    let interval = null;

    function goTo(idx) {
        current = idx;
        track.style.transform = 'translateX(-' + (current * 100) + '%)';
        dots.forEach((d, i) => d.classList.toggle('active', i === current));
    }

    dots.forEach(dot => {
        dot.addEventListener('click', function() {
            goTo(parseInt(this.dataset.slide));
            resetInterval();
        });
    });

    function next() { goTo((current + 1) % total); }
    function resetInterval() { clearInterval(interval); interval = setInterval(next, 5000); }
    resetInterval();
})();
</script>
<?php elseif (isset($ad_config) && is_array($ad_config)): ?>
<?php renderPromoCard($ad_config); ?>
<?php endif; ?>
