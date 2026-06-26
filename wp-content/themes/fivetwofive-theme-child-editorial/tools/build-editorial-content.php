<?php
/**
 * Editorial child — content build script (dev tool, not loaded by the theme).
 *
 * Composes the editorial site structure on the shared local install so the four
 * pages render end-to-end against the #138 components. Idempotent: everything it
 * creates is tagged `_ftf_editorial_build` and removed on the next run, so it is
 * safe to re-run after edits.
 *
 * Content is PII-free placeholder copy/imagery only — real client copy and images
 * are added later (or on a graduated standalone install). Page slugs are
 * namespaced `editorial-*` to avoid colliding with the portfolio's `home`/`about`
 * /`contact`/`work` in the shared database.
 *
 * Run from the WordPress root:
 *   wp eval-file wp-content/themes/fivetwofive-theme-child-editorial/tools/build-editorial-content.php
 *
 * (On LocalWP, prefix php with the mysql socket — see the project memory.)
 *
 * @package FiveTwoFive_Child_Editorial
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return; // Dev tool — only runs under WP-CLI.
}

require_once ABSPATH . 'wp-admin/includes/image.php';

$FLAG     = '_ftf_editorial_build';
$TEMPLATE = 'page-templates/template-module.php';

// -----------------------------------------------------------------------------
// Helpers
// -----------------------------------------------------------------------------

/** Solid-color placeholder image → attachment ID. */
$make_image = function ( $w, $h, $rgb, $title, $parent ) use ( $FLAG ) {
	$upload = wp_upload_dir();
	$name   = 'ftf-editorial-' . sanitize_title( $title ) . '-' . wp_rand( 1000, 9999 ) . '.jpg';
	$path   = trailingslashit( $upload['path'] ) . $name;

	$im = imagecreatetruecolor( $w, $h );
	imagefill( $im, 0, 0, imagecolorallocate( $im, $rgb[0], $rgb[1], $rgb[2] ) );
	imagejpeg( $im, $path, 82 );
	imagedestroy( $im );

	$type = wp_check_filetype( $name, null );
	$att  = wp_insert_attachment( array(
		'post_mime_type' => $type['type'],
		'post_title'     => $title,
		'post_status'    => 'inherit',
		'meta_input'     => array( $FLAG => '1' ),
	), $path, $parent );
	wp_update_attachment_metadata( $att, wp_generate_attachment_metadata( $att, $path ) );
	return $att;
};

/** Get-or-create an ftf_work_category term, return term_id. */
$term = function ( $name ) {
	$t = term_exists( $name, 'ftf_work_category' );
	if ( ! $t ) {
		$t = wp_insert_term( $name, 'ftf_work_category' );
	}
	return is_wp_error( $t ) ? 0 : (int) $t['term_id'];
};

/** Module fragment builders keep the page definitions readable. */
$cm  = function ( $args ) { return array_merge( array( 'acf_fc_layout' => 'module-content-and-media' ), $args ); };
$mc  = function ( $args ) { return array_merge( array( 'acf_fc_layout' => 'module-multi-column' ), $args ); };
$wk  = function ( $args ) { return array_merge( array( 'acf_fc_layout' => 'module-works' ), $args ); };
$cta = function ( $title, $content ) {
	return array(
		'acf_fc_layout' => 'module-cta',
		'title'         => $title,
		'content'       => $content,
		'button'        => array( 'title' => 'Start a conversation', 'url' => home_url( '/' ), 'target' => '' ),
		'module_classes' => 'cta--editorial',
	);
};
$icon = function ( $name ) { return '[fivetwofive_icon group=ui icon=' . $name . ' size=40]'; };

// -----------------------------------------------------------------------------
// 1. Clean up prior build artifacts
// -----------------------------------------------------------------------------
$old = get_posts( array(
	'post_type'   => array( 'ftf_work', 'page', 'attachment' ),
	'post_status' => 'any',
	'numberposts' => -1,
	'fields'      => 'ids',
	'meta_key'    => $FLAG,
	'meta_value'  => '1',
) );
foreach ( $old as $id ) {
	wp_delete_post( $id, true );
}
$prev_menu = wp_get_nav_menu_object( 'Editorial Primary' );
if ( $prev_menu ) {
	wp_delete_nav_menu( $prev_menu->term_id );
}
WP_CLI::log( 'Cleaned up ' . count( $old ) . ' prior post(s)' . ( $prev_menu ? ' + menu' : '' ) . '.' );

