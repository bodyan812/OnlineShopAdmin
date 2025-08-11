<?php

namespace App\Controller\Admin;

use App\Entity\Award;
use App\Entity\Rank;
use App\Entity\User;
use App\Entity\Veteran;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public function index(): Response
    {
        $url = $this->adminUrlGenerator
            ->setController(VeteranCrudController::class)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Мемориальная книга')
            ->setFaviconPath('favicon.ico');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::subMenu('Люди', 'fas fa-users')->setSubItems([
            MenuItem::linkToCrud('Российско-чеченский конфликт', 'fas fa-shield', Veteran::class)
                ->setQueryParameter('warType', 'chechen')
                ->setLinkRel('nofollow'),

            MenuItem::linkToCrud('Герои СВО', 'fas fa-star', Veteran::class)
                ->setQueryParameter('warType', 'svo')
                ->setLinkRel('nofollow'),

            MenuItem::linkToCrud('Герои ВОВ', 'fas fa-medal', Veteran::class)
                ->setQueryParameter('warType', 'ww2')
                ->setLinkRel('nofollow'),

            MenuItem::linkToCrud('Локальные конфликты', 'fas fa-globe', Veteran::class)
                ->setQueryParameter('warType', 'local')
                ->setLinkRel('nofollow'),

            MenuItem::linkToCrud('Афганская война', 'fas fa-flag', Veteran::class)
                ->setQueryParameter('warType', 'afghan')
                ->setLinkRel('nofollow'),
        ]);

        yield MenuItem::linkToCrud('Звания', 'fas fa-ranking-star', Rank::class);
        yield MenuItem::linkToCrud('Награды', 'fas fa-award', Award::class);

        yield MenuItem::subMenu('Настройки', 'fas fa-cog')->setSubItems([
            MenuItem::linkToCrud('Пользователи', 'fas fa-user', User::class),
            MenuItem::linkToUrl('API', 'fas fa-code', '/api')
        ]);
    }
}
