<?php

namespace App\Controller;

use App\Entity\Content;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ContentController extends AbstractController
{
    #[Route('/content', name: 'app_content')]
    public function index(): Response
    {
        return $this->render('content/index.html.twig', [
            'controller_name' => 'ContentController',
        ]);
    }
    #[Route('/post/readcontent', name: 'read_post', methods: ['POST'])]
    // Définition de la route /post/read avec la méthode HTTP POST
    public function read(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Fonction qui sera exécutée lors de l'appel de l'endpoint
        $data = json_decode($request->getContent(), true);
        // Décoder les données JSON de la requête
        $post = $entityManager->getRepository(Content::class)->find($data['id']);
        // Trouver le Post par son ID
        if (!$post) {
            return new JsonResponse(['status' => 'Order not found'], JsonResponse::HTTP_NOT_FOUND);
            // Si le Post n'existe pas, retourner une erreur 404
        }

        return new JsonResponse([
            'id' => $post->getId(),
            'product_id' => $post->getProductId(),
            'quantity' => $post->getQuantity(),
            'total_price' => $post->getTotalPrice(),
            'customer_email' => $post->getCustomerEmail()
        ], JsonResponse::HTTP_OK);
        // Retourner les détails du Post sous forme de réponse JSON
    }
    
    // Endpoint pour mettre à jour un Post par son ID
    #[Route('/post/updatecontent', name: 'update_post', methods: ['POST'])]
    // Définition de la route /post/update avec la méthode HTTP POST
    public function update(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Fonction qui sera exécutée lors de l'appel de l'endpoint
        $data = json_decode($request->getContent(), true);
        // Décoder les données JSON de la requête
        $post = $entityManager->getRepository(Content::class)->find($data['id']);
        // Trouver le Post par son ID
        if (!$post) {
            return new JsonResponse(['status' => 'Order not found'], JsonResponse::HTTP_NOT_FOUND);
            // Si le Post n'existe pas, retourner une erreur 404
        }
        $post->setTitle($data['title']);
        // Mettre à jour le titre du Post
        $post->setContent($data['content']);
        // Mettre à jour le contenu du Post
        $entityManager->flush();
        // Sauvegarder les changements dans la base de données
        return new JsonResponse(['status' => 'Order updated !'], JsonResponse::HTTP_OK);
        // Retourner une réponse JSON indiquant le succès de l'opération
    }

    // Endpoint pour supprimer un Post par son ID
    #[Route('/post/deletecontent', name: 'delete_post', methods: ['POST'])]
    // Définition de la route /post/delete avec la méthode HTTP POST
    public function delete(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Fonction qui sera exécutée lors de l'appel de l'endpoint
        $data = json_decode($request->getContent(), true);
        // Décoder les données JSON de la requête
        $post = $entityManager->getRepository(Content::class)->find($data['id']);
        // Trouver le Post par son ID
        if (!$post) {
            return new JsonResponse(['status' => 'Order not found'], JsonResponse::HTTP_NOT_FOUND);
            // Si le Post n'existe pas, retourner une erreur 404
        }
        $entityManager->remove($post);
        // Supprimer le Post de la base de données
        $entityManager->flush();
        // Sauvegarder les changements dans la base de données
        return new JsonResponse(['status' => 'Order deleted !'], JsonResponse::HTTP_OK);
        // Retourner une réponse JSON indiquant le succès de l'opération
    }

    // Endpoint pour supprimer un Post par son ID
    #[Route('/post/createcontent', name: 'create_post', methods: ['POST'])]
    // Définition de la route /post/delete avec la méthode HTTP POST
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Fonction qui sera exécutée lors de l'appel de l'endpoint
        $data = json_decode($request->getContent(), true);
        // Décoder les données JSON de la requête
        if (isset($data['product_id']) && isset($data['quantity']) && isset($data['total_price']) && isset($data['customer_email'])){
            $content = new Content();
            // Création d'un objet Content
            $content->setProductId($data['product_id']);
            $content->setQuantity($data['quantity']);
            $content->setTotalPrice($data['total_price']);
            $content->setCustomerEmail($data['customer_email']);
            // Assignation des différents attributs à l'objet

            $entityManager->persist($content);
            // Sauvegarder le nouvel objet dans l'entityManager

            $entityManager->flush();
            // Exécute la requête

            return new JsonResponse(['status' => 'Order created !'], JsonResponse::HTTP_CREATED);
            // Retourner une réponse JSON indiquant le succès de l'opération
        } else {
            return new JsonResponse(['status' => 'Order not created due to missing / wrong arguments.'], JsonResponse::HTTP_NOT_FOUND);
            // Retourner une réponse JSON indiquant l'echec de l'opération
        }
    }
}
