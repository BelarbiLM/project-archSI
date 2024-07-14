<?php

namespace App\Controller;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Error\Notice;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    #[Route('/notification', name: 'app_notification')]
    public function index(): Response
    {
        return $this->render('notification/index.html.twig', [
            'controller_name' => 'NotificationController',
        ]);
    }
    #[Route('/post', name: 'create_post', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupérer les données JSON de la requête
        $data = json_decode($request->getContent(), true);
        // Créer une nouvelle instance de Post
        $notification = new Notification();
        $notification->setEmailRecipient($data['email_recipient']);
        $notification->setMessage($data['message']);
        $notification->setSujet($data['sujet']);
        // Persister l'entité Post
        $entityManager->persist($notification);
        $entityManager->flush();
        // Retourner une réponse JSON
        return new JsonResponse(
            ['status' => 'Post created!'],
            JsonResponse::HTTP_CREATED
        );
    }
}
