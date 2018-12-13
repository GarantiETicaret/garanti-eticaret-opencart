<?php
class ModelPaymentgaranti extends Model {
	public function getMethod($address, $total) {

			$method_data = array(
				'code'       => 'garanti',
				'title'      => 'Kredi kartı ile öde',
				'terms'      => '',
				'sort_order' => 1
			);


		return $method_data;
	}
}