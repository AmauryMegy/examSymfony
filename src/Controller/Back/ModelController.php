<?php

namespace App\Controller\Back;

use App\Entity\Model;
use App\Form\Filter\ModelFilterType;
use App\Form\ModelType;
use App\Repository\ModelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdaterInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/model')]
class ModelController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) { }

    #[Route('/', name: 'app_admin_model_index', methods: ['GET'])]
    public function index(
        ModelRepository $modelRepository,
        PaginatorInterface $paginator,
        FilterBuilderUpdaterInterface $builderUpdater,
        Request $request
    ): Response
    {
        $qb = $modelRepository->getQbAll();

        $filterForm = $this->createForm(ModelFilterType::class, null, [
            'method' => 'GET',
        ]);

        if ($request->query->has($filterForm->getName())) {
            $filterForm->submit($request->query->get($filterForm->getName()));
            $builderUpdater->addFilterConditions($filterForm, $qb);
        }

        $models = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('back/model/index.html.twig', [
            'models' => $models,
            'filters' => $filterForm->createView(),
        ]);
    }

    #[Route('/new', name: 'app_admin_model_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $form = $this->createForm(ModelType::class, new Model());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Model $data */
            $data = $form->getData();
            $this->entityManager->persist($data);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_admin_model_index');
        }

        return $this->render('back/model/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}', name: 'app_admin_model_show', methods: ['GET'])]
    public function show(Model $model): Response
    {
        return $this->render('back/model/show.html.twig', [
            'model' => $model,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_admin_model_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Model $model): Response
    {
        $form = $this->createForm(ModelType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Model $data */
            $data = $form->getData();
            $this->entityManager->persist($data);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_admin_game_index');
        }

        return $this->render('back/game/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}', name: 'app_admin_model_delete', methods: ['POST'])]
    public function delete(Request $request, Model $model, ModelRepository $modelRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$model->getId(), $request->request->get('_token'))) {
            $modelRepository->remove($model, true);
        }

        return $this->redirectToRoute('app_model_index', [], Response::HTTP_SEE_OTHER);
    }
}
