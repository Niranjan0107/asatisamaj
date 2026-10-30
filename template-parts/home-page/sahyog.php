<?php
/**
 * Homepage sahyog section.
 *
 * @package Samanvay
 */

$announcement_query = new WP_Query( array(
	'post_type'           => 'announcement',
	'posts_per_page'      => 5,
	'ignore_sticky_posts' => true,
) );
$archive_url = get_post_type_archive_link( 'announcement' );

$donation = get_option('samaj_donation_options', array());

$donation_enabled = ($donation['enabled'] ?? '0') === '1';

$deceased_name = $donation['deceased_name'] ?? '';
$nominee_name = $donation['nominee_name'] ?? '';
$account_number = $donation['account_number'] ?? '';
$upi_id = $donation['upi_id'] ?? '';
$qr_image = $donation['qr_image'] ?? '';
?>
<section class="home-announcements" aria-labelledby="home-announcements-title">
	<div class="site-container home-announcements__layout">
		<header class="home-announcements__intro">
			<span class="home-announcements__intro-icon"><svg aria-hidden="true" viewBox="0 0 64 64"><path d="M10 38V25l34-13v39L10 38Z"/><path d="M44 24h8c3 0 5 2 5 5v5c0 3-2 5-5 5h-8M14 40l4 13h11l-5-16M8 25H4v13h6"/></svg></span>
			<p><?php esc_html_e( 'दिवंगत परिवारों के सहायतार्थी', 'samanvay' ); ?></p>
			<h2 id="home-announcements-title"><?php esc_html_e( 'सहयोग ', 'samanvay' ); ?></h2>
			<span class="home-announcements__rule"></span>
			<p class="home-announcements__description"><?php esc_html_e( 'किसी अपने को खोने का दुःख अपार होता है। ऐसे कठिन समय में परिवार को भावनात्मक संबल के साथ आर्थिक सहयोग की भी आवश्यकता हो सकती है। आपका छोटा-सा योगदान किसी परिवार के लिए बड़ी राहत बन सकता है।', 'samanvay' ); ?></p>
			<?php if ( $archive_url ) : ?><a class="home-announcements__all" href="<?php //echo esc_url( $archive_url ); ?>"><?php //esc_html_e( 'सभी घोषणाएँ देखें', 'samanvay' ); ?></a><?php endif; ?>
		</header>

		<div class="home-announcements__board">
<?php if ($donation_enabled) : ?>

<section class="samaj-donation">

    <div class="samaj-donation__header">
        <h2>दिवंगत परिवार सहायता विवरण</h2>
        <p>आपका सहयोग, परिवार के लिए एक सहारा</p>
    </div>

    <div class="samaj-donation__body">

        <div class="samaj-donation__qr">

            <?php if (!empty($qr_image)) : ?>
                <img
                    src="<?php echo esc_url($qr_image); ?>"
                    alt="सहायता राशि भुगतान QR कोड">
            <?php endif; ?>

            <h3>स्कैन करके सहयोग करें</h3>
            <p>UPI QR Code</p>

        </div>

        <div class="samaj-donation__details">

            <div class="samaj-donation__item">
                <span>मृतक का नाम</span>
                <strong>
                    <?php echo esc_html($deceased_name); ?>
                </strong>
            </div>

            <div class="samaj-donation__item">
                <span>नामिनी का नाम</span>
                <strong>
                    <?php echo esc_html($nominee_name); ?>
                </strong>
            </div>

            <div class="samaj-donation__item">
                <span>बैंक खाता संख्या</span>
                <strong>
                    <?php echo esc_html($account_number); ?>
                </strong>
            </div>

            <div class="samaj-donation__item">
                <span>UPI ID</span>
                <strong>
                    <?php echo esc_html($upi_id); ?>
                </strong>
            </div>

        </div>

    </div>

</section>

<?php else : ?>
				
				<div class="home-announcements__empty"><svg aria-hidden="true" viewBox="0 0 64 64"><path d="M13 9h38v46H13V9Z"/><path d="M21 22h22M21 31h22M21 40h15"/></svg><h3><?php esc_html_e( 'फिलहाल कोई सहयोग गतिमान नहीं है', 'samanvay' ); ?></h3><p><?php esc_html_e( 'नई सूचना प्रकाशित होने पर वह यहाँ दिखाई देगी।', 'samanvay' ); ?></p></div>
			<?php endif; ?>
		
		</div>
	</div>
</section>

<style>
.samaj-donation {
  --samaj-primary: #087f89;

  padding: 28px;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 18px;
  box-shadow: 0 8px 30px rgba(0,0,0,.06);
  font-family: inherit;
}

.samaj-donation__header {
  text-align: center;
  margin-bottom: 28px;
}

.samaj-donation__header h2 {
  margin: 0 0 8px;
  color: var(--samaj-primary);
  font-size: 26px;
}

.samaj-donation__header p {
  margin: 0;
  color: #666;
}

.samaj-donation__body {
  display: grid;
  grid-template-columns: 0.85fr 1.4fr;
  gap: 30px;
  align-items: stretch;
}

.samaj-donation__qr {
  padding: 24px;
  text-align: center;
  background: #f3f9f9;
  border-radius: 14px;
}

.samaj-donation__qr img {
  display: block;
  width: 100%;
  max-width: 220px;
  aspect-ratio: 1;
  object-fit: contain;
  margin: 0 auto 16px;
}

.samaj-donation__qr h3 {
  margin: 0 0 5px;
  font-size: 17px;
  color: #222;
}

.samaj-donation__qr p {
  margin: 0;
  color: #666;
  font-size: 13px;
}

.samaj-donation__details {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 16px;
}

.samaj-donation__item {
  padding: 20px;
  border: 1px solid #e8ecef;
  border-radius: 12px;
  min-width: 0;
}

.samaj-donation__item span {
  display: block;
  margin-bottom: 10px;
  font-size: 13px;
  color: #707780;
}

.samaj-donation__item strong {
  display: block;
  font-size: 16px;
  color: #20252b;
  overflow-wrap: anywhere;
}

@media (max-width: 600px) {
  .samaj-donation {
    padding: 18px;
  }

  .samaj-donation__body {
    grid-template-columns: 1fr;
    gap: 20px;
  }

  .samaj-donation__details {
    grid-template-columns: 1fr;
    gap: 12px;
  }

  .samaj-donation__header h2 {
    font-size: 22px;
  }
}
</style>