<?php

namespace App\Controller;

use App\Repository\TodoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(TodoRepository $todoRepository): Response
    {
        $user = $this->getUser();
        $todos = $todoRepository->findBy(
            ['user' => $user],
            ['dueDate' => 'ASC']
        );

        $completedTodos = $todoRepository->findBy([
            'user' => $user,
            'isCompleted' => true
        ]);

        $pendingTodos = $todoRepository->findBy([
            'user' => $user,
            'isCompleted' => false
        ]);

        return $this->render('dashboard/index.html.twig', [
            'todos' => $todos,
            'completed_count' => count($completedTodos),
            'pending_count' => count($pendingTodos),
            'total_count' => count($todos),
        ]);
    }
}
