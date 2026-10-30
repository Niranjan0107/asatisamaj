<?php
/**
 * Template Name: Member Registration
 * Template Post Type: page
 *
 * @package Samanvay
 */

if ( is_user_logged_in() && samanvay_get_member_by_user( get_current_user_id() ) ) {
	wp_safe_redirect( samanvay_get_member_dashboard_url() );
	exit;
}

get_header();
$step       = isset( $_GET['membership_step'] ) ? sanitize_key( wp_unslash( $_GET['membership_step'] ) ) : 'details';
$member_id  = isset( $_GET['member_id'] ) ? absint( $_GET['member_id'] ) : 0;
$token      = isset( $_GET['member_token'] ) ? sanitize_text_field( wp_unslash( $_GET['member_token'] ) ) : '';
$member     = in_array( $step, array( 'family', 'payment' ), true ) ? samanvay_verify_payment_token( $member_id, $token ) : false;
$qr_id      = absint( samanvay_get_theme_setting( 'membership_qr' ) );
$fee        = absint( samanvay_get_theme_setting( 'registration_fee' ) );

$membership_fee        = absint( samanvay_get_theme_setting( 'membership_fee' ) );
$total_fee = $fee + $membership_fee;
$duration   = max( 1, absint( samanvay_get_theme_setting( 'membership_duration' ) ) );

