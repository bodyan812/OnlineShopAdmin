<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
class CategoryCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return Category::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('категорию')
            ->setEntityLabelInPlural('Категории')
            ->setPageTitle('new', 'Создать категорию');
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name', 'Название'),
            AssociationField::new('parent', 'Родительская категория')
                ->setCrudController(CategoryCrudController::class)
                ->setFormTypeOption('choice_label', 'name')
                ->setFormTypeOption('query_builder', function (CategoryRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                }),
            TextField::new('imageFile', 'Изображение')
                ->setFormType(VichImageType::class)
                ->onlyOnForms(),
            ImageField::new('imageName', 'Превью')
                ->setBasePath('uploads/categories')
                ->onlyOnIndex()
        ];
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Category) {
            // Проверяем, есть ли дочерние категории
            if ($entityInstance->getChildren()->count() > 0) {
                $this->addFlash('danger', 'Невозможно удалить категорию. Отвяжите все дочерние категории перед удалением.');
                return;
            }

            // Если дочерних категорий нет, удаляем продукты
            foreach ($entityInstance->getProducts() as $product) {
                $entityInstance->removeProduct($product);
            }
            $entityManager->flush();
        }

        parent::deleteEntity($entityManager, $entityInstance);
    }
}
