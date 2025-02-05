<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\DatosRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Datos;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;



class DefaulController extends AbstractController
{
    #[Route('/', name: 'home_redirect')]
    public function redirectToPrincipal(): RedirectResponse
    {
        // Redirige a la página principal
        return $this->redirectToRoute('app_defaul');
    }

    #[Route('/principal', name: 'app_defaul')]
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
    

    
    // 🔹 RUTA PROTEGIDA: Solo accesible si la sesión está activa
    #[Route('/insertard', name: 'ADMIN')]
   public function inserta(Request $request, DatosRepository $datosRepository): Response
  {
     // Verifica si el usuario está autenticado
     if (!$request->getSession()->get('admin_authenticated')) {
         // Redirige a la página de login si no está autenticado
         return $this->redirectToRoute('login_admin');
     }
 
     // Si está autenticado, obtiene los boletos y muestra la página
     $boletos = $datosRepository->findAll();
 
     return $this->render('Adminj03.html.twig', [
         'boletos' => $boletos,
     ]);
 }
 
  
  #[Route('/login_admin', name: 'login_admin')]
  public function login(Request $request, Connection $connection): Response
  {
      if ($request->isMethod('POST')) {
         $usuario = $request->request->get('usuario');
         $contraseñaIngresada = $request->request->get('contraseña');
 
         // Verificar que los datos se están recuperando correctamente
         $sql = "SELECT * FROM a_login WHERE usuario = ?";
         $usuarioDB = $connection->fetchAssociative($sql, [$usuario]);
 
         if (!$usuarioDB) {
             return new Response("❌ Acceso denegado. Usuario no encontrado.", 403);
         }
 
         // Debugging para ver la contraseña almacenada
         file_put_contents('debug_log.txt', "Ingresada: $contraseñaIngresada \nGuardada: {$usuarioDB['contraseña']}\n", FILE_APPEND);
 
         // Verificar la contraseña
         if (!password_verify($contraseñaIngresada, $usuarioDB['contraseña'])) {
             return new Response("❌ Acceso denegado. Contraseña incorrecta.", 403);
         }
 
         // Guardar en sesión
         $request->getSession()->set('admin_authenticated', true);
         return $this->redirectToRoute('ADMIN');
     }
 
     return $this->render('login_admin.html.twig');
 }
} 