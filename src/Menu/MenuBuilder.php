<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Security;

class MenuBuilder
{
    private $factory;
    private $context;

    public function __construct(FactoryInterface $factory, Security $context)
    {
        $this->factory = $factory;
        $this->context = $context;
    }


    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');


        if ($this->context->isGranted('ROLE_ADMIN')) {
            $menu->addChild('Home', [
                'route' => 'app_home',
                'extras' => ['icon' => 'house']
            ]);
        }

        if ($this->context->isGranted('ROLE_USER') && !$this->context->isGranted('ROLE_ADMIN')) {
            $menu->addChild('Home', [
                'route' => 'app_client_home',
                'extras' => ['icon' => 'house']
            ]);
        }

        if ($this->context->isGranted('ROLE_USER') && !$this->context->isGranted('ROLE_ADMIN')) {
            $menu->addChild('Meus Chamados', [
                'route' => 'app_client_tickets',
                'extras' => ['icon' => 'headset']
            ]);
        }

        if ($this->context->isGranted('ROLE_ANALYST')) {
            $menu->addChild('Chamados Abertos', [
                'route' => 'app_tickets_opened',
                'extras' => ['icon' => 'door-open']
            ]);

            $menu->addChild('Chamados Fechados', [
                'route' => 'app_tickets_closed',
                'extras' => ['icon' => 'door-closed']
            ]);
        }

        if ($this->context->isGranted('ROLE_ADMIN')) {
            $menu->addChild('Aguardando Aprovação', [
                'route' => 'app_tickets_waiting',
                'extras' => ['icon' => 'hourglass']
            ]);

            $menu->addChild('Clientes', [
                'route' => 'customers',
                'extras' => ['icon' => 'users']
            ]);

            $menu->addChild('Técnicos', [
                'route' => 'app_techs',
                'extras' => ['icon' => 'users-gear']
            ]);
        }

        return $menu;
    }

    public function createTopNavBar(array $options): ItemInterface
    {
        $navbar = $this->factory->createItem('nav');

        $navbar->addChild('user', [
            'route' => 'app_home',
            'extras' => [
                'icon' => 'fa-circle-user',
                'label' => 'Ola, ' . $this->context->getUser()->getUsername(),
            ]
        ]);

        return $navbar;
    }
}
