# AGENTS.md

## Project

PHP BMI calculator. Pure PHP 8+ (no framework, no Composer, no database). Vanilla JS + custom CSS (no Bootstrap, no libraries).

## Architecture

- `index.php` — sole entrypoint; uses **Post/Redirect/Get** pattern (POST → 302 → GET with session results) to prevent browser resubmission dialogs on reload
- `includes/functions.php` — all BMI logic, `declare(strict_types=1)`, WHO standard ranges (18.5/25/30 cutoffs)
- `includes/config.php` — loads `.env` manually (no `getenv()` helper), sets error display based on `APP_ENV`
- Results stored in `$_SESSION['bmi_results']` after redirect, persist until next submission (not cleared on display)

## Conventions

- No build step, no test runner, no linting configured
- Medical disclaimer **must** appear on every results display (non-negotiable for public health tool)
- `.htaccess` denies direct access to `includes/` and `.env` files
- Inter font loaded from Google Fonts (single weight: 400,500,600)
- BMI scale bar is CSS-only with `transform: translateX` pointer animation

## Web Implementation Idea
1. https://www.forhers.com/tools/bmi-calculator

## UI Design Notes

- Modern clean medical aesthetic inspired by forhers.com but more minimal
- Color-coded category badges (blue/green/amber/red per WHO)
- Age field added (optional) — under-18 users get pediatric BMI note
- Two-column input layout on desktop, single column on mobile
- Live BMI preview updates as user types (JS-only, non-authoritative)
- Results animate in with fade + slide (`@keyframes fadeIn`)
- Card-based layout with subtle borders, no heavy shadows
- Input focus uses indigo (`#4F46E5`) accent, not default blue
