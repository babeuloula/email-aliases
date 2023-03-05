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

class AddAliasController extends AbstractController
{
    #[Route('/aliases/add', name: 'app_add_alias')]
    public function add(Request $request, GandiService $gandi, AliasRepository $repository): Response
    {
        if (Request::METHOD_GET === $request->getMethod()) {
            return $this->render('aliases/add.html.twig');
        }

        $alias = (new Alias())
            ->setSource($request->request->get('source'))
            ->setDomain($request->request->get('domain'))
            ->setDestinations(
                (array) preg_split('/\r\n|[\r\n]/', $request->request->get('destinations'))
            )
        ;

        $gandi->addAddress($alias);
        $repository->save($alias, true);

        $this->addFlash('success', 'Alias created with success.');

        return $this->redirectToRoute('app_home');
    }
}
