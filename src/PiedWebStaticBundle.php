<?php

namespace PiedWeb\StaticBundle;

use PiedWeb\StaticBundle\DependencyInjection\PiedWebStaticExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

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
