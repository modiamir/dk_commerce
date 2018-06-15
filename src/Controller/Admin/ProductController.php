<?php

namespace Digikala\Controller\Admin;

use Digikala\Entity\Product;
use Digikala\Form\Admin\ProductType;
use Digikala\Repository\ProductRepository;
use Digikala\Worker\IndexProductWorker;
use Doctrine\ORM\EntityManagerInterface;
use Enqueue\Client\ProducerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends Controller
{
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $page = $request->query->get('page', 1);
        return $this->render('admin/product/index.html.twig', [
            'products' => $productRepository->findBy([], null, 10, ($page - 1) * 10),
            'count' => $productRepository->count([]),
        ]);
    }

    public function new(Request $request, EntityManagerInterface $em, ProducerInterface $producer): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();

            $producer->sendCommand(IndexProductWorker::class, ['product_id' => $product->getId()]);
            return $this->redirectToRoute('admin_product_index');
        }

        return $this->render('admin/product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    public function show($id, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);
        if (!$product instanceof Product) {
            throw new NotFoundHttpException();
        }
        return $this->render('admin/product/show.html.twig', ['product' => $product]);
    }

    public function edit(
        Request $request,
        $id,
        ProductRepository $productRepository,
        EntityManagerInterface $em,
        ProducerInterface $producer
    ): Response {
        $product = $productRepository->find($id);
        if (!$product instanceof Product) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $producer->sendCommand(IndexProductWorker::class, ['product_id' => $product->getId()]);
            return $this->redirectToRoute('admin_product_edit', ['id' => $product->getId()]);
        }

        return $this->render('admin/product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    public function delete(
        Request $request,
        $id,
        ProductRepository $productRepository,
        EntityManagerInterface $em
    ): Response {
        $product = $productRepository->find($id);
        if (!$product instanceof Product) {
            throw new NotFoundHttpException();
        }

        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $em->remove($product);
            $em->flush();
        }

        return $this->redirectToRoute('admin_product_index');
    }
}
