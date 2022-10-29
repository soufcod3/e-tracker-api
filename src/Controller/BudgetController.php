<?php

namespace App\Controller;

use App\Entity\Budget;
use App\Form\BudgetType;
use App\Repository\BudgetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/budgets')]
class BudgetController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'budget_index', methods: ['GET'])]
    public function index(BudgetRepository $budgetRepository): Response
    {
        echo 'test';
        
        return $this->json($budgetRepository->findAll());
    }

    #[Route('/featured', name: 'app_budget_index', methods: ['GET'])]
    public function featured(BudgetRepository $budgetRepository): Response
    {
        // $featured = $budgetRepository->findBy(['featured' => true]);
        $query = $this->em->createQuery(
            'SELECT b FROM App\Entity\Budget b WHERE b.featured = true'
        );
        $featured = $query->getArrayResult();

        return new JsonResponse($featured);
    }

    #[Route('/new', name: 'app_budget_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BudgetRepository $budgetRepository): Response
    {
        $budget = new Budget();
        $form = $this->createForm(BudgetType::class, $budget);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $budgetRepository->save($budget, true);

            return $this->redirectToRoute('app_budget_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('budget/new.html.twig', [
            'budget' => $budget,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_budget_show', methods: ['GET'])]
    public function show(Budget $budget): Response
    {
        return $this->render('budget/show.html.twig', [
            'budget' => $budget,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_budget_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Budget $budget, BudgetRepository $budgetRepository): Response
    {
        $form = $this->createForm(BudgetType::class, $budget);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $budgetRepository->save($budget, true);

            return $this->redirectToRoute('app_budget_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('budget/edit.html.twig', [
            'budget' => $budget,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_budget_delete', methods: ['POST'])]
    public function delete(Request $request, Budget $budget, BudgetRepository $budgetRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$budget->getId(), $request->request->get('_token'))) {
            $budgetRepository->remove($budget, true);
        }

        return $this->redirectToRoute('app_budget_index', [], Response::HTTP_SEE_OTHER);
    }
}
