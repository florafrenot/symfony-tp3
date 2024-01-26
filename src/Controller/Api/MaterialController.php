<?php

namespace App\Controller\Api;

use App\Entity\Material;
use App\Repository\MaterialRepository;
use OpenApi\Attributes as OA;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api')]

class MaterialController extends AbstractController
{
    // Récupérer la liste du matériel
    #[Route('/materials', name: 'app_materials', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne toutes les marques.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Material::class, groups: ['brand:read']))
        )
    )]
    #[OA\Tag(name: 'Matériels')]
    public function index(MaterialRepository $materialRepository): JsonResponse
    {
        $materials =  $materialRepository->findAll();

        return $this->json([
            'materials' => $materials,
        ], context: [
            'groups' => ['material:read']
        ]);
    } 

    //Récupérer un seul matériel grâce à son ID
    #[OA\Tag(name: 'Matériels')]
    #[Route('/material/{id}', name: 'app_material_get', methods: ['GET'])]
    public function get(Material $type): JsonResponse
    {
        return $this->json($type, context: [
            'groups' => ['material:read']
        ]);
    }    

    //Ajouter un matériel
    #[Route('/materials', name: 'app_material_add', methods: ['POST'])]
    #[OA\Tag(name: 'Matériels')]
    public function add(
        Request $request,
        EntityManagerInterface $em,
    ): JsonResponse {
    try {
            $data = json_decode($request->getContent(), true);

            // je traite les données pour créer un matériel
            $material = new Material();
            $material->setName($data['name']);

            $em->persist($material);
            $em->flush();

            return $this->json($material, context: [
                'groups' => ['material:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    //mise à jour du matériel
    #[OA\Tag(name: 'Matériels')]
    #[Route('/material/{id}', name: 'app_material_update', methods: ['PUT','PATCH'])]
    // #[OA\Tag(name: 'Stylos')]
    public function update(
        Material $material,
        Request $request,
        EntityManagerInterface $em,
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            // On traite les données pour mettre à jour le matériel
            $material->setName($data['name']);

            $em->persist($material);
            $em->flush();

            return $this->json($material, context: [
                'groups' => ['material:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    } 

    // suppresion d'un matériel
    #[OA\Tag(name: 'Matériels')]
    #[Route('/material/{id}', name: 'app_material_delete', methods: ['DELETE'])]
    // #[OA\Tag(name: 'Marques')]
    public function delete(Material $material, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($material);
        $em->flush();
    
        return $this->json([
            'code' => 200,
            'message' => 'Matérial supprimé'
        ]);
    }
}
