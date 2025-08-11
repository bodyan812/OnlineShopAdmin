<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichFileType;

class MediaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Media::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Медиа')
            ->setEntityLabelInPlural('Медиа файлы');
    }

    public function configureFields(string $pageName): iterable
    {
        $constraints = [];

        if ($pageName === Crud::PAGE_NEW) {
            $constraints = [
                new \Symfony\Component\Validator\Constraints\NotNull(),
            ];
        }

        return [
            TextField::new('title', 'Заголовок'),
            TextareaField::new('description', 'Описание'),
            TextField::new('file', 'Файл')
                ->setFormType(VichFileType::class)
                ->setFormTypeOptions([
                    'allow_delete' => false,
                    'download_uri' => false,
                    'asset_helper' => true,
                    'download_label' => false,
                ])
                ->setTemplatePath('admin/field/vich_file.html.twig')
                ->hideOnIndex(),
        ];
    }
}
