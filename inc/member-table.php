<?php
/** Public, searchable active member table. @package Samanvay */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Usage: [samanvay_active_members_table per_page="20" title="सक्रिय सदस्य"] */
function samanvay_active_members_table_shortcode( $atts ) {
	global $wpdb;
	$atts = shortcode_atts( array( 'per_page' => 20, 'title' => __( 'हमारे सक्रिय सदस्य', 'samanvay' ) ), $atts, 'samanvay_active_members_table' );
	$per_page = max( 1, min( 100, absint( $atts['per_page'] ) ) );
	$filters = array();
	foreach ( array( 'search', 'state', 'district', 'tehsil' ) as $key ) {
		$filters[ $key ] = isset( $_GET[ 'samt_' . $key ] ) && is_string( $_GET[ 'samt_' . $key ] ) ? sanitize_text_field( wp_unslash( $_GET[ 'samt_' . $key ] ) ) : '';
	}
	$page = isset( $_GET['samt_page'] ) && is_scalar( $_GET['samt_page'] ) ? max( 1, absint( $_GET['samt_page'] ) ) : 1;
	$table = samanvay_members_table();
	$now = current_time( 'mysql', true );
	$where = "status = 'active' AND (is_lifetime_free = 1 OR membership_expires > %s)";
	$args = array( $now );
	if ( '' !== $filters['search'] ) {
		$where .= ' AND (full_name LIKE %s OR state LIKE %s OR district LIKE %s OR tehsil LIKE %s)';
		$like = '%' . $wpdb->esc_like( $filters['search'] ) . '%';
		$args = array_merge( $args, array( $like, $like, $like, $like ) );
	}
	$labels = array( 'state' => __( 'राज्य', 'samanvay' ), 'district' => __( 'जिला', 'samanvay' ), 'tehsil' => __( 'ब्लॉक / तहसील', 'samanvay' ) );
	$options = array();
	foreach ( $labels as $key => $label ) {
		// Column names come only from the fixed allowlist above.
		$options[ $key ] = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT {$key} FROM {$table} WHERE status = 'active' AND (is_lifetime_free = 1 OR membership_expires > %s) AND {$key} <> '' ORDER BY {$key} ASC", $now ) );
		if ( '' !== $filters[ $key ] ) {
			$where .= " AND {$key} = %s";
			$args[] = $filters[ $key ];
		}
	}
	$total = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE {$where}", $args ) );
	$pages = max( 1, (int) ceil( $total / $per_page ) );
	$page = min( $page, $pages );
	$offset = ( $page - 1 ) * $per_page;
	$members = $wpdb->get_results( $wpdb->prepare( "SELECT member_code, full_name, state, district, tehsil, gender, employment_sector FROM {$table} WHERE {$where} ORDER BY full_name ASC, id ASC LIMIT %d OFFSET %d", array_merge( $args, array( $per_page, $offset ) ) ) );
	$gender_labels = array( 'male' => __( 'पुरुष', 'samanvay' ), 'female' => __( 'महिला', 'samanvay' ), 'other' => __( 'अन्य', 'samanvay' ) );
	$employment_labels = array( 'government' => __( 'शासकीय', 'samanvay' ), 'non_government' => __( 'अशासकीय', 'samanvay' ), 'business' => __( 'व्यवसाय', 'samanvay' ), 'housewife' => __( 'गृहिणी', 'samanvay' ) );
	$id = wp_unique_id( 'sam-member-table-' );
	$url = get_permalink();
	$query = array();
	wp_parse_str( (string) wp_parse_url( $url, PHP_URL_QUERY ), $query );
	ob_start();
	?>
	<section class="sam-member-table">
		<?php if ( '' !== $atts['title'] ) : ?><h2><?php echo esc_html( $atts['title'] ); ?></h2><?php endif; ?>
		<form class="sam-member-table__filters" method="get" action="<?php echo esc_url( $url ); ?>">
			<?php foreach ( $query as $key => $value ) : if ( ! is_scalar( $value ) ) { continue; } ?><input type="hidden" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>"><?php endforeach; ?>
			<label for="<?php echo esc_attr( $id ); ?>-search"><?php esc_html_e( 'नाम या स्थान खोजें', 'samanvay' ); ?><input id="<?php echo esc_attr( $id ); ?>-search" type="search" name="samt_search" value="<?php echo esc_attr( $filters['search'] ); ?>"></label>
			<?php foreach ( $labels as $key => $label ) : ?>
			<label for="<?php echo esc_attr( $id . '-' . $key ); ?>"><?php echo esc_html( $label ); ?><select id="<?php echo esc_attr( $id . '-' . $key ); ?>" name="<?php echo esc_attr( 'samt_' . $key ); ?>"><option value=""><?php esc_html_e( 'सभी', 'samanvay' ); ?></option><?php foreach ( array_unique( array_merge( $options[ $key ], array_filter( array( $filters[ $key ] ) ) ) ) as $option ) : ?><option value="<?php echo esc_attr( $option ); ?>" <?php selected( $filters[ $key ], $option ); ?>><?php echo esc_html( $option ); ?></option><?php endforeach; ?></select></label>
			<?php endforeach; ?>
			<button type="submit"><?php esc_html_e( 'खोजें', 'samanvay' ); ?></button><a href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( 'रीसेट करें', 'samanvay' ); ?></a>
		</form>
		<p><?php echo esc_html( sprintf( __( 'कुल सदस्य: %s', 'samanvay' ), number_format_i18n( $total ) ) ); ?></p>
		<div class="sam-member-table__scroll" role="region" aria-label="<?php esc_attr_e( 'सक्रिय सदस्य सूची', 'samanvay' ); ?>" tabindex="0">
		<table><caption class="screen-reader-text"><?php esc_html_e( 'सक्रिय सदस्य सूची', 'samanvay' ); ?></caption><thead><tr><?php foreach ( array( 'क्रमांक', 'सदस्य आईडी', 'सदस्य का नाम', 'राज्य', 'जिला', 'ब्लॉक / तहसील', 'लिंग', 'रोजगार का प्रकार' ) as $label ) : ?><th scope="col"><?php echo esc_html( $label ); ?></th><?php endforeach; ?></tr></thead><tbody>
		<?php foreach ( $members as $index => $member ) : ?><tr><td><?php echo esc_html( number_format_i18n( $offset + $index + 1 ) ); ?></td><td><?php echo esc_html( $member->member_code ?? '—' ); ?></td><th scope="row"><?php echo esc_html( $member->full_name ); ?></th><td><?php echo esc_html( $member->state ); ?></td><td><?php echo esc_html( $member->district ); ?></td><td><?php echo esc_html( $member->tehsil ); ?></td><td><?php echo esc_html( $gender_labels[ $member->gender ] ?? '—' ); ?></td><td><?php echo esc_html( $employment_labels[ $member->employment_sector ] ?? '—' ); ?></td></tr><?php endforeach; ?>
		<?php if ( ! $members ) : ?><tr><td colspan="8"><?php esc_html_e( 'कोई सक्रिय सदस्य नहीं मिला। अन्य नाम या स्थान से खोजें।', 'samanvay' ); ?></td></tr><?php endif; ?>
		</tbody></table></div>
		<?php if ( $pages > 1 ) :
			$pagination_args = array();
			foreach ( $filters as $key => $value ) {
				if ( '' !== $value ) { $pagination_args[ 'samt_' . $key ] = $value; }
			}
			$base = add_query_arg( $pagination_args, $url );
			?><nav class="active-members-directory__pagination" aria-label="<?php esc_attr_e( 'Member directory pages', 'samanvay' ); ?>"><?php echo wp_kses_post( paginate_links( array( 'base' => add_query_arg( 'samt_page', '%#%', $base ), 'format' => '', 'current' => $page, 'total' => $pages, 'prev_text' => '←', 'next_text' => '→' ) ) ); ?></nav>
		<?php endif; ?>
	</section>
	<?php
	return ob_get_clean();
}
add_shortcode( 'samanvay_active_members_table', 'samanvay_active_members_table_shortcode' );
