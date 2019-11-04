<?php

namespace PiedWeb\StaticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use PiedWeb\StaticBundle\Service\StaticService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

class StaticController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function generateStatic(StaticService $static)
    {
        $static->dump();

        return $this->render('@PiedWebStatic/static.admin.html.twig');
    }
}
