<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Config for the iDeal form library
 * @see ../libraries/iDeal form.php
 */
$config['ideal_form_endpoint'] = '';						// Endpoint for your bank
$config['ideal_form_merchant_id'] = '';						// Merchant id, provided by your bank
$config['ideal_form_sub_id'] = '0';							// Sub account id, probably '0'
$config['ideal_form_merchant_key'] = '';					// Key to salt your hash (generated in your dashboard)
$config['ideal_form_valid_until'] = '86400';				// Time the payment is the valid after initizilation (in seconds)

$config['ideal_form_url_success'] = '';						// Redirect after completing the transaction
$config['ideal_form_url_error'] = '';						// Redirect when an processing error occurs the transaction
$config['ideal_form_url_cancel'] = '';						// Redirect when the payment is canceled