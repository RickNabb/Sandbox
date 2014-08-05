<?php

use PayPal\Api\PaymentExecution;

use PayPal\Api\Amount;
use PayPal\Api\CreditCard;
use PayPal\Api\CreditCardToken;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;

function makePaymentUsingPayPal($total, $currency, $paymentDesc, $returnUrl, $cancelUrl){

	$payer = new Payer();
	$payer->setPaymentMethod("paypal");

	$amount = new Amount();
	$amount->setCurrency($currency);
	$amount->setTotal($total);

	$transaction = new Transaction();
	$transaction->setAmount($amount);
	$transaction->setDescription($paymentDesc);

	$redirectUrls = new RedirectUrls();
	$redirectUrls->setReturnUrl($returnUrl);
	$redirectUrls->setCancelUrl($cancelUrl);

	$payment = new Payment();
	$payment->setRedirectUrls($redirectUrls);
	$payment->setIntent("sale");
	$payment->setPayer($payer);
	$payment->setTransactions(array($transaction));

	$payment->create(getApiContext());
	return $payment;
}

function executePayment($paymentId, $payerId){

	$payment = Payment::get($paymentId, getApiContext());
	$paymentExecution = new PaymentExecution();
	$paymentExecution->setPayerId($payerId);
	$payment = $payment->execute($paymentExecution, getApiContext());

	return $payment;
}