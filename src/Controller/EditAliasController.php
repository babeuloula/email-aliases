<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Alias;
use App\Repository\AliasRepository;
use App\Service\GandiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditAliasController extends AbstractController
{
    #[Route('/aliases/edit/{domain}/{source}', name: 'app_edit_alias')]
    public function edit(
        string $domain,
        string $source,
        Request $request,
        GandiService $gandi,
        AliasRepository $repository
    ): Response {
        $alias = $repository->findOneBy(['domain' => $domain, 'source' => $source]);

        if (false === $alias instanceof Alias) {
            $this->addFlash('warning', 'Unable to find alias.');
            return $this->redirectToRoute('app_home');
        }

        if (Request::METHOD_GET === $request->getMethod()) {
            return $this->render(
                'aliases/edit.html.twig',
                [
                    'alias' => $alias,
                ]
            );
        }

        $alias
            ->setSource($request->request->get('source'))
            ->setDomain($request->request->get('domain'))
            ->setDestinations(
                preg_split('/\r\n|[\r\n]/', $request->request->get('destinations'))
            )
        ;

        $gandi->updateAddress($alias);
        $repository->save($alias, true);

        $this->addFlash('success', 'Alias updated with success.');

        return $this->redirectToRoute('app_home');
    }
}
