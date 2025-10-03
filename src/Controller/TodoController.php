<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Form\TodoType;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/todo')]
final class TodoController extends AbstractController
{
    #[Route('/', name: 'app_todo_index', methods: ['GET'])]
    public function index(Request $request, TodoRepository $todoRepository): Response
    {
        $filter = $request->query->get('filter');
        $user = $this->getUser();

        switch ($filter) {
            case 'pending':
                $todos = $todoRepository->findBy([
                    'user' => $user,
                    'isCompleted' => false
                ]);
                break;
            case 'completed':
                $todos = $todoRepository->findBy([
                    'user' => $user,
                    'isCompleted' => true
                ]);
                break;
            case 'high':
                $todos = $todoRepository->createQueryBuilder('t')
                    ->where('t.user = :user')
                    ->andWhere('t.priority IN (:highPriorities)')
                    ->setParameter('user', $user)
                    ->setParameter('highPriorities', ['4', '5'])
                    ->getQuery()
                    ->getResult();
                break;
            default:
                $todos = $todoRepository->findBy(['user' => $user]);
                break;
        }

        return $this->render('todo/index.html.twig', [
            'todos' => $todos,
            'current_filter' => $filter
        ]);
    }

    #[Route('/new', name: 'app_todo_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TodoRepository $todoRepository): Response
    {
        $todo = new Todo();
        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $todo->setUser($this->getUser());
            $todoRepository->save($todo, true);

            $this->addFlash('success', 'Tâche créée avec succès!');

            return $this->redirectToRoute('app_todo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('todo/new.html.twig', [
            'todo' => $todo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_todo_show', methods: ['GET'])]
    public function show(Todo $todo): Response
    {
        if ($todo->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Accès non autorisé.');
        }

        return $this->render('todo/show.html.twig', [
            'todo' => $todo,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_todo_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Todo $todo, EntityManagerInterface $entityManager): Response
    {
        if ($todo->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Accès non autorisé.');
        }

        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Tâche modifiée avec succès!');

            return $this->redirectToRoute('app_todo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('todo/edit.html.twig', [
            'todo' => $todo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_todo_delete', methods: ['POST'])]
    public function delete(Request $request, Todo $todo, EntityManagerInterface $entityManager): Response
    {
        if ($todo->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Accès non autorisé.');
        }

        if ($this->isCsrfTokenValid('delete'.$todo->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($todo);
            $entityManager->flush();

            $this->addFlash('success', 'Tâche supprimée avec succès!');
        }

        return $this->redirectToRoute('app_todo_index', [], Response::HTTP_SEE_OTHER);
    }
}