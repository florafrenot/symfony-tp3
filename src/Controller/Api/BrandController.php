<?php

namespace App\Controller\Api;

use App\Entity\Brand;
use OpenApi\Attributes as OA;
use App\Repository\BrandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Nelmio\ApiDocBundle\Annotation\Model;
use Faker\Factory;
use OA\JsonContent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api')]
class BrandController extends AbstractController
{
    // Récupérer la liste de toutes les marques
    #[Route('/brands', name: 'app_brands', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne toutes les marques.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Brand::class, groups: ['brand:read']))
        )
    )]
    #[OA\Tag(name: 'Marques')]
    #[Security(name: 'Bearer')]
    public function index(BrandRepository $brandRepository): JsonResponse
    {
        $brand = $brandRepository->findAll();

        return $this->json([
            'brands' => $brand,
        ], context: [
            'groups' => ['brand:read', 'pen:read']
        ]);
    }

    // Récupérer une marque par son ID
    #[Route('/brand/{id}', name: 'app_brand_get', methods: ['GET'])]
    #[OA\Tag(name: 'Marques')]
    public function get(Brand $brand): JsonResponse
    {
        return $this->json($brand, context: [
            'groups' => ['pen:read','brand:read']
        ]);
    }

    //Ajouter une marque
    #[Route('/brands', name: 'app_brand_add', methods: ['POST'])]
    #[OA\Tag(name: 'Marques')]
    public function add(
        Request $request,
        EntityManagerInterface $em,
    ): JsonResponse {
    try {
            $data = json_decode($request->getContent(), true);

            // je traite les données pour créer une nouvelle marque
            $brand = new Brand();
            $brand->setName($data['name']);

            $em->persist($brand);
            $em->flush();

            return $this->json($brand, context: [
                'groups' => ['brand:read', 'pen:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    //mise à jour de la marque
    #[Route('/brand/{id}', name: 'app_brand_update', methods: ['PUT','PATCH'])]
    #[OA\Tag(name: 'Marques')]
    public function update(
        Brand $brand,
        Request $request,
        EntityManagerInterface $em,
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            // On traite les données pour mettre à jour la marque
            $brand->setName($data['name']);

            $em->persist($brand);
            $em->flush();

            return $this->json($brand, context: [
                'groups' => ['brand:read', 'pen:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/brand/{id}', name: 'app_brand_delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Marques')]
    public function delete(Brand $brand, EntityManagerInterface $em): JsonResponse
    {

        $em->remove($brand);
        $em->flush();

        return $this->json([
            'code' => 200,
            'message' => 'Marque suppriméé'
        ]);
    }


}