// -----------------------------------------------------------------------------
// 2. Works — 6 placeholder case studies (outcome-led titles, sector + discipline)
// -----------------------------------------------------------------------------
$works_spec = array(
	array( 'Cut checkout drop-off by 38%',        array( 'Fintech', 'Product Design' ),    array( 46, 93, 75 ) ),
	array( 'Unified four brands into one system', array( 'Healthcare', 'Brand & Identity' ), array( 110, 108, 100 ) ),
	array( 'Scaled onboarding to 50k users',      array( 'SaaS', 'Web Platform' ),         array( 22, 21, 15 ) ),
	array( 'Rebuilt a flagship app in 12 weeks',  array( 'Logistics', 'Product Strategy' ), array( 70, 80, 70 ) ),
	array( 'Doubled trial-to-paid conversion',    array( 'B2B', 'Growth Design' ),         array( 120, 110, 90 ) ),
	array( 'Shipped a design system org-wide',    array( 'Enterprise', 'Design Systems' ), array( 60, 70, 85 ) ),
);

$work_ids = array();
foreach ( $works_spec as $i => $spec ) {
	list( $title, $terms, $rgb ) = $spec;
	$wid = wp_insert_post( array(
		'post_type'    => 'ftf_work',
		'post_title'   => $title,
		'post_status'  => 'publish',
		'post_excerpt' => 'Placeholder case study seeded for the editorial build.',
		'meta_input'   => array( $FLAG => '1' ),
	) );
	wp_set_object_terms( $wid, array_filter( array_map( $term, $terms ) ), 'ftf_work_category' );
	set_post_thumbnail( $wid, $make_image( 1200, 900, $rgb, $title, $wid ) );
	$work_ids[] = $wid;
}
WP_CLI::log( 'Created ' . count( $work_ids ) . ' works.' );

$hero_img     = $make_image( 1000, 800, array( 239, 234, 220 ), 'Editorial hero', 0 );
$portrait_img = $make_image( 800, 1000, array( 110, 108, 100 ), 'Editorial portrait', 0 );

// -----------------------------------------------------------------------------
// 3. Pages — 4 compositions on the module template
// -----------------------------------------------------------------------------
$previously = $mc( array(
	'title'          => 'Previously',
	'module_classes' => 'previously',
	'columns'        => array(
		array( 'width' => 'col-md-12', 'text' => '<p>Northwind · Globex · Initech · Umbrella · Soylent</p>' ),
	),
) );

