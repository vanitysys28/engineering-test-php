<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

class Order {
   public $id;
   public $drink;
   public $quantity;
   public $time;
  
   function __construct($id, $drink, $quantity, $time)  {
       $this->id = $id;
       $this->drink = $drink;
       $this->quantity = $quantity;
       $this->time = $time;
   }
}

Route::post('/order', function (Request $request) {
	$id = $request->input('id');
	$drink = $request->input('drink');
	$quantity = $request->input('quantity');
	$time = date("Y-m-d H:i:s");
	$delay = 5;
	
	$orders = [];
	$previousHistory = Cache::get('history');

	if(is_null($request->input('delay'))) {
	$modifiedDelay = $delay;
	} else {
	$modifiedDelay = $request->input('delay');
	}
	
	if($drink === "BEER" and $quantity <= 2 and is_null(Cache::get('orders'))){
		$orders[] = new Order($id,$drink,$quantity,$time); 
		$previousHistory[] = $orders;
		Cache::put('history',$previousHistory);
		Cache::put('orders',$orders,$seconds = $modifiedDelay * $quantity);
		return response()->json([
    		'msg' => "Order accepted",
		], 200);
	} else if ($drink === "BEER" and $quantity == 1 and is_null(Cache::get('orders') == 0)){
		$orders[] = new Order($id,$drink,$quantity,$time); 
		$previousHistory[] = $orders;
		Cache::put('history',$previousHistory);
		Cache::put('orders',$orders,$seconds = $modifiedDelay);
		return response()->json([
    		'msg' => "Order accepted",
		], 200);
	} else if ($drink === "DRINK" and $quantity == 1 and is_null(Cache::get('orders'))){
		$orders[] = new Order($id,$drink,$quantity,$time); 
		$previousHistory[] = $orders;
		Cache::put('history',$previousHistory);
		Cache::put('orders',$orders,$seconds = $modifiedDelay);
		return response()->json([
    		'msg' => "Order accepted",
		], 200);
	} else {
		return response()->json([
    		'msg' => "Order can't be processed",
		], 429);
	}
});

Route::get('/history', function (Request $request) {
		return Cache::get('history');
});
