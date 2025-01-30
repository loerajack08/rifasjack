<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\DatosRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Datos;
use Doctrine\ORM\EntityManagerInterface;

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

    #[Route('/pagarP', name: 'pagar')]
    public function listarBoleto(DatosRepository $datosRepository): Response
    {
        // Obtiene todos los boletos
        $boletos = $datosRepository->findAll();

        return $this->render('paypal_payment.html.twig', [
            'bolet' => $boletos,
        ]);
    }

    #[Route('/editar', name: 'editar_boleto')]
    public function editarBoleto(Request $request, EntityManagerInterface $entityManager): Response
    {
        $id = $request->query->get('id');
    
        if (!$id) {
            $this->addFlash('error', 'Debe seleccionar un boleto para editar.');
            return $this->redirectToRoute('ADMIN'); // Redirige a la página de selección
        }
    
        $boleto = $entityManager->getRepository(Datos::class)->find($id);
    
        if (!$boleto) {
            throw $this->createNotFoundException('El boleto no existe.');
        }
    
        if ($request->isMethod('POST')) {
            // Procesa el formulario POST para editar el boleto
            $nombreCompleto = $request->request->get('nombre_completo');
            $ciudad = $request->request->get('ciudad');
            $status = $request->request->get('status');
            $numeroCelular = $request->request->get('numero_celular');
            $fechaActualizacion = new \DateTime();
    
            $boleto->setNombreCompleto($nombreCompleto)
                   ->setCiudad($ciudad)
                   ->setStatus($status)
                   ->setFechaActualizacion($fechaActualizacion)
                   ->setNumeroCelular($numeroCelular);
    
            $entityManager->flush();
    
            $this->addFlash('success', 'Boleto actualizado correctamente.');
            return $this->redirectToRoute('BOLETOS');
        }
    
        return $this->render('editar_boleto.html.twig', [
            'boleto' => $boleto,
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
