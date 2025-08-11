<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public static function getEntityFqcn(): string
    {
        return User::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('пользователя')
            ->setEntityLabelInPlural('Пользователи');
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('username', 'Логин'),
            TextField::new('plainPassword', 'Пароль')
                ->setFormType(PasswordType::class)
                ->onlyOnForms()
                ->setRequired($pageName !== Crud::PAGE_EDIT),
            BooleanField::new('isAdmin', 'Администратор')
        ];
    }

    private function updateRoles(User $user): void
    {
        $roles = ['ROLE_USER'];
        if ($user->isAdmin()) {
            $roles[] = 'ROLE_ADMIN';
        }
        $user->setRoles($roles);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            $this->encodePassword($entityInstance);
            $this->updateRoles($entityInstance);
        }
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            $this->encodePassword($entityInstance);
            $this->updateRoles($entityInstance);
        }
        parent::updateEntity($entityManager, $entityInstance);
    }

    private function encodePassword(User $user): void
    {
        if ($user->getPlainPassword()) {
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    $user->getPlainPassword()
                )
            );
        }
    }
}
