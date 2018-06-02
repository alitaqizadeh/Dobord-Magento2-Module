<?php

namespace Mediagostar\Dobord\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\UrlInterface;

class AfterPlaceOrder implements ObserverInterface
{
    /**
     * Order Model
     *
     * @var \Magento\Sales\Model\Order $order
     */


    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if(isset($_COOKIE['dbrdsid'])){
            $auth = [
	            'username' => 'test',
	            'password' => 'test'
            ];
            
            $order = $observer->getEvent()->getOrder();
            
            $transactionid = $order->getId();
            $products = array();
            foreach ($order->getAllItems() as $item) 
			{
                $productId = $item->getId();
                $productPrice = $item->getPrice();
                $productQty = $item->getQtyOrdered();
                $productTotal = $productPrice * $productQty;

				$products[$productId] = $productTotal;
            }

            $ch = curl_init();
        	curl_setopt($ch, CURLOPT_URL, "http://site.dobord.com/merchant");
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS,
	            http_build_query($auth));
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        $response_loging = curl_exec($ch);
	        curl_close($ch);
	        if ($response_loging)
	        {
	            $response_loging = json_decode($response_loging, true);
	        }
	        if (is_array($response_loging) && $response_loging['token'])
	        {
               $customer = [
                    'token' => $response_loging['token'],
                    'userid' => $_COOKIE['dbrdsid'],
                    'transactionid' => $transactionid,
                    "productlist" => $products,
                ];

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, "http://site.dobord.com/customer");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS,
                    http_build_query($customer));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response_cache_back_money = curl_exec($ch);
                curl_close($ch);
            }
            setcookie("dbrdsid", "", time() - 3600, "/");
        }
    }
}