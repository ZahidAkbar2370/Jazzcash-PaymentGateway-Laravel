<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    public function index()
	{
		// $product = DB::select('select * from product where product_id='.$product_id);
		return view('checkout',);
	}

    public function DoCheckout(Request $request)
	{	
		$data = $request->input();
		// print_r($data);
		
		// $product_id = $data['product_id'];
		// $product = DB::select('select * from product where product_id='.$product_id);
		
		//NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN
		//1.
		//get formatted price. remove period(.) from the price
		$temp_amount 	= 20*100;
		$amount_array 	= explode('.', $temp_amount);
		$pp_Amount 		= $amount_array[0];
		//NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN
		
		//NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN
		//2.
		//get the current date and time
		//be careful set TimeZone in config/app.php
		$DateTime 		= new \DateTime();
		$pp_TxnDateTime = $DateTime->format('YmdHis');
		//NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN
		
		//NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN
		//3.
		//to make expiry date and time add one hour to current date and time
		$ExpiryDateTime = $DateTime;
		$ExpiryDateTime->modify('+' . 1 . ' hours');
		$pp_TxnExpiryDateTime = $ExpiryDateTime->format('YmdHis');
		//NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN
		
		//NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN
		//4.
		//make unique transaction id using current date
		$pp_TxnRefNo = 'T'.$pp_TxnDateTime;
		//NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN
		
		//--------------------------------------------------------------------------------
		//$post_data

		$post_data =  array(
			"pp_Version" 			=> "1.1",
			"pp_TxnType" 			=> "MWALLET",
			"pp_Language" 			=> "EN",
			"pp_MerchantID" 		=> "MC58185",
			"pp_SubMerchantID" 		=> "",
			"pp_Password" 			=> "0sbbxg5544",
			"pp_BankID" 			=> "TBANK",
			"pp_ProductID" 			=> "RETL",
			"pp_TxnRefNo" 			=> $pp_TxnRefNo,
			"pp_Amount" 			=> $pp_Amount,
			"pp_TxnCurrency" 		=> "PKR",
			"pp_TxnDateTime" 		=> $pp_TxnDateTime,
			"pp_BillReference" 		=> "billRef",
			"pp_Description" 		=> "Description of transaction",
			"pp_TxnExpiryDateTime" 	=> $pp_TxnExpiryDateTime,
			"pp_ReturnURL" 			=> "http://127.0.0.1:8000/paymentstatus",
			"pp_SecureHash" 		=> "",
			"ppmpf_1" 				=> "1",
			"ppmpf_2" 				=> "2",
			"ppmpf_3" 				=> "3",
			"ppmpf_4" 				=> "4",
			"ppmpf_5" 				=> "5",
		);
		
		$pp_SecureHash = $this->get_SecureHash($post_data);
		
		$post_data['pp_SecureHash'] = $pp_SecureHash;
		
		$values = array(
			'TxnRefNo'    => $post_data['pp_TxnRefNo'],
			'amount' 	  => 20,
			'description' => $post_data['pp_Description'],
			'status' 	  => 'pending'
		);
		// DB::table('order')->insert($values);
		
		
		Session::put('post_data',$post_data);
		// echo '<pre>';
		// print_r($post_data);
		// echo '</pre>';
		
		return view('do_checkout_v');
		
		
	}
	
	//NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN
	private function get_SecureHash($data_array)
	{
		ksort($data_array);
		
		$str = '';
		foreach($data_array as $key => $value){
			if(!empty($value)){
				$str = $str . '&' . $value;
			}
		}
		
		$str = "001hszft38".$str;
		
		$pp_SecureHash = hash_hmac('sha256', $str, "001hszft38");
		
		//echo '<pre>';
		//print_r($data_array);
		//echo '</pre>';
		
		return $pp_SecureHash;
	}
	
	
	
	//NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN
	public function paymentStatus(Request $request)
	{
		$response = $request->input();
		// echo '<pre>';
		// print_r($response);
		// echo '</pre>';
		
		if($response['pp_ResponseCode'] == '000')
		{
			$response['pp_ResponseMessage'] = 'Your Payment has been Successful';
			$values = array('status' => 'completed');
			// DB::table('order')
			// 	->where('TxnRefNo',$response['pp_TxnRefNo'])
			// 	->update(['status' => 'completed']);
		}
		
		return view('payment-status',['response'=>$response]);
	}
}
