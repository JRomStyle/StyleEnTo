<?php

require_once __DIR__ . '/../config/config.php';

class StripeAPI {
    private $config;
    private $secretKey;
    private $publicKey;
    private $apiVersion;

    public function __construct() {
        $this->config = Config::getInstance();
        $stripeConfig = $this->config->get('apis')['stripe'];
        $this->secretKey = $stripeConfig['secret_key'];
        $this->publicKey = $stripeConfig['public_key'];
        $this->apiVersion = $stripeConfig['api_version'];
        
        // Inicializar Stripe SDK (en un entorno real, se instalaría via Composer)
        $this->initializeStripe();
    }

    private function initializeStripe() {
        // En un entorno real, se usaría require_once 'vendor/autoload.php';
        // Y luego: tripetripe::setApiKey($this->secretKey);
    }

    public function createPaymentIntent($amount, $currency = 'usd', $metadata = []) {
        // Simulación de creación de PaymentIntent
        // En un entorno real:
        // return tripeaymentIntent::create([
        //     'amount' => $amount * 100, // Convertir a centavos
        //     'currency' => $currency,
        //     'metadata' => $metadata
        // ]);
        
        return [
            'id' => 'pi_' . uniqid(),
            'client_secret' => 'pi_' . uniqid() . '_secret_' . uniqid(),
            'amount' => $amount * 100,
            'currency' => $currency,
            'status' => 'requires_payment_method'
        ];
    }

    public function createCustomer($email, $name = null) {
        // Simulación de creación de cliente
        return [
            'id' => 'cus_' . uniqid(),
            'email' => $email,
            'name' => $name
        ];
    }

    public function processPayment($paymentIntentId, $paymentMethodId) {
        // Simulación de procesamiento de pago
        return [
            'success' => true,
            'payment_intent' => $paymentIntentId,
            'payment_method' => $paymentMethodId,
            'status' => 'succeeded',
            'transaction_id' => 'txn_' . uniqid()
        ];
    }

    public function refundPayment($paymentIntentId, $amount = null) {
        // Simulación de reembolso
        return [
            'success' => true,
            'refund_id' => 're_' . uniqid(),
            'payment_intent' => $paymentIntentId,
            'amount' => $amount,
            'status' => 'succeeded'
        ];
    }

    public function getPublickey() {
        return $this->publicKey;
    }
}
