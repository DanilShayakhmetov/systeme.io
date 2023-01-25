<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Tax;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="app_product")
     */
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $form = $this->getProductForm($doctrine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $tax = $doctrine->getRepository(Tax::class)->findOneBy([
                'prefix' => preg_replace('/\d+/', '', $data['tax_number'])
            ]);

            if ($tax) {
                $priceWithTax = $data['price'] * (1 + $tax->getTaxation());
            } else {
                $errMsg = 'Отсутсвуют данные по налоговому сбору для данного региона, проверьте корректность tax номера';
            }
        }

        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'product_form' => $form->createView(),
            'price' => $priceWithTax ?? null,
            'error' => $errMsg ?? null,
        ]);
    }

    private function getProductForm(ManagerRegistry $doctrine): FormInterface
    {
        $products = $doctrine->getRepository(Product::class)->findAll();

        if (!$products) {
            throw $this->createNotFoundException(
                'No product found'
            );
        }

        return $this->createFormBuilder()
            ->add('price', ChoiceType::class, [
                'choices' => array_reduce($products, function ($priceList, $product) {
                    $priceList[$product->getName()] = $product->getPrice();
                    return $priceList;
                }),
                'label' => 'Товар '
            ])
            ->add('tax_number', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Regex('/[A-Z]{2,3}[0-9]{9,11}/'),
                ],
                'label' => 'Tax номер ',
            ])
            ->add('send', SubmitType::class)
            ->getForm();
    }
}
