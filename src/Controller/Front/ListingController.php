<?php

namespace App\Controller\Front;

use App\Entity\Listing;
use App\Form\ListingType;
use App\Repository\ListingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/listing')]
class ListingController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) { }

    #[Route('/', name: 'app_listing_index', methods: ['GET'])]
    public function index(
        ListingRepository $listingRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response
    {
        $qb = $listingRepository->getQbAll();

        $listings = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('front/listing/index.html.twig', [
            'listings' => $listings,
        ]);
    }

    #[Route('/new', name: 'app_listing_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $form = $this->createForm(ListingType::class, new Listing());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Listing $data */
            $data = $form->getData();
            $this->entityManager->persist($data);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_admin_brand_index');
        }

        return $this->render('back/brand/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}', name: 'app_listing_show', methods: ['GET'])]
    public function show(Listing $listing): Response
    {
        return $this->render('front/listing/show.html.twig', [
            'listing' => $listing,
        ]);
    }
}
