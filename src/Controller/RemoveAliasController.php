<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Alias;
use App\Repository\AliasRepository;
use App\Service\GandiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RemoveAliasController extends AbstractController
{
    #[Route('/aliases/remove/{domain}/{source}', name: 'app_remove_alias')]
    public function remove(
        string $domain,
        string $source,
        GandiService $gandi,
        AliasRepository $repository
    ): Response {
        $alias = $repository->findOneBy(['domain' => $domain, 'source' => $source]);

        if (false === $alias instanceof Alias) {
            $this->addFlash('warning', 'Unable to find alias.');
        } else {
            $gandi->removeAddress($domain, $source);
            $repository->remove($alias, true);

            $this->addFlash('success', 'Alias removed with success.');
        }

        return $this->redirectToRoute('app_home');
    }
}
