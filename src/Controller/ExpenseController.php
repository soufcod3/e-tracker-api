<?php

namespace App\Controller;

use App\Entity\Budget;
use App\Entity\Expense;
use App\Form\ExpenseType;
use App\Repository\CategoryRepository;
use App\Repository\ExpenseRepository;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\IsNull;
use Symfony\Component\Validator\Constraints\NotNull;

#[Route('api/expenses')]
class ExpenseController extends AbstractController
{
    #[Route('/', name: 'app_expense_index', methods: ['GET'])]
    public function index(Request $request, ExpenseRepository $expenseRepository, LoggerInterface $logger): Response
    {
        $logger->error('here are the budgets bro');
        return $this->render('expense/index.html.twig', [
            'expenses' => $expenseRepository->findAll(),
        ]);
    }
    // Je dois supprimer methods[POST] pour ne plus avoir l'erreur GET ????!!!
    #[Route('/add/{id}', name: 'app_expense_new')]
    public function new(Request $request, ExpenseRepository $expenseRepository, Budget $budget, CategoryRepository $categoryRepo): Response
    {
        $data = json_decode($request->getContent(), true);

        if (null == $data) {
            return $this->json([
                'success' => false,
                'message' => 'Aucun paramètre POST trouvé'], 300);
        }

        // Building the date
        $date = new DateTimeImmutable();
        $date::createFromFormat('Y-m-d', $data['expense_date']);

        // return $this->json($data);
        $expense = new Expense();
        $expense->setAmount($data['expense_amount']);
        // dump($data);:
        $expense->setDate($date);
        $expense->setDescription($data['expense_description']);
        $expense->setBudget($budget);
        if ($data['expense_category']) {
            $expense->setCategory($categoryRepo->findOneById($data['expense_category']));
        }
        $expenseRepository->save($expense, true);

        return $this->json($expense);
    }

    #[Route('/{id}', name: 'app_expense_show', methods: ['GET'])]
    public function show(Expense $expense): Response
    {
        return $this->render('expense/show.html.twig', [
            'expense' => $expense,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_expense_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Expense $expense, ExpenseRepository $expenseRepository): Response
    {
        $form = $this->createForm(ExpenseType::class, $expense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $expenseRepository->save($expense, true);

            return $this->redirectToRoute('app_expense_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('expense/edit.html.twig', [
            'expense' => $expense,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_expense_delete', methods: ['POST'])]
    public function delete(Request $request, Expense $expense, ExpenseRepository $expenseRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$expense->getId(), $request->request->get('_token'))) {
            $expenseRepository->remove($expense, true);
        }

        return $this->redirectToRoute('app_expense_index', [], Response::HTTP_SEE_OTHER);
    }
}
