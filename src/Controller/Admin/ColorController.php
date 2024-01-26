<?php

namespace App\Controller\Admin;

use App\Entity\Color;
use App\Form\ColorType;
use App\Repository\ColorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/color')]
#[IsGranted('ROLE_ADMIN')]

class ColorController extends AbstractController
{
    #[Route('/', name: 'app_admin_color_index', methods: ['GET'])]
    public function index(ColorRepository $colorRepository): Response
    {
        return $this->render('admin/color/index.html.twig', [
            'colors' => $colorRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_color_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $color = new Color();
        $form = $this->createForm(ColorType::class, $color);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($color);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_color_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/color/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_color_show', methods: ['GET'])]
    public function show(Color $color): Response
    {
        return $this->render('admin/color/show.html.twig', [
            'color' => $color,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_color_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Color $color, EntityManagerInterface $entityManager): Response
    {
        // dd($color);
        $form = $this->createForm(ColorType::class, $color);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_color_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/color/edit.html.twig', [
            'color' => $color,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_color_delete', methods: ['POST'])]
    public function delete(Request $request, Color $color, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$color->getId(), $request->request->get('_token'))) {
            $entityManager->remove($color);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_color_index', [], Response::HTTP_SEE_OTHER);
    }
}
