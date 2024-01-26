<?php

namespace App\Controller\Admin;

use App\Entity\Material;
use App\Form\Material1Type;
use App\Repository\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/material')]
#[IsGranted('ROLE_ADMIN')]

class MaterialController extends AbstractController
{
    #[Route('/', name: 'app_admin_material_index', methods: ['GET'])]
    public function index(MaterialRepository $materialRepository): Response
    {
        return $this->render('admin/material/index.html.twig', [
            'materials' => $materialRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_material_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $material = new Material();
        $form = $this->createForm(Material1Type::class, $material);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($material);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_material_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/material/new.html.twig', [
            'material' => $material,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_material_show', methods: ['GET'])]
    public function show(Material $material): Response
    {
        return $this->render('admin/material/show.html.twig', [
            'material' => $material,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_material_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Material $material, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Material1Type::class, $material);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_material_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/material/edit.html.twig', [
            'material' => $material,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_material_delete', methods: ['POST'])]
    public function delete(Request $request, Material $material, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$material->getId(), $request->request->get('_token'))) {
            $entityManager->remove($material);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_material_index', [], Response::HTTP_SEE_OTHER);
    }
}
