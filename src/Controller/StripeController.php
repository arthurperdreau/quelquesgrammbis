<?php

// src/Controller/StripeController.php
namespace App\Controller;

use App\Service\StripeService;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Checkout\Session;

class StripeController extends AbstractController
{

    #[Route('/don', name: 'donation_page')]
    public function index():Response
    {
        return $this->render('stripe/index.html.twig', [
            'stripe_public_key' => $_ENV['STRIPE_PUBLIC_KEY'],
        ]);
    }

    #[Route('/create-checkout-session', name: 'stripe_checkout', methods: ['POST'])]
    public function createSession(): JsonResponse
    {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => 'Don'],
                    'unit_amount' => 100, // 1 USD = 100 cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('don_success', [], 0),
            'cancel_url' => $this->generateUrl('don_cancel', [], 0),
        ]);

        return new JsonResponse(['id' => $session->id]);
    }

    #[Route('/don-success', name: 'don_success')]
    public function success(): Response
    {
        return new Response("<h2>Merci pour votre don !</h2>");
    }

    #[Route('/don-cancel', name: 'don_cancel')]
    public function cancel(): Response
    {
        return new Response("<h2>Le don a été annulé.</h2>");
    }
}
