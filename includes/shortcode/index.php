<?php
function my_load_assets_shortcode() {
    wp_enqueue_script('shortcode-js', plugins_url('../admin/assets/js/shortcode.js', __FILE__), array(), false, true);
	wp_enqueue_style('main_css', plugins_url('../admin/assets/css/main.css', __FILE__ ), false, null);

    wp_localize_script('shortcode-js', 'rc_api_nasdaq_obj_shortcode', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
function rc_api_nasdaq_shortcode( $atts = [], $content = null, $tag = '' ) {

	my_load_assets_shortcode();

	$atts = array_change_key_case( (array) $atts, CASE_LOWER );

	$wporg_atts = shortcode_atts(
		array(
			'api_key' => '',
			'database_code' => 'WIKI',
			'dataset_code' => '',
			'start_date' => '',
			'end_date' => ''
		), $atts, $tag
	);

	$api_key = $wporg_atts['api_key'];
	$database_code = strtoupper($wporg_atts['database_code']);
	$dataset_code = strtoupper($wporg_atts['dataset_code']);
	$start_date = $wporg_atts['start_date'];
	$end_date = $wporg_atts['end_date'];
	$url = "https://data.nasdaq.com/api/v3/datasets/$database_code/$dataset_code.json?api_key=$api_key&start_date=$start_date&end_date=$end_date&rows=1";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$response = curl_exec($ch);
	curl_close($ch);

	$data = json_decode($response, true);

	$res = $data['dataset'];

	$openValue = 0.00;
	$closeValue = 0.00;
	$columnNameDate = '';
	$dayAndYear = '';
	$percentageResult = 0.00;
	$percentageResultClass = '';

	if($res['data'] != '') {
		$openValue = $res['data'][0][1] !== null ? number_format($res['data'][0][1], 2, '.', ',') : 0.00;
		$closeValue = $res['data'][0][4] !== null ? number_format($res['data'][0][4], 2, '.', ',') : 0.00;
		$columnNameDate = $res['data'][0][0];
	}

	date_default_timezone_set('America/Sao_Paulo');

	$currentDate = date('Y-m-d H:i:s');

	$month = strtoupper(date('M', strtotime($currentDate)));

	$time = date('H:i:s', strtotime($currentDate));

	$timeCompareInitial = '10:00:00';
	$timeCompareFinal = '16:00:00';

	$msgMarket = ($time >= $timeCompareInitial && $time <= $timeCompareFinal) ? 'CLOSED AT 4:00 PM ET ON '.$month : 'MARKET OPEN' ;

	if($columnNameDate != '') {
		$dayAndYear = date('d, Y', strtotime($columnNameDate));
		$percentageResult = number_format((($closeValue - $openValue) / $openValue * 100), 2, '.', '');
	}

	if($percentageResult != '') {
		if($percentageResult == 0) {
			$percentageResultClass = 'text-light';
		} else if($percentageResult > 0) {
			$percentageResultClass = 'text-success';
		} else {
			$percentageResultClass = 'text-danger';
		}
	}

	if(!$data['quandl_error']) {
		$html = '
			<div class="card-rc-api-nasdaq mt-0">
				<div class="row">
					<div class="col-12">
						<div class="box-title">
							<h4 class="card-title">'.$res['name'].'</h4>
							<h4 class="symbol">('.$res['dataset_code'].')</h4>
						</div>
					</div>
					<div class="col-lg-6 col-6 col-one">
						<div class="c1">
							<span class="sub-title">
								<strong>Nasdaq</strong> <span>Listed</span>
							</span>
							<span class="sub-title">
								<strong>Nasdaq</strong> <span>100</span>
							</span>
						</div>
					</div>
					<div class="col-lg-6 col-6 col-two">
						<p class="lastSalePrice">$'.$openValue.' <span class="'.$percentageResultClass.'">('.$percentageResult.'%)</span></p>
						<p class="marketStatus">'.$msgMarket.'</p>
						<p class="lastTradeTimestamp">'.$dayAndYear.'</p>
					</div>
				</div>
			</div>
		';
	} else {
		$html = '
			<div class="alert alert-danger" role="alert">
				'.$data['quandl_error']['message'].'
			</div>
		';
	}

	if ( ! is_null( $content ) ) {
		$html .= apply_filters( 'the_content', $content );
	}

	return $html;
}

function rc_api_nasdaq_shortcodes_init() {
	add_shortcode( 'rc_api_nasdaq', 'rc_api_nasdaq_shortcode' );
}
