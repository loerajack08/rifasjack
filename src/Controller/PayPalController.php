<?php

namespace App\Controller;

use App\Service\PayPalService;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\PaymentExecution;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PayPalController extends AbstractController
{
    private PayPalService $payPalService;

    public function __construct(PayPalService $payPalService)
    {
        $this->payPalService = $payPalService;
    }

    /**
     * @Route("/paypal/create-payment", name="paypal_create_payment")
     */
    public function createPayment(): Response
    {
        // Crear el pagador
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");
    
        // Crear el monto del pago
        $amount = new Amount();
        $amount->setTotal("10.00"); // Monto en dólares
        $amount->setCurrency("USD");
    
        // Crear la transacción
        $transaction = new Transaction();
        $transaction->setAmount($amount);
        $transaction->setDescription("Pago por 10 USD"); // Descripción del pago
    
        // Generar URLs absolutas correctamente
        $successUrl = $this->generateUrl('paypal_success', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $cancelUrl = $this->generateUrl('paypal_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL);
    
        dump($successUrl);
        dump($cancelUrl);
        die(); // Detener ejecución para verificar URLs
    
        // Crear las URLs de redirección
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($successUrl)
                     ->setCancelUrl($cancelUrl);
    
        // Crear el objeto de pago
        $payment = new Payment();
        $payment->setIntent("sale")
                ->setPayer($payer)
                ->setTransactions([$transaction]) // Asegúrate de pasar un array
                ->setRedirectUrls($redirectUrls);
    
        try {
            // Crear el pago en PayPal
            $payment->create($this->payPalService->getApiContext());
            return $this->redirect($payment->getApprovalLink()); // Redirigir al enlace de aprobación
        } catch (\Exception $e) {
            // Manejo de errores en caso de falla
            return new Response("Error al crear el pago: " . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * @Route("/paypal/execute-payment", name="paypal_execute_payment")
     */
    public function executePayment(Request $request): Response
    {
        // Obtener los parámetros paymentId y PayerID de la URL
        $paymentId = $request->query->get('paymentId');
        $payerId = $request->query->get('PayerID');

        if (!$paymentId || !$payerId) {
            return new Response("Faltan parámetros de pago.", Response::HTTP_BAD_REQUEST);
        }

        try {
            // Obtener el pago desde la API de PayPal
            $payment = Payment::get($paymentId, $this->payPalService->getApiContext());

            // Ejecutar el pago con el payerId
            $execution = new PaymentExecution();
            $execution->setPayerId($payerId);

            $result = $payment->execute($execution, $this->payPalService->getApiContext());

            // Verificar si el pago fue aprobado
            if ($result->getState() === "approved") {
                return new Response("Pago aprobado. ID: $paymentId", Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            // Manejar errores durante la ejecución del pago
            return new Response("Error ejecutando el pago: " . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response("El pago no fue aprobado.", Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/paypal/success", name="paypal_success")
     */
    public function success(Request $request): Response
    {
        // Obtener los parámetros paymentId y PayerID
        $paymentId = $request->query->get('paymentId');
        $payerId = $request->query->get('PayerID');

        try {
            // Obtener el pago desde la API de PayPal
            $payment = Payment::get($paymentId, $this->payPalService->getApiContext());
            $execution = new PaymentExecution();
            $execution->setPayerId($payerId);

            // Ejecutar el pago
            $result = $payment->execute($execution, $this->payPalService->getApiContext());

            // Verificar si el pago fue aprobado
            if ($result->getState() === "approved") {
                return new Response("Pago exitoso. ID del pago: $paymentId", Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            return new Response("Error al procesar el pago: " . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response("El pago no fue aprobado.", Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/paypal/cancel", name="paypal_cancel")
     */
    public function cancel(): Response
    {
        return new Response("El pago fue cancelado.", Response::HTTP_OK);
    }
}

