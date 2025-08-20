<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\User;
use App\Entity\Cart;
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
            ->setController(ProductCrudController::class)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Админ-панель магазина')
            ->setFaviconPath('favicon.ico');
    }

    public function configureMenuItems(): iterable
    {
        // Основные разделы магазина
        yield MenuItem::linkToCrud('Товары', 'fas fa-box', Product::class);
        yield MenuItem::linkToCrud('Категории', 'fas fa-list', Category::class);

        // Настройки
        yield MenuItem::subMenu('Настройки', 'fas fa-cog')->setSubItems([
            MenuItem::linkToCrud('Пользователи', 'fas fa-user', User::class),
            MenuItem::linkToUrl('API', 'fas fa-code', '/api')
        ]);
    }
}
