<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\DatosRepository;


class DefaulController extends AbstractController
{
    #[Route('/defaul', name: 'app_defaul')]
    public function index(): Response
    {
        return $this->render('Rifas.html.twig', [
            'controller_name' => 'DefaulController',
        ]);
    }

    #[Route('/pagos', name: 'pagos')]
    public function pagar(): Response
    {
        return $this->render('metodosdepago.html.twig');
    }

  
    #[Route('/BOLETOS', name: 'BOLETOS')]
    public function listarBoletos(DatosRepository $datosRepository): Response
    {
        // Obtiene todos los boletos
        $boletos = $datosRepository->findAll();

        return $this->render('Boletos.html.twig', [
            'boletos' => $boletos,
        ]);
    }

    #[Route('/insertard', name: 'ADMIN')]
    public function inserta(DatosRepository $datosRepository): Response
    {
        // Obtiene todos los boletos
        $boletos = $datosRepository->findAll();

        return $this->render('Adminj03.html.twig', [
            'boletos' => $boletos,
        ]);
    }

}