$pages = array(
	'editorial-home' => array(
		'title'   => 'Home',
		'modules' => array(
			$cm( array(
				'eyebrow'        => 'Editorial',
				'title'          => 'A clearer way to ship the work that matters',
				'subtitle'       => 'Strategy, design, and delivery — without the handoff gaps.',
				'description'    => '<p>Placeholder intro. Warm paper, near-mono type, one quiet accent.</p>',
				'image_or_video' => 1,
				'image'          => $hero_img,
				'alignment'      => 'right',
				'vertical_alignment' => 'center',
				'module_classes' => 'split-hero',
			) ),
			$previously,
			$wk( array( 'title' => 'Selected work', 'works' => array_slice( $work_ids, 0, 3 ), 'display' => 'grid' ) ),
			$mc( array(
				'module_classes' => 'stats-band',
				'columns'        => array(
					array( 'title' => '12',   'text' => 'Years' ),
					array( 'title' => '40+',  'text' => 'Projects' ),
					array( 'title' => '8',    'text' => 'Sectors' ),
					array( 'title' => '100%', 'text' => 'Referrals' ),
				),
			) ),
			$cta( 'Let’s build something great', '<p>Tell me about the outcome you’re after.</p>' ),
		),
	),

	'editorial-work-with-me' => array(
		'title'   => 'Work with me',
		'modules' => array(
			$cm( array(
				'eyebrow'        => 'Work with me',
				'title'          => 'Three ways we can work together',
				'description'    => '<p>Placeholder intro for the services page.</p>',
				'image_or_video' => 1,
				'image'          => $hero_img,
				'alignment'      => 'left',
				'vertical_alignment' => 'center',
				'module_classes' => 'split-hero',
			) ),
			$mc( array(
				'title'          => 'Ways to work',
				'module_classes' => 'ways-to-work',
				'columns'        => array(
					array( 'text' => $icon( 'ed-strategy' ) . '<h4>Strategy</h4><p>Set direction before building — where the work is headed.</p>' ),
					array( 'text' => $icon( 'ed-collaboration' ) . '<h4>Collaboration</h4><p>Embedded with your team, not handing off from a distance.</p>' ),
					array( 'text' => $icon( 'ed-delivery' ) . '<h4>Delivery</h4><p>Shipped, measured, and handed over clean.</p>' ),
				),
			) ),
			$mc( array(
				'title'   => 'Who I work with',
				'columns' => array(
					array( 'title' => 'Founders', 'text' => '<p>Early teams finding their shape.</p>' ),
					array( 'title' => 'Scale-ups', 'text' => '<p>Teams hardening what already works.</p>' ),
					array( 'title' => 'Enterprises', 'text' => '<p>Orgs aligning many teams on one system.</p>' ),
				),
			) ),
			$cta( 'Have a project in mind?', '<p>Let’s find the right way to work together.</p>' ),
		),
	),

	'editorial-case-studies' => array(
		'title'   => 'Case studies',
		'modules' => array(
			$cm( array(
				'eyebrow'        => 'Case studies',
				'title'          => 'Selected outcomes',
				'description'    => '<p>Placeholder intro for the case-studies index.</p>',
				'image_or_video' => 1,
				'image'          => $hero_img,
				'alignment'      => 'right',
				'vertical_alignment' => 'center',
				'module_classes' => 'split-hero',
			) ),
			$wk( array( 'works' => $work_ids, 'display' => 'grid' ) ),
			$cta( 'Want results like these?', '<p>Tell me about the outcome you’re after.</p>' ),
		),
	),

	'editorial-about' => array(
		'title'   => 'About',
		'modules' => array(
			$cm( array(
				'eyebrow'        => 'About',
				'title'          => 'Hi — I help teams ship the work that matters',
				'description'    => '<p>Placeholder bio intro alongside a portrait.</p>',
				'image_or_video' => 1,
				'image'          => $portrait_img,
				'alignment'      => 'left',
				'vertical_alignment' => 'center',
				'module_classes' => 'portrait',
			) ),
			$mc( array(
				'title'   => 'The throughline',
				'columns' => array(
					array( 'width' => 'col-md-12', 'text' => '<p>Placeholder narrative tying the work together — outcome-led, calm, and clear.</p>' ),
				),
			) ),
			$previously,
			$cta( 'Let’s talk', '<p>Always happy to compare notes.</p>' ),
		),
	),
);

$page_ids = array();
foreach ( $pages as $slug => $spec ) {
	$pid = wp_insert_post( array(
		'post_type'   => 'page',
		'post_title'  => $spec['title'],
		'post_name'   => $slug,
		'post_status' => 'publish',
		'meta_input'  => array(
			$FLAG               => '1',
			'_wp_page_template' => $TEMPLATE,
		),
	) );
	update_field( 'field_5c9b90b7e68cc', $spec['modules'], $pid );
	$page_ids[ $slug ] = $pid;
}
WP_CLI::log( 'Created ' . count( $page_ids ) . ' pages.' );

// -----------------------------------------------------------------------------
// 4. Editorial Primary menu → assigned to the editorial child's primary location
// -----------------------------------------------------------------------------
$menu_id = wp_create_nav_menu( 'Editorial Primary' );
$nav     = array(
	'editorial-home'         => 'Home',
	'editorial-work-with-me' => 'Work with me',
	'editorial-case-studies' => 'Case studies',
	'editorial-about'        => 'About',
);
foreach ( $nav as $slug => $label ) {
	wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'     => $label,
		'menu-item-object'    => 'page',
		'menu-item-object-id' => $page_ids[ $slug ],
		'menu-item-type'      => 'post_type',
		'menu-item-status'    => 'publish',
	) );
}

// Theme mods are per-theme; the editorial child is active, so this leaves the
// portfolio's primary-menu assignment untouched.
$locations                 = (array) get_theme_mod( 'nav_menu_locations' );
$locations['primary_menu'] = $menu_id;
set_theme_mod( 'nav_menu_locations', $locations );
WP_CLI::log( 'Created + assigned the Editorial Primary menu.' );

// -----------------------------------------------------------------------------
WP_CLI::success( 'Editorial content built.' );
foreach ( $page_ids as $slug => $pid ) {
	WP_CLI::log( '  ' . get_permalink( $pid ) );
}
