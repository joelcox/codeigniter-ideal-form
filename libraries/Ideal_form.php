<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter iDeal form
 *
 * A CodeIgniter library to interact with the iDeal online payment method through form submission.
 *
 * @package        	CodeIgniter
 * @category    	Libraries
 * @author        	Joël Cox
 * @link 			https://github.com/joelcox/codeigniter-ideal-form
 * @link			http://joelcox.nl		
 * @license         http://www.opensource.org/licenses/mit-license.html
 */
class Ideal_form {

	/**
	 * @var	holds the CodeIgniter super object
	 */
	private $_ci;
	
	/**
	 * @var	identifier for this payment
	 */
	public $purchase_id;
	
	/**
	 * @var	description for this payment
	 */
	public $description;
	
	/**
	 * @var	all products assigned to this payment
	 */
	public $products = array();
	
	/**
	 * @var total of all products in cents
	 */
	public $payment_total = 0;
	
	/**
	 * These constants will probably never change, just for good measure
	 */
	const CURRENCY = 'EUR';
	const LANGUAGE = 'NL';
	const PAYMENT_TYPE = 'ideal';

	/**
	 * Constructor
	 */
	public function __construct()
	{
		
		log_message('debug', 'iDeal Form Class Initialized');
		$this->_ci = &get_instance();

		// Load all config items
		$this->_ci->load->config('ideal_form');
		
		// Set payment time
		$this->valid_until = time() + $this->_ci->config->item('ideal_form_valid_until');
	
	}
	
	/**
	 * Assign a purchase id to the payment
	 * @param 	mixed	identifier for the payment
	 * @return 	bool
	 */
	public function purchase_id($id)
	{
	
		$this->purchase_id = $id;
		return TRUE;
	
	}
	
	/**
	 * Assign a description to the payment
	 * @param 	mixed 	description for the payment
	 * @return bool
	 */
	public function description($description)
	{
	
		$this->description = $description;
		return TRUE;
		
	}
	
	/**
	 * Add a new product
	 * @param 	int		item identifier
	 * @param	string	description of the product
	 * @param	double	price of the item
	 * @param	int		amount ordered
	 * @return 	bool
	 */
	public function product($id, $description, $price, $quantity = 1)
	{
	
		$product = array(
			'number' => $id,
			'description' => $description,
			'price' => str_replace('.', '', $price),
			'quantity' => $quantity
		);
	
		$this->products[] = $product;
		$this->payment_total += str_replace('.', '', $price) * $quantity;

		return TRUE;
		
	}
	
	/**
	 * Renders the form
	 * @return 	string
	 */ 
	public function render_form()
	{
	
		$this->_ci->load->helper('form');
		
		$html = form_open($this->_ci->config->item('ideal_form_endpoint'));

		// General fields
		$html .= form_hidden('merchantID', $this->_ci->config->item('ideal_form_merchant_id'));
		$html .= form_hidden('subID', $this->_ci->config->item('ideal_form_sub_id'));
		$html .= form_hidden('currency', self::CURRENCY);
		$html .= form_hidden('language', self::LANGUAGE);
		$html .= form_hidden('paymentType', self::PAYMENT_TYPE);
		
		// Redirect URLs
		$html .= form_hidden('urlSuccess', $this->_ci->config->item('ideal_form_url_success'));
		$html .= form_hidden('urlCancel', $this->_ci->config->item('ideal_form_url_cancel'));
		$html .= form_hidden('urlError', $this->_ci->config->item('ideal_form_url_error'));
		
		// Order specific
		$html .= form_hidden('validUntil', date('Y-m-d\TH:i:s', $this->valid_until) . '.SSSZ');
		$html .= form_hidden('purchaseID', $this->purchase_id);
		$html .= form_hidden('description', $this->description);
		$html .= form_hidden('amount', $this->payment_total);
		$html .= form_hidden('hash', $this->_process_hash());
		
		// Append the products and hash
		$html .= $this->_process_products();
		
		$html .= form_submit('ideal', 'Start iDeal betaling');
		$html .= form_close();
		
		return $html;
	
	}
	
	/**
	 * Loops the products array and writes the different products to hidden fields
	 * @return 	string
	 */
	private function _process_products()
	{
	
		$html = '';
	
		for ($i = 0; $i < count($this->products); $i++)
		{
			
			$html .= form_hidden('itemNumber' . ($i + 1), $this->products[$i]['number']);
			$html .= form_hidden('itemDescription' . ($i + 1), $this->products[$i]['description']);
			$html .= form_hidden('itemPrice' . ($i + 1), $this->products[$i]['price']);
			$html .= form_hidden('itemQuantity' . ($i + 1), $this->products[$i]['quantity']);
		
		}
		
		return $html;
	
	}
	
	/**
	 * Calculates the hash for the transaction
	 * @return 	string
	 */
	private function _process_hash()
	{
	
		$concat = $this->_ci->config->item('ideal_form_merchant_key') . $this->_ci->config->item('ideal_form_merchant_id');
		$concat .= $this->_ci->config->item('ideal_form_sub_id') . $this->payment_total . $this->purchase_id;
		$concat .= self::PAYMENT_TYPE . date('Y-m-d\TH:i:s', $this->valid_until) . '.SSSZ';		
	
		foreach ($this->products as $product)
		{
			$concat .= $product['number'] . $product['description'] . $product['quantity'] . $product['price']; 
		}

		$whitespace = array("\t", "\n", "\r", " ");
		$concat = str_replace($whitespace, '', html_entity_decode($concat));
		
		return sha1($concat);
	
	}
	
}