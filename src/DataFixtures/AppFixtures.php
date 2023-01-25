<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Tax;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $this->loadProducts($manager);
        $this->loadTaxes($manager);
    }

    private function loadProducts(ObjectManager $manager): void
    {
        $product = new Product();
        $product->setName('телефон')
            ->setPrice('1000');
        $manager->persist($product);

        $product = new Product();
        $product->setName('наушники')
            ->setPrice('9.90');
        $manager->persist($product);

        $product = new Product();
        $product->setName('чехол для телефона')
            ->setPrice('0.90');
        $manager->persist($product);

        $product = new Product();
        $product->setName('ноутбук')
            ->setPrice('2121.90');
        $manager->persist($product);

        $product = new Product();
        $product->setName('телевизор')
            ->setPrice('1234');
        $manager->persist($product);

        $product = new Product();
        $product->setName('холодильник')
            ->setPrice('999.99');
        $manager->persist($product);

        $manager->flush();

    }

    private function loadTaxes(ObjectManager $manager): void
    {
        $tax = new Tax();
        $tax->setCountry('Германия')
            ->setPrefix('DE')
            ->setTaxation('0.19');
        $manager->persist($tax);

        $tax = new Tax();
        $tax->setCountry('Италия')
            ->setPrefix('IT')
            ->setTaxation('0.22');
        $manager->persist($tax);

        $tax = new Tax();
        $tax->setCountry('Греция')
            ->setPrefix('GR')
            ->setTaxation('0.24');
        $manager->persist($tax);


        $manager->flush();
    }
}
