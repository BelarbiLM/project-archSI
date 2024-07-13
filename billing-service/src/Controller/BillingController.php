<?php

namespace App\Controller;

use App\Entity\Billing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BillingController extends AbstractController
{
    #[Route('/billing', name: 'app_billing')]
    public function index(): Response
    {
        return $this->render('billing/index.html.twig', [
            'controller_name' => 'BillingController',
        ]);
    }

    #[Route('/post/readbilling', name: 'read_post', methods: ['POST'])]
    // Définition de la route /post/read avec la méthode HTTP POST
    public function read(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Fonction qui sera exécutée lors de l'appel de l'endpoint
        $data = json_decode($request->getContent(), true);
        // Décoder les données JSON de la requête
        $post = $entityManager->getRepository(Billing::class)->find($data['id']);
        // Trouver le Post par son ID
        if (!$post) {
            return new JsonResponse(['status' => 'Bill not found'], JsonResponse::HTTP_NOT_FOUND);
            // Si le Post n'existe pas, retourner une erreur 404
        }

        return new JsonResponse([
            'id' => $post->getId(),
            'amount' => $post->getAmount(),
            'dueDate' => $post->getDueDate(),
            'customerEmail' => $post->getCustomerEmail()
        ], JsonResponse::HTTP_OK);
        // Retourner les détails du Post sous forme de réponse JSON
    }
    
    // Endpoint pour mettre à jour un Post par son ID
    #[Route('/post/updatebilling', name: 'update_post', methods: ['POST'])]
    // Définition de la route /post/update avec la méthode HTTP POST
    public function update(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Fonction qui sera exécutée lors de l'appel de l'endpoint
        $data = json_decode($request->getContent(), true);
        // Décoder les données JSON de la requête
        $post = $entityManager->getRepository(Billing::class)->find($data['id']);
        // Trouver le Post par son ID
        if (!$post) {
            return new JsonResponse(['status' => 'Bill not found'], JsonResponse::HTTP_NOT_FOUND);
            // Si le Post n'existe pas, retourner une erreur 404
        }
        $post->setTitle($data['title']);
        // Mettre à jour le titre du Post
        $post->setContent($data['content']);
        // Mettre à jour le contenu du Post
        $entityManager->flush();
        // Sauvegarder les changements dans la base de données
        return new JsonResponse(['status' => 'Bill updated!'], JsonResponse::HTTP_OK);
        // Retourner une réponse JSON indiquant le succès de l'opération
    }

    // Endpoint pour supprimer un Post par son ID
    #[Route('/post/deletebilling', name: 'delete_post', methods: ['POST'])]
    // Définition de la route /post/delete avec la méthode HTTP POST
    public function delete(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Fonction qui sera exécutée lors de l'appel de l'endpoint
        $data = json_decode($request->getContent(), true);
        // Décoder les données JSON de la requête
        $post = $entityManager->getRepository(Billing::class)->find($data['id']);
        // Trouver le Post par son ID
        if (!$post) {
            return new JsonResponse(['status' => 'Bill not found'], JsonResponse::HTTP_NOT_FOUND);
            // Si le Post n'existe pas, retourner une erreur 404
        }
        $entityManager->remove($post);
        // Supprimer le Post de la base de données
        $entityManager->flush();
        // Sauvegarder les changements dans la base de données
        return new JsonResponse(['status' => 'Bill deleted!'], JsonResponse::HTTP_OK);
        // Retourner une réponse JSON indiquant le succès de l'opération
    }

    // Endpoint pour supprimer un Post par son ID
    #[Route('/post/createbilling', name: 'create_post', methods: ['POST'])]
    // Définition de la route /post/delete avec la méthode HTTP POST
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Fonction qui sera exécutée lors de l'appel de l'endpoint
        $data = json_decode($request->getContent(), true);
        // Décoder les données JSON de la requête
        if (isset($data['amount']) && isset($data['due_date']) && isset($data['customer_email'])){
            $billing = new Billing();
            // Création d'un objet Billing
            $billing->setAmount($data['amount']);
            $billing->setDueDate($data['due_date']);
            $billing->setCustomerEmail($data['customer_email']);
            // Assignation des différents attributs à l'objet

            $entityManager->persist($billing);
            // Sauvegarder le nouvel objet dans l'entityManager

            $entityManager->flush();
            // Exécute la requête

            return new JsonResponse(['status' => 'Bill created!'], JsonResponse::HTTP_CREATED);
            // Retourner une réponse JSON indiquant le succès de l'opération
        } else {
            return new JsonResponse(['status' => 'Bill not created due to missing / wrong arguments.'], JsonResponse::HTTP_NOT_FOUND);
            // Retourner une réponse JSON indiquant l'echec de l'opération
        }
    }
}