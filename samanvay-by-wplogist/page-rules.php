<?php
/**
 * Template Name: Rules / नियमावली
 * Template Post Type: page
 * @package Samanvay
 */
get_header();
$phone = samanvay_get_theme_setting( 'phone' );
$registration_page = get_page_by_path( 'member-registration' );
$registration_url = $registration_page ? get_permalink( $registration_page ) : home_url( '/member-registration/' );
$rule_sections = require get_template_directory() . '/inc/rules-content.php';
$topics = array_merge( array( 'सदस्यता की पात्रता' ), array_column( $rule_sections, 'title' ) );
$content = trim( (string) get_post_field( 'post_content', get_queried_object_id() ) );
$custom = '' !== $content;
if ( $custom ) {
	$content = apply_filters( 'the_content', $content );
	$topics = array();
	// Build the contents menu from the editor's H2 headings.
	$content = preg_replace_callback( '/<h2\b([^>]*)>(.*?)<\/h2>/is', function ( $match ) use ( &$topics ) {
		$topics[] = wp_strip_all_tags( $match[2] );
		$attributes = preg_replace( '/\s+id\s*=\s*(?:"[^"]*"|\x27[^\x27]*\x27|[^\s>]+)/i', '', $match[1] );
		return '<h2 id="rules-topic-' . count( $topics ) . '"' . $attributes . '>' . $match[2] . '</h2>';
	}, $content );
}
?>
<main id="primary" class="rules-page">
	<section class="rules-hero">
		<?php if ( has_post_thumbnail() ) : ?><div class="rules-hero__image"><?php the_post_thumbnail( 'full', array( 'alt' => '' ) ); ?></div><?php endif; ?>
		<div class="site-container rules-hero__inner">
			<div><nav aria-label="Breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a><span aria-hidden="true"> › </span>नियमावली</nav><h1>विस्तृत <span>नियमावली</span></h1><h2>सदस्यता एवं सहयोग हेतु अनिवार्य नियम</h2><p>पारदर्शिता • अनुशासन • आपसी विश्वास • मानवीय सहयोग</p></div>
			<div class="rules-hero__quote"><blockquote>“ एक सदस्य की सुरक्षा,<br>सभी की जिम्मेदारी। ”</blockquote><svg viewBox="0 0 280 100" aria-hidden="true"><path d="M15 75Q75 50 140 76Q205 50 265 75v16Q200 70 140 91Q80 70 15 91ZM140 76v15M32 67q55-20 96 0m27 0q50-20 89 0M194 14l35 35m-49-30 20-20 35 35-20 20ZM217 49l39 37"/></svg></div>
		</div>
	</section>
	<div class="site-container rules-layout">
		<aside class="rules-sidebar"><nav class="rules-toc" aria-label="नियमावली के विषय"><h2>नियमावली के सभी विषय</h2><ol><?php foreach ( $topics as $index => $topic ) : ?><li><a href="#rules-topic-<?php echo esc_attr( $index + 1 ); ?>"><span><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span><?php echo esc_html( $topic ); ?></a></li><?php endforeach; ?></ol></nav><div class="rules-help"><h3>कोई प्रश्न है?</h3><p>हमारी टीम आपकी सहायता के लिए सदैव उपलब्ध है।</p><a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^+0-9]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></div></aside>
		<div class="rules-main">
		<?php if ( $custom ) : ?><article class="rules-panel entry-content"><?php echo $content; // Already processed by the_content, like a standard page. ?></article>
		<?php else : ?>
			<header class="rules-topic-heading" id="rules-topic-1"><span>01</span><div><h2>सदस्यता की पात्रता</h2><p>योजना में शामिल होने के लिए आवश्यक पात्रता, नियम और शुल्क</p></div></header>
			<article class="rules-panel">
				<h3 class="rules-strip">कौन सदस्य बन सकता है?</h3>
				<ol class="rules-numbered"><li>योजना की सदस्यता अखिल भारतीय असाटी समाज के लिए मान्य है।</li><li>प्रारंभ में योजना सभी शिक्षकों के लिए लागू की गई है; आगे चलकर इसका विस्तार समाज के अन्य पात्र सदस्यों तक किया जा सकता है।</li><li>जो सदस्य योजना के नियमों के अनुसार नियमित सहयोग करते हैं, वे इस योजना के लिए पात्र बने रहेंगे।</li></ol>
				<div class="rules-note"><span aria-hidden="true">i</span><div><h3>महत्वपूर्ण</h3><p>सदस्यता केवल उन्हीं सदस्यों के लिए मान्य होगी, जो समिति द्वारा निर्धारित नियमों का पालन करते हुए नियमित सहयोग करते हैं।</p></div></div>
				<h3 class="rules-strip">₹ &nbsp; सदस्यता एवं शुल्क</h3>
				<div class="rules-fees"><div><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="7" r="4"/><path d="M2 22v-4a7 7 0 0 1 12-5m4 1v8m-4-4h8"/></svg><div><p>पंजीकरण शुल्क</p><strong>₹<?php echo esc_html( number_format_i18n( absint( samanvay_get_theme_setting( 'registration_fee' ) ) ) ); ?> (निःशुल्क)</strong></div></div><div><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 5h18v17H3ZM7 2v6m10-6v6M3 11h18m-14 4h2m4 0h2m-8 4h2m4 0h2"/></svg><div><p>सदस्यता शुल्क (<?php echo esc_html( absint( samanvay_get_theme_setting( 'membership_duration' ) ) ); ?> दिन)</p><strong>₹<?php echo esc_html( number_format_i18n( absint( samanvay_get_theme_setting( 'membership_fee' ) ) ) ); ?> (निःशुल्क)</strong></div></div></div>
				<div class="rules-note rules-note--important" style="display:none;"><span aria-hidden="true">!</span><div><h3>अनिवार्य नियम</h3><p>सदस्यता एवं शुल्क से संबंधित सभी नियमों का पालन करना प्रत्येक सदस्य के लिए अनिवार्य होगा।</p></div></div>
			</article>
		<?php endif; ?>
			<div class="rules-cta"><div><h3>हम सब साथ हैं</h3><p>आपकी सदस्यता, एक सुरक्षित और सशक्त समाज की ओर एक महत्वपूर्ण कदम है।</p></div><a href="<?php echo esc_url( $registration_url ); ?>">अब सदस्य बनें <span aria-hidden="true">→</span></a></div>
		<?php if ( ! $custom ) : foreach ( $rule_sections as $index => $section ) : ?>
			<section class="rules-panel rules-detail" id="rules-topic-<?php echo esc_attr( $index + 2 ); ?>">
				<h2><?php echo esc_html( $section['title'] ); ?></h2>
				<ol class="rules-numbered"><?php foreach ( $section['paragraphs'] as $paragraph ) : ?><li><?php echo esc_html( $paragraph ); ?></li><?php endforeach; ?></ol>
			</section>
		<?php endforeach; endif; ?>
		</div>
	</div>
</main>
<?php get_footer(); ?>
