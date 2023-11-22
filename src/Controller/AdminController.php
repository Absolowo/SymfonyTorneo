<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Participante;

class AdminController extends AbstractController
{
    #[Route('/admin/{texto?}', name: 'app_admin')]
    public function admin(ManagerRegistry $doctrine, $texto): Response{
        $repositorio = $doctrine->getRepository(Participante::class);
        $participantes = $repositorio->findByName($texto);
        return $this->render('lista_participantes_admin.html.twig', ["participantes" => $participantes]);
    }

    #[Route('/admin/delete/{id}', name: 'eliminar_participante')]
    public function delete(ManagerRegistry $doctrine, $id): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Participante::class);
        $participante = $repositorio->find($id);
        if ($participante){
            try
            {
                $entityManager->remove($participante);
                $entityManager->flush();
                return $this->render('eliminar.html.twig');
            } catch (\Exception $e) {
                return new Response("Error eliminando objetos");
            }
        }else
            return $this->render('lista_participantes_admin.html.twig', ['participante' => null]);
    }
}
