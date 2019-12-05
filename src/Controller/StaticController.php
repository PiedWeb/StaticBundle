<?php

namespace PiedWeb\StaticBundle\Controller;

use PiedWeb\StaticBundle\Service\StaticService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
