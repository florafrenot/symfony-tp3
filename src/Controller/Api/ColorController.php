<?php

namespace App\Controller\Api;

use App\Entity\Color;
use OpenApi\Attributes as OA;
use App\Repository\ColorRepository;
use OpenApi\Attributes\JsonContent;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]

class ColorController extends AbstractController
{
    // Récupérer la liste de tous les types
    #[Route('/colors', name: 'app_colors', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne tous les stylos.',
        content: new JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Color::class, groups: ['pen:read']))
        )
    )]
    #[OA\Tag(name: 'Couleurs')]
    #[Security(name: 'Bearer')]
    public function index(ColorRepository $colorRepository): JsonResponse
    {
        $colors =  $colorRepository->findAll();

        return $this->json([
            'types' => $colors,
        ], context: [
            'groups' => ['color:read']
        ]);
    } 
    
    // Récupérer une couleur par son ID
    #[OA\Tag(name: 'Couleurs')]
    #[Route('/color/{id}', name: 'app_color_get', methods: ['GET'])]
    public function get(Color $color): JsonResponse
    {
        return $this->json($color, context: [
            'groups' => ['color:read']
        ]);
    }  
 
    //Ajouter une couleur
    #[Route('/colors', name: 'app_color_add', methods: ['POST'])]
    #[OA\Tag(name: 'Couleurs')]
    public function add(
        Request $request,
        EntityManagerInterface $em,
    ): JsonResponse {
    try {
            $data = json_decode($request->getContent(), true);

            // je traite les données pour créer une nouvelle couleur
            $color = new Color();
            $color->setName($data['name']);

            $em->persist($color);
            $em->flush();

            return $this->json($color, context: [
                'groups' => ['color:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    //mise à jour de la couleur
    #[Route('/color/{id}', name: 'app_color_update', methods: ['PUT','PATCH'])]
    #[OA\Tag(name: 'Couleurs')]
    public function update(
        Color $color,
        Request $request,
        EntityManagerInterface $em,
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $color->setName($data['name']);

            $em->persist($color);
            $em->flush();

            return $this->json($color, context: [
                'groups' => ['color:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }  

    // suppresion d'une couleur
    #[Route('/color/{id}', name: 'app_color_delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Couleurs')]
    public function delete(Color $color, EntityManagerInterface $em): JsonResponse
    {

        $em->remove($color);
        $em->flush();

        return $this->json([
            'code' => 200,
            'message' => 'Couleur supprimée'
        ]);
    }
}
