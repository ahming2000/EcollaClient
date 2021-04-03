<?php


namespace App\Session;


use App\Models\Customer;
use App\Models\Variation;

/**
 * Class Cart
 * @package App\Session
 */
class Cart
{
    public static string $DEFAULT_ORDER_MODE = 'pickup';
    public static string $DEFAULT_SESSION_NAME = 'ecollaCart';

    public array $cartItems;
    public string $orderMode;
    public bool $canCheckOut;
    public string $orderVerifyId;
    public Customer $customer;

    public function start(){

        if(session()->has(Cart::$DEFAULT_SESSION_NAME)){
            $this->pullSessionCart(session(Cart::$DEFAULT_SESSION_NAME));
        } else{
            $this->cartItems = array();
            $this->orderMode = Cart::$DEFAULT_ORDER_MODE;
            $this->canCheckOut = false;
            $this->orderVerifyId = "";
            $this->customer = new Customer();
            $this->pushSessionCart();
        }
    }

    /**
     * @param array $cartItems
     * @param string $orderMode
     * @param bool $canCheckOut
     * @param string $orderVerifyId
     * @param Customer $customer
     */
    public function importCart(array $cartItems, string $orderMode, bool $canCheckOut, string $orderVerifyId, Customer $customer)
    {
        $this->cartItems = $cartItems;
        $this->orderMode = $orderMode;
        $this->canCheckOut = $canCheckOut;
        $this->orderVerifyId = $orderVerifyId;
        $this->customer = $customer;
    }

    /**
     * @param Variation $variation
     * @param $quantity
     */
    public function addItem(Variation $variation, $quantity){

        $hasNotDuplicated = true;

        foreach ($this->cartItems as $cartItem){
            if($cartItem->variation->id == $variation->id){
                $cartItem->quantity += $quantity;
                $hasNotDuplicated = false;
            }
        }

        if($hasNotDuplicated){
            $this->cartItems[] = new CartItem($variation, $quantity);
        }

        $this->pushSessionCart();
    }

    /**
     * @param $barcode
     */
    public function deleteItem($barcode){
        for($i = 0; $i < sizeof($this->cartItems); $i++){
            if($this->cartItems[$i]->variation->barcode === $barcode){
                unset($this->cartItems[$i]);
                break;
            }
        }

        $newArray = array();
        foreach ($this->cartItems as $cartItem){
            $newArray[] = $cartItem;
        }
        $this->cartItems = $newArray;

        $this->pushSessionCart();
    }

    /**
     * @param $barcode
     * @param $quantity
     */
    public function editQuantity($barcode, $quantity){

        foreach ($this->cartItems as $cartItem){
            if($cartItem->variation->barcode === $barcode && $cartItem->quantity + $quantity >= 1){
                $cartItem->quantity += $quantity;
            }
        }

        $this->pushSessionCart();
    }

    public function resetCart(){
        unset($this->cartItems);
        $this->cartItems = array();
        $this->canCheckOut = false;
        $this->pushSessionCart();
    }

    public function changeOrderMode(string $mode){
        $this->orderMode = $mode;
        $this->pushSessionCart();
    }

    public function updateCustomerData($customerData){
        foreach ($customerData as $key => $value){
            $this->customer->setAttribute($key, $value);
        }

        $this->pushSessionCart();
    }

    public function updateOrderVerifyId($orderVerifyId){
        $this->orderVerifyId = $orderVerifyId['order_verify_id'];
        $this->pushSessionCart();
    }

    private function canCheckOut(){
        if($this->orderMode == 'delivery'){
            if(strtolower($this->customer->area) == 'kampar' && !empty($this->cartItems)){
                $this->canCheckOut = true;
            } else {
                $this->canCheckOut = false;
            }
        } else{
            if($this->orderVerifyId != "" && !empty($this->cartItems)){
                $this->canCheckOut = true;
            } else{
                $this->canCheckOut = false;
            }
        }

    }

    /**
     * @param Variation $variation
     * @return bool
     */
    public function isFound(Variation $variation): bool
    {
        foreach ($this->cartItems as $cartItem){
            if($cartItem->variation === $variation){
                return true;
            }
        }
        return false;
    }

    private function pullSessionCart(Cart $cart){
        $this->cartItems = $cart->cartItems;
        $this->orderMode = $cart->orderMode;
        $this->canCheckOut = $cart->canCheckOut;
        $this->orderVerifyId = $cart->orderVerifyId;
        $this->customer = $cart->customer;
    }

    private function pushSessionCart(){
        $this->canCheckOut();
        session([Cart::$DEFAULT_SESSION_NAME => $this]);
    }

    public function getCartCount(): int
    {
        return sizeof($this->cartItems) ?? 0;
    }

    public function getSubTotal(): float
    {
        $total = 0.0;
        foreach($this->cartItems as $cartItem){
            $total += $cartItem->getSubPrice();
        }
        return $total;
    }

    public function getShippingFee(): float
    {
        $fee = 0.0;

        if(strtolower($this->customer->area) == 'kampar'){
            $fee = 2.0;
        }

        $this->pushSessionCart();
        return $fee;
    }

}