$error_code = isset( $_GET['registration_error'] ) ? sanitize_key( wp_unslash( $_GET['registration_error'] ) ) : '';
if ( in_array( $step, array( 'family', 'payment' ), true ) && ! $member ) {
	$step = 'details';
	$error_code = 'invalid_session';
} elseif ( 'payment' === $step && ! samanvay_member_has_family_details( $member ) ) {
	$step = 'family';
	$error_code = 'required';
}
?>
<main id="primary" class="membership-page">
	<section class="membership-hero" aria-labelledby="membership-title">
		<div class="site-container"><p class="membership-eyebrow">
			<?php esc_html_e( 'समुदाय से जुड़ें', 'samanvay' ); ?></p>
			<h1 id="membership-title"><?php esc_html_e( 'सदस्य पंजीकरण', 'samanvay' ); ?></h1>
			<p><?php //echo esc_html( sprintf( __( '✶ रजिस्ट्रेशन फीस ₹%d (एक बार)', 'samanvay' ), $fee ) ); ?></p>
			<p><?php //echo esc_html( sprintf( __( '✶ सदस्यता ₹%d (प्रतिवर्ष)', 'samanvay' ), $membership_fee ) ); ?></p>
			<p>रजिस्ट्रेशन फीस एवं सदस्यता - <b>निःशुल्क</b></p>
			<small>कृपया फॉर्म में भुगतान पृष्ठ को ख़ाली छोड़ दे </small>
		</div>
	</section>
	<section class="membership-main"><div class="site-container membership-shell">
		<ol class="membership-steps" aria-label="<?php esc_attr_e( 'Registration progress', 'samanvay' ); ?>">
			<li class="<?php echo 'details' === $step ? 'is-current' : 'is-complete'; ?>"><span>1</span><strong><?php esc_html_e( 'व्यक्तिगत जानकारी', 'samanvay' ); ?></strong></li>
			<li class="<?php echo 'family' === $step ? 'is-current' : ( in_array( $step, array( 'payment', 'thanks' ), true ) ? 'is-complete' : '' ); ?>"><span>2</span><strong><?php esc_html_e( 'अन्य जानकारी', 'samanvay' ); ?></strong></li>
			<li class="<?php echo 'payment' === $step ? 'is-current' : ( 'thanks' === $step ? 'is-complete' : '' ); ?>"><span>3</span><strong><?php esc_html_e( 'भुगतान', 'samanvay' ); ?></strong></li>
			<li class="<?php echo 'thanks' === $step ? 'is-current' : ''; ?>"><span>4</span><strong><?php esc_html_e( 'पूर्ण', 'samanvay' ); ?></strong></li>
		</ol>

		<?php if ( $error_code ) : ?><div class="membership-alert membership-alert--error" role="alert"><?php echo esc_html( samanvay_membership_error_message( $error_code ) ); ?></div><?php endif; ?>
		<?php if ( 'details' === $step ) : ?>
		<div class="membership-card">
			<header><h2><?php esc_html_e( 'सदस्य विवरण', 'samanvay' ); ?></h2><p><?php esc_html_e( 'तारांकन (*) वाले सभी फ़ील्ड आवश्यक हैं।', 'samanvay' ); ?></p></header>
			<form class="membership-form" id="samanvay-registration-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
				<input type="hidden" name="action" value="samanvay_register_member"><?php wp_nonce_field( 'samanvay_register_member', 'samanvay_register_nonce' ); ?>
				<p class="membership-honeypot" aria-hidden="true"><label>Company<input type="text" name="company_name" tabindex="-1" autocomplete="off"></label></p>
				<div class="membership-grid">
					<p class="membership-field membership-field--wide"><label for="member-full-name"><?php esc_html_e( 'पूरा नाम', 'samanvay' ); ?> *</label><input id="member-full-name" name="full_name" type="text" autocomplete="name" maxlength="190" required></p>
					<p class="membership-field"><label for="member-email"><?php esc_html_e( 'ईमेल', 'samanvay' ); ?> *</label><input id="member-email" name="email" type="email" autocomplete="email" maxlength="190" required></p>
					<p class="membership-field"><label for="member-mobile"><?php esc_html_e( 'मोबाइल नंबर', 'samanvay' ); ?> *</label><input id="member-mobile" name="mobile" type="tel" autocomplete="tel" inputmode="numeric" pattern="[6-9][0-9]{9}" maxlength="10" placeholder="10 अंकों का मोबाइल नंबर" required></p>
					<p class="membership-field"><label for="member-state"><?php esc_html_e( 'राज्य', 'samanvay' ); ?> *</label><select id="member-state" name="state" required><option value=""><?php esc_html_e( 'राज्य चुनें', 'samanvay' ); ?></option></select></p>
					<p class="membership-field"><label for="member-district"><?php esc_html_e( 'जिला', 'samanvay' ); ?> *</label><select id="member-district" name="district" disabled required><option value=""><?php esc_html_e( 'पहले राज्य चुनें', 'samanvay' ); ?></option></select></p>
					<p class="membership-field"><label for="member-tehsil"><?php esc_html_e( 'ब्लॉक / तहसील', 'samanvay' ); ?> *</label><select id="member-tehsil" name="tehsil" disabled required><option value=""><?php esc_html_e( 'पहले जिला चुनें', 'samanvay' ); ?></option></select></p>
					<p class="membership-field"><label for="member-pin"><?php esc_html_e( 'पिन कोड', 'samanvay' ); ?> *</label><input id="member-pin" name="pin_code" type="text" inputmode="numeric" autocomplete="postal-code" pattern="[1-9][0-9]{5}" maxlength="6" required></p>
					<p class="membership-field membership-field--wide"><label for="member-address"><?php esc_html_e( 'पूरा पता', 'samanvay' ); ?> *</label><textarea id="member-address" name="address" rows="4" autocomplete="street-address" maxlength="1000" required></textarea></p>
				</div>
				<div class="membership-form-error" role="alert" hidden></div>
				<label class="membership-terms" for="member-accept-terms"><input id="member-accept-terms" type="checkbox" name="accept_terms" value="1" required><span><?php esc_html_e( 'मैंने नियमावली पढ़ ली है। एवं मैं सारे बिंदुओं से सहमत हूँ।', 'samanvay' ); ?></span></label>
				<button class="membership-submit" type="submit"><?php esc_html_e( 'आगे बढ़ें और खाता बनाएँ', 'samanvay' ); ?><span aria-hidden="true">→</span></button>
			</form>
		</div>
		<p class="membership-login-link"><?php esc_html_e( 'पहले से सदस्य हैं?', 'samanvay' ); ?> <a href="<?php echo esc_url( samanvay_get_member_login_url() ); ?>"><?php esc_html_e( 'सदस्य लॉगिन करें', 'samanvay' ); ?></a></p>
		<?php elseif ( 'family' === $step ) : ?>
		<div class="membership-card">
			<header><h2><?php esc_html_e( 'अन्य जानकारी', 'samanvay' ); ?></h2><p><?php esc_html_e( 'भुगतान चरण पर जाने से पहले आवश्यक जानकारी भरें।', 'samanvay' ); ?></p></header>
			<form class="membership-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="samanvay_save_member_family_details">
				<input type="hidden" name="member_id" value="<?php echo esc_attr( $member_id ); ?>">
				<input type="hidden" name="member_token" value="<?php echo esc_attr( $token ); ?>">
				<?php wp_nonce_field( 'samanvay_save_family_details', 'samanvay_family_nonce' ); ?>
				<div class="membership-grid">
					<p class="membership-field"><label for="member-gender"><?php esc_html_e( 'लिंग', 'samanvay' ); ?> *</label><select id="member-gender" name="gender" required><option value=""><?php esc_html_e( 'लिंग चुनें', 'samanvay' ); ?></option><option value="male"><?php esc_html_e( 'पुरुष', 'samanvay' ); ?></option><option value="female"><?php esc_html_e( 'महिला', 'samanvay' ); ?></option></select></p>
					<p class="membership-field"><label for="member-date-of-birth"><?php esc_html_e( 'जन्म तिथि', 'samanvay' ); ?> *</label><input id="member-date-of-birth" name="date_of_birth" type="date" max="<?php echo esc_attr( current_time( 'Y-m-d' ) ); ?>" required><small><?php esc_html_e( 'आपका प्रारंभिक पासवर्ड जन्म तिथि DDMMYYYY प्रारूप में होगा। उदाहरण: 05 जनवरी 1990 → 05011990।', 'samanvay' ); ?></small></p>
					<p class="membership-field"><label for="member-father-name"><?php esc_html_e( 'पिता का नाम', 'samanvay' ); ?> *</label><input id="member-father-name" name="father_name" type="text" maxlength="190" required></p>
					<p class="membership-field"><label for="member-employment-sector"><?php esc_html_e( 'रोजगार का प्रकार', 'samanvay' ); ?> *</label><select id="member-employment-sector" name="employment_sector" required><option value=""><?php esc_html_e( 'प्रकार चुनें', 'samanvay' ); ?></option><option value="government"><?php esc_html_e( 'शासकीय', 'samanvay' ); ?></option><option value="non_government"><?php esc_html_e( 'अशासकीय', 'samanvay' ); ?></option><option value="business"><?php esc_html_e( 'व्यवसाय', 'samanvay' ); ?></option><option value="housewife" disabled hidden><?php esc_html_e( 'गृहिणी', 'samanvay' ); ?></option></select></p>
					<p class="membership-field membership-field--wide"><label for="member-marital-status"><?php esc_html_e( 'वैवाहिक स्थिति', 'samanvay' ); ?> *</label><select id="member-marital-status" name="marital_status" required><option value=""><?php esc_html_e( 'वैवाहिक स्थिति चुनें', 'samanvay' ); ?></option><option value="unmarried"><?php esc_html_e( 'अविवाहित', 'samanvay' ); ?></option><option value="married"><?php esc_html_e( 'विवाहित', 'samanvay' ); ?></option><option value="widowed"><?php esc_html_e( 'विधवा / विधुर', 'samanvay' ); ?></option><option value="divorced"><?php esc_html_e( 'तलाकशुदा', 'samanvay' ); ?></option></select></p>
					
					<fieldset class="membership-nominee membership-field--wide"><legend><?php esc_html_e( 'नॉमिनी 1', 'samanvay' ); ?></legend><div class="membership-nominee__grid"><p class="membership-field"><label for="member-nominee-one-name"><?php esc_html_e( 'नॉमिनी का नाम', 'samanvay' ); ?> *</label><input id="member-nominee-one-name" name="nominee_one_name" type="text" maxlength="190" required></p><p class="membership-field"><label for="member-nominee-one-relationship"><?php esc_html_e( 'नॉमिनी का संबंध', 'samanvay' ); ?> *</label><select id="member-nominee-one-relationship" name="nominee_one_relationship" required><option value=""><?php esc_html_e( 'संबंध चुनें', 'samanvay' ); ?></option><?php foreach ( samanvay_get_nominee_relationships() as $relationship_value => $relationship_label ) : ?><option value="<?php echo esc_attr( $relationship_value ); ?>"><?php echo esc_html( $relationship_label ); ?></option><?php endforeach; ?></select></p></div></fieldset>
					<fieldset class="membership-nominee membership-field--wide"><legend><?php esc_html_e( 'नॉमिनी 2', 'samanvay' ); ?></legend><div class="membership-nominee__grid"><p class="membership-field"><label for="member-nominee-two-name"><?php esc_html_e( 'नॉमिनी का नाम', 'samanvay' ); ?> *</label><input id="member-nominee-two-name" name="nominee_two_name" type="text" maxlength="190" required></p><p class="membership-field"><label for="member-nominee-two-relationship"><?php esc_html_e( 'नॉमिनी का संबंध', 'samanvay' ); ?> *</label><select id="member-nominee-two-relationship" name="nominee_two_relationship" required><option value=""><?php esc_html_e( 'संबंध चुनें', 'samanvay' ); ?></option><?php foreach ( samanvay_get_nominee_relationships() as $relationship_value => $relationship_label ) : ?><option value="<?php echo esc_attr( $relationship_value ); ?>"><?php echo esc_html( $relationship_label ); ?></option><?php endforeach; ?></select></p></div></fieldset>
				</div>
				<button class="membership-submit" type="submit"><?php esc_html_e( 'भुगतान चरण पर जाएँ', 'samanvay' ); ?><span aria-hidden="true">→</span></button>
			</form>
		</div>
		<?php elseif ( 'payment' === $step ) : ?>
		<div class="membership-card membership-payment">
			<header><h2><?php esc_html_e( 'पंजीकरण और प्रथम सदस्यता शुल्क का भुगतान करें', 'samanvay' ); ?></h2><p><?php echo esc_html( sprintf( __( 'पंजीकरण शुल्क ₹%1$d + प्रथम सदस्यता शुल्क ₹%2$d = कुल ₹%3$d। कुल भुगतान का प्रमाण जमा करें।', 'samanvay' ), $fee, $membership_fee, $total_fee ) ); ?></p></header>
			<div class="membership-payment__layout"><div class="membership-qr"><?php if ( $qr_id ) : echo wp_get_attachment_image( $qr_id, 'large', false, array( 'alt' => __( 'Payment QR code', 'samanvay' ) ) ); else : ?><div class="membership-qr__missing"><span aria-hidden="true">QR</span><strong><?php esc_html_e( 'Payment QR not configured', 'samanvay' ); ?></strong><small><?php esc_html_e( 'An administrator must add it in Appearance → Theme Settings → Membership.', 'samanvay' ); ?></small></div><?php endif; ?><p><strong>₹<?php echo esc_html( $total_fee ); ?></strong> · <?php esc_html_e( 'पंजीकरण + प्रथम सदस्यता शुल्क', 'samanvay' ); ?></p></div>
			<form class="membership-form membership-upload" method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="samanvay_submit_member_payment"><input type="hidden" name="member_id" value="<?php echo esc_attr( $member_id ); ?>"><input type="hidden" name="member_token" value="<?php echo esc_attr( $token ); ?>"><?php wp_nonce_field( 'samanvay_submit_payment', 'samanvay_payment_nonce' ); ?>
				<?php if ( isset( $_GET['payment_error'] ) ) : ?><div class="membership-alert membership-alert--error"><?php echo esc_html( samanvay_membership_error_message( sanitize_key( wp_unslash( $_GET['payment_error'] ) ) ) ); ?></div><?php endif; ?>
				<p class="membership-field"><label for="payment-transaction-id"><?php esc_html_e( 'Transaction ID (वैकल्पिक)', 'samanvay' ); ?></label><input id="payment-transaction-id" type="text" name="transaction_id" maxlength="100" autocomplete="off"></p>
				<label class="membership-upload-box" for="payment-screenshot"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 16V4m0 0L7 9m5-5 5 5M4 15v4h16v-4"/></svg><strong><?php esc_html_e( 'भुगतान स्क्रीनशॉट अपलोड करें (वैकल्पिक)', 'samanvay' ); ?></strong><span><?php esc_html_e( 'JPG, PNG या WebP · अधिकतम 5 MB', 'samanvay' ); ?></span><input id="payment-screenshot" type="file" name="payment_screenshot" accept="image/jpeg,image/png,image/webp"></label><output class="membership-file-name"></output>
				<p class="membership-note"><?php esc_html_e( 'Transaction ID या भुगतान स्क्रीनशॉट में से कम से कम एक देना आवश्यक है।', 'samanvay' ); ?></p>
				<button class="membership-submit" type="submit"<?php echo $qr_id ? '' : ' disabled'; ?>><?php esc_html_e( 'भुगतान प्रमाण जमा करें', 'samanvay' ); ?><span aria-hidden="true">→</span></button><p class="membership-note"><?php esc_html_e( 'इस भुगतान में पंजीकरण और प्रथम सदस्यता शुल्क दोनों शामिल हैं। प्रशासक की स्वीकृति के बाद सदस्यता सक्रिय होगी। अगला सदस्यता भुगतान डैशबोर्ड से करें।', 'samanvay' ); ?></p>
			</form></div>
		</div>
		<?php else : ?>
		<div class="membership-card membership-thanks"><span class="membership-thanks__icon"><svg viewBox="0 0 64 64" aria-hidden="true"><circle cx="32" cy="32" r="27"/><path d="m19 33 9 9 18-21"/></svg></span><h2><?php esc_html_e( 'धन्यवाद! आपका आवेदन प्राप्त हुआ', 'samanvay' ); ?></h2><p><?php esc_html_e( 'आपके पंजीकरण और प्रथम सदस्यता शुल्क का भुगतान प्रमाण प्राप्त हो गया है। प्रशासक की स्वीकृति के बाद सदस्यता सक्रिय होगी। लॉगिन करके डैशबोर्ड में भुगतान की स्थिति देखें।', 'samanvay' ); ?></p><a class="membership-submit" href="<?php echo esc_url( samanvay_get_member_login_url() ); ?>"><?php esc_html_e( 'सदस्य लॉगिन करें', 'samanvay' ); ?> <span aria-hidden="true">→</span></a></div>
		<?php endif; ?>
	</div></section>
</main>
<?php get_footer(); ?>
