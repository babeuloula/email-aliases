<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Gandi\Address;
use App\Entity\Alias;
use App\Repository\AliasRepository;
use App\Service\GandiService;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(GandiService $gandi, AliasRepository $repository): Response
    {
        $aliases = $repository->findBy(
            criteria: [],
            orderBy: ['source' => Criteria::ASC],
        );

        if (0 === \count($aliases)) {
            $domain = 'reynaud.io';

            $aliases = array_map(
                static function (Address $address) use ($repository, $domain): Alias {
                    return $repository->save(
                        Alias::fromAddress($address, $domain),
                        true,
                    );
                },
                $gandi->getAddresses($domain)
            );
        }

        return $this->render(
            'home.html.twig',
            [
                'aliases' => $aliases,
            ]
        );
    }
}
