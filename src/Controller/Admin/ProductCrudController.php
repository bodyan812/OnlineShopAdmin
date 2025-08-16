<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\VichGalleryField;
use App\Entity\Product;
use App\Form\MediaType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name', 'Название'),
            TextareaField::new('description', 'Описание'),
            MoneyField::new('price', 'Цена')->setCurrency('RUB'),
            AssociationField::new('category', 'Категория')
                ->setCrudController(CategoryCrudController::class)
                ->setFormTypeOption('choice_label', 'name'),
        ];

        if ($pageName === Crud::PAGE_NEW || $pageName === Crud::PAGE_EDIT) {
            $fields[] = CollectionField::new('media', 'Изображения')
                ->setEntryType(MediaType::class)
                ->setFormTypeOption('by_reference', false)
                ->allowAdd()
                ->allowDelete();
        } else {
            $fields[] = VichGalleryField::new('media.mediaFile', 'Галерея');
        }

        return $fields;
    }
}
