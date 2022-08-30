<?php

namespace App\Controller\Back;

use App\Entity\Brand;
use App\Form\BrandType;
use App\Form\Filter\BrandFilterType;
use App\Repository\BrandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdaterInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/brand')]
class BrandController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) { }

    #[Route('/', name: 'app_admin_brand_index', methods: ['GET'])]
    public function index(
        BrandRepository $brandRepository,
        PaginatorInterface $paginator,
        FilterBuilderUpdaterInterface $builderUpdater,
        Request $request
    ): Response
    {
        $qb = $brandRepository->getQbAll();

        $filterForm = $this->createForm(BrandFilterType::class, null, [
            'method' => 'GET',
        ]);

        if ($request->query->has($filterForm->getName())) {
            $filterForm->submit($request->query->get($filterForm->getName()));
            $builderUpdater->addFilterConditions($filterForm, $qb);
        }

        $brands = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('back/brand/index.html.twig', [
            'brands' => $brands,
            'filters' => $filterForm->createView(),
        ]);
    }

    #[Route('/new', name: 'app_admin_brand_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $form = $this->createForm(BrandType::class, new Brand());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Brand $data */
            $data = $form->getData();
            $this->entityManager->persist($data);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_admin_brand_index');
        }

        return $this->render('back/brand/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}', name: 'app_admin_brand_show', methods: ['GET'])]
    public function show(Brand $brand): Response
    {
        return $this->render('back/brand/show.html.twig', [
            'brand' => $brand,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_admin_brand_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Brand $brand): Response
    {
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Brand $data */
            $data = $form->getData();
            $this->entityManager->persist($data);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_admin_game_index');
        }

        return $this->render('back/game/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}', name: 'app_admin_brand_delete', methods: ['POST'])]
    public function delete(Request $request, Brand $brand, BrandRepository $brandRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$brand->getId(), $request->request->get('_token'))) {
            $brandRepository->remove($brand, true);
        }

        return $this->redirectToRoute('app_brand_index', [], Response::HTTP_SEE_OTHER);
    }
}
