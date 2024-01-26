<?php

namespace App\Controller\Api;

use App\Entity\Type;
use OpenApi\Attributes as OA;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]

class TypeController extends AbstractController
{
    // Récupérer la liste de tous les types
    #[Route('/types', name: 'app_types', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne toutes les marques.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Type::class, groups: ['brand:read']))
        )
    )]
    #[OA\Tag(name: 'Types')]    
    public function index(TypeRepository $typeRepository): JsonResponse
    {
        $types =  $typeRepository->findAll();

        return $this->json([
            'types' => $types,
        ], context: [
            'groups' => ['types:read']
        ]);
    } 
    
    // Récupérer un type par son ID
    #[OA\Tag(name: 'Types')]    
    #[Route('/type/{id}', name: 'app_type_get', methods: ['GET'])]
    public function get(Type $type): JsonResponse
    {
        return $this->json($type, context: [
            'groups' => ['types:read']
        ]);
    }    

    //Ajouter un type
    #[Route('/types', name: 'app_types_add', methods: ['POST'])]
    #[OA\Tag(name: 'Types')]
    public function add(
        Request $request,
        EntityManagerInterface $em,
    ): JsonResponse {
    try {
            $data = json_decode($request->getContent(), true);

            // je traite les données pour créer une nouvelle marque
            $type = new Type();
            $type->setName($data['name']);

            $em->persist($type);
            $em->flush();

            return $this->json($type, context: [
                'groups' => ['types:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    //mise à jour du type
    #[OA\Tag(name: 'Types')]    
    #[Route('/type/{id}', name: 'app_type_update', methods: ['PUT','PATCH'])]
    // #[OA\Tag(name: 'Stylos')]
    public function update(
        Type $type,
        Request $request,
        EntityManagerInterface $em,
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            // On traite les données pour mettre à jour la marque
            $type->setName($data['name']);

            $em->persist($type);
            $em->flush();

            return $this->json($type, context: [
                'groups' => ['types:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    } 
    
    // suppresion d'un type
    #[OA\Tag(name: 'Types')]    
    #[Route('/type/{id}', name: 'app_type_delete', methods: ['DELETE'])]
    // #[OA\Tag(name: 'Marques')]
    public function delete(Type $type, EntityManagerInterface $em): JsonResponse
    {

        $em->remove($type);
        $em->flush();

        return $this->json([
            'code' => 200,
            'message' => 'Type supprimé'
        ]);
    }

}
