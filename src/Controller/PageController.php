<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Participante;
use App\Form\ProfileFormType;


class PageController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('page/index.html.twig', []);
    }

    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('page/about.html.twig', []);
    }

    #[Route('/profile/{texto?}', name: 'profile')]
    public function profile(ManagerRegistry $doctrine, $texto): Response{
        $repositorio = $doctrine->getRepository(Participante::class);
        $participante = $repositorio->findByName($texto);
        $this->denyAccessUnlessGranted("ROLE_USER");
        return $this->render('profile.html.twig', ["participante" => $participante]);
    }

    #[Route('/list/{texto?}', name: 'list_participantes')]
    public function list(ManagerRegistry $doctrine, $texto): Response{
        $repositorio = $doctrine->getRepository(Participante::class);
        $participantes = $repositorio->findByName($texto);
        return $this->render('lista_participantes.html.twig', ["participantes" => $participantes]);
    }

    #[Route('/edit/{id}', name: 'editar_participante')]
    public function edit(ManagerRegistry $doctrine, Request $request, $id){
        $repositorio = $doctrine->getRepository(Participante::class);
        $participante = $repositorio->find($id);
        $this->denyAccessUnlessGranted("ROLE_USER");

        if ($participante){
            $form = $this->createForm(ProfileFormType::class, $participante);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $contacto = $form->getData();    
                $entityManager = $doctrine->getManager();    
                $entityManager->persist($contacto);
                $entityManager->flush();
                return $this->redirectToRoute('profile', []);
            }
            return $this->render('editar.html.twig', array('profileForm' => $form->createView()));
        }
    }
}
