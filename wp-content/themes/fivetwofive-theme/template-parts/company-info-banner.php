<?php
/**
 * Company Info Banner
 *
 * Displays company location and phone number from ACF options
 *
 * @package FiveTwoFive_Theme
 */

// Check if ACF is active
if (!function_exists('get_field')) {
	return;
}

// Get ACF field values with proper defaults
$company_location = get_field('company_city_and_state', 'option') ?: 'Sacramento, CA';
$company_phone = get_field('company_phone_number', 'option') ?: '(123) 456-7890';

// Don't display if both fields are empty (ACF not configured)
if (empty($company_location) && empty($company_phone)) {
	return;
}
?>

<div id="company-info" class="bg-dark text-white">
	<div class="container py-2">
		<div class="row align-items-center">
			<?php if ($company_location) : ?>
				<div class="col-6">
					<div class="location">
						<?php echo fivetwofive_theme_get_icon_svg( 'ui', 'map_pin', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted internal SVG markup. ?>
						<span><?php echo esc_html($company_location); ?></span>
					</div>
				</div>
			<?php endif; ?>
			
			<?php if ($company_phone) : ?>
				<div class="col-6 text-end">
					<?php
					// Format phone number for tel: link (remove all non-numeric characters)
					$phone_link = preg_replace('/[^0-9]/', '', $company_phone);
					$phone_display = esc_html($company_phone);
					$phone_href = esc_attr($phone_link);
					?>
					<a class="text-white" href="tel:+<?php echo $phone_href; ?>" aria-label="Call us at <?php echo $phone_display; ?>">
						<?php echo $phone_display; ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
