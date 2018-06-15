<?php

namespace Digikala\Controller\Admin;

use Digikala\Entity\Product;
use Digikala\Entity\Variant;
use Digikala\Form\Admin\ProductType;
use Digikala\Form\Admin\VariantType;
use Digikala\Repository\ProductRepository;
use Digikala\Repository\VariantRepository;
use Digikala\Worker\IndexProductWorker;
use Doctrine\ORM\EntityManagerInterface;
use Enqueue\Client\ProducerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VariantController extends Controller
{
    public function index(
        $productId,
        ProductRepository $productRepository,
        VariantRepository $variantRepository
    ): Response {
        $product = $productRepository->find($productId);
        if (!$product instanceof Product) {
            throw new NotFoundHttpException();
        }

        return $this->render('admin/variant/index.html.twig', [
            'variants' => $variantRepository->findBy([
                'product' => $product
            ]),
            'product' => $product
        ]);
    }

    public function new(
        $productId,
        Request $request,
        ProductRepository $productRepository,
        EntityManagerInterface $em,
        ProducerInterface $producer
    ): Response {
        $product = $productRepository->find($productId);
        if (!$product instanceof Product) {
            throw new NotFoundHttpException();
        }

        $variant = new Variant();
        $variant->setProduct($product);
        $form = $this->createForm(VariantType::class, $variant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($variant);
            $em->flush();
            $producer->sendCommand(IndexProductWorker::class, ['product_id' => $product->getId()]);
            return $this->redirectToRoute('admin_variant_index',[
                'productId' => $productId
            ]);
        }

        return $this->render('admin/variant/new.html.twig', [
            'product' => $product,
            'variant' => $variant,
            'form' => $form->createView(),
        ]);
    }

    public function edit(
        Request $request,
        $productId,
        $id,
        ProductRepository $productRepository,
        VariantRepository $variantRepository,
        EntityManagerInterface $em,
        ProducerInterface $producer
    ): Response {
        $product = $productRepository->find($productId);
        if (!$product instanceof Product) {
            throw new NotFoundHttpException();
        }

        $variant = $variantRepository->findOneBy([
            'id' => $id,
            'product' => $product,
        ]);
        if (!$variant instanceof Variant) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(VariantType::class, $variant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $producer->sendCommand(IndexProductWorker::class, ['product_id' => $product->getId()]);
            return $this->redirectToRoute('admin_variant_index', [
                'productId' => $productId
            ]);
        }

        return $this->render('admin/variant/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'variant' => $variant,
        ]);
    }

    public function delete(
        Request $request,
        $productId,
        $id,
        ProductRepository $productRepository,
        VariantRepository $variantRepository,
        EntityManagerInterface $em
    ): Response {
        $product = $productRepository->find($productId);
        if (!$product instanceof Product) {
            throw new NotFoundHttpException();
        }

        $variant = $variantRepository->findOneBy([
            'id' => $id,
            'product' => $product,
        ]);
        if (!$variant instanceof Variant) {
            throw new NotFoundHttpException();
        }

        if ($this->isCsrfTokenValid('delete'.$product->getId().$variant->getId(), $request->request->get('_token'))) {
            $em->remove($variant);
            $em->flush();
        }

        return $this->redirectToRoute('admin_variant_index', [
            'productId' => $productId,
        ]);
    }
}
