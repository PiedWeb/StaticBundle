<?php

namespace PiedWeb\StaticBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use PiedWeb\StaticBundle\DependencyInjection\PiedWebStaticExtension;

class PiedWebStaticBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new PiedWebStaticExtension();
        }

        return $this->extension;
    }
}
