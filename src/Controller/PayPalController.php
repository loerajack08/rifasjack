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
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        $amount = new Amount();
        $amount->setTotal("10.00"); // Monto en dólares
        $amount->setCurrency("USD");

        $transaction = new Transaction();
        $transaction->setAmount($amount);
        $transaction->setDescription("Pago por 10 USD"); // Descripción del pago

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($this->generateUrl('paypal_success', [], true)) // URL completa de éxito
            ->setCancelUrl($this->generateUrl('paypal_cancel', [], true)); // URL completa de cancelación

        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setTransactions([$transaction])
            ->setRedirectUrls($redirectUrls);

        try {
            $payment->create($this->payPalService->getApiContext());
            return $this->redirect($payment->getApprovalLink());
        } catch (\Exception $e) {
            return new Response("Error al crear el pago: " . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/paypal/success", name="paypal_success")
     */
    public function success(Request $request): Response
    {
        $paymentId = $request->query->get('paymentId');
        $payerId = $request->query->get('PayerID');

        try {
            $payment = Payment::get($paymentId, $this->payPalService->getApiContext());
            $execution = new PaymentExecution();
            $execution->setPayerId($payerId);

            $result = $payment->execute($execution, $this->payPalService->getApiContext());
            if ($result->getState() === "approved") {
                // Aquí puedes registrar el pago en la base de datos o realizar otras acciones necesarias
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
