<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PiedWeb\StaticBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment as Twig;
use PiedWeb\CMSBundle\Entity\PageInterface as Page;
use PiedWeb\CMSBundle\Service\PageCanonicalService as PageCanonical;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Router;

class StaticService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var \Twig_environement
     */
    private $twig;

    /**
     * @var string
     */
    private $webDir;

    /**
     * @var string
     */
    private $staticDir;

    /**
     * @var RequestStack
     */
    private $requesStack;

    /**
     * @var Router
     */
    private $pageCanonical;

    private $parser;

    protected $addToHtaccess = '';

    public function __construct(
        EntityManagerInterface $em,
        Twig $twig,
        ParameterBagInterface $params,
        RequestStack $requesStack,
        PageCanonical $pageCanonical,
        string $webDir
    ) {
        $this->em = $em;
        $this->filesystem = new Filesystem();
        $this->twig = $twig;
        $this->params = $params;
        $this->requesStack = $requesStack;
        $this->webDir = $webDir;
        $this->pageCanonical = $pageCanonical;
        $this->staticDir = $this->webDir.'/../static';
        $this->parser = \WyriHaximus\HtmlCompress\Factory::construct();
    }

    /**
     * @throws \RuntimeException
     * @throws \LogicException
     */
    public function dump()
    {
        if (!method_exists($this->filesystem, 'dumpFile')) {
            throw new \RuntimeException('Method dumpFile() is not available on your Filesystem component version, you should upgrade it.');
        }

        $this->rmdir($this->staticDir);
        $this->pageToStatic();
        $this->assetsToStatic();
        $this->htaccessToStatic();
    }

    // '.($this->params->has('app.static_dot_html') ? '
    protected function htaccessToStatic()
    {
        if (!$this->params->has('app.static_domain')) {
            throw new \Exception('You need to configure (in your config/parameters.yaml) app.static_domain to generate the .htaccess');
        }

        $htaccess =
'RewriteEngine on
RewriteBase /

Options -Indexes

AddDefaultCharset UTF-8

#---
# Page Redirection
#---
'.$this->addToHtaccess.'

#---
# Fix linking behavior
#---

# First we redirect if requested uri end with a slash
RewriteCond %{REQUEST_URI} (.*)/
RewriteCond %{REQUEST_FILENAME}.html -f
RewriteRule ^(.+)/$ /$1 [NC,L,R=301]

RewriteCond %{REQUEST_FILENAME} ([a-zA-Z0-9][a-zA-Z0-9\-/]*$)
RewriteCond %{REQUEST_FILENAME}.html -f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^\.]+)$ $1.html [NC,L,END]

RewriteCond %{REQUEST_URI} ^/?(.*).(html)$
RewriteRule ^/?(.*).(html)$ /$1 [R=301,NC,L]

#---
# Redirect www subfolder (or other) to domain.tld
#---
RewriteCond %{HTTP_HOST} ^(.*).'.$this->params->get('app.static_domain').'
RewriteRule ^(.*)$ https://'.$this->params->get('app.static_domain').'/$1 [L,R=301]


#---
# Redirect http to https
#---
RewriteCond %{SERVER_PORT} 80
RewriteRule ^(.*) https://'.$this->params->get('app.static_domain').'/$1  [QSA,L,R=301]

#---
# Redirect index.html to /
#---
RewriteRule ^index\.html$ https://'.$this->params->get('app.static_domain').'/ [QSA,L,R=301]

#---
# Errors
#---
ErrorDocument 403 /_error.html
ErrorDocument 404 /_error.html
ErrorDocument 500 /_error.html

#---
# Cache
#---
<IfModule mod_headers.c>
Header set Connection keep-alive
# 4 HOURS
Header set Cache-Control "max-age=14400, must-revalidate"
    # 480 weeks - 290304000
    <filesMatch "\.(ico|pdf|flv|jpg|JPG|jpeg|png|gif|js|swf|css|eot|svg|ttf|woff|woff2)$">
        Header set Cache-Control "max-age=290304000, public"
    </filesMatch>
    <FilesMatch "\.(gif|jpg|png|ico|css|js|pdf|txt)$">
        Header append Cache-Control "public"
    </FilesMatch>
</IfModule>
# ---
# GZIP
# ---
<IfModule mod_deflate.c>
  # Compress HTML, CSS, JavaScript, Text, XML and fonts
  AddOutputFilterByType DEFLATE application/javascript
  AddOutputFilterByType DEFLATE application/rss+xml
  AddOutputFilterByType DEFLATE application/vnd.ms-fontobject
  AddOutputFilterByType DEFLATE application/x-font
  AddOutputFilterByType DEFLATE application/x-font-opentype
  AddOutputFilterByType DEFLATE application/x-font-otf
  AddOutputFilterByType DEFLATE application/x-font-truetype
  AddOutputFilterByType DEFLATE application/x-font-ttf
  AddOutputFilterByType DEFLATE application/x-javascript
  AddOutputFilterByType DEFLATE application/xhtml+xml
  AddOutputFilterByType DEFLATE application/xml
  AddOutputFilterByType DEFLATE font/opentype
  AddOutputFilterByType DEFLATE font/otf
  AddOutputFilterByType DEFLATE font/ttf
  AddOutputFilterByType DEFLATE image/svg+xml
  AddOutputFilterByType DEFLATE image/x-icon
  AddOutputFilterByType DEFLATE text/css
  AddOutputFilterByType DEFLATE text/html
  AddOutputFilterByType DEFLATE text/javascript
  AddOutputFilterByType DEFLATE text/plain
  AddOutputFilterByType DEFLATE text/xml
</IfModule>';
        $this->filesystem->dumpFile($this->staticDir.'/.htaccess', $htaccess);
    }

    protected static function rmdir($dir)
    {
        if (is_link($dir)) {
            unlink($dir);
        }

        if (!is_dir($dir)) {
            return false;
        }

        $dir_handle = opendir($dir);
        if (!$dir_handle) {
            return false;
        }

        while ($file = readdir($dir_handle)) {
            if ('.' != $file && '..' != $file) {
                if (!is_dir($dir.'/'.$file)) {
                    unlink($dir.'/'.$file);
                } else {
                    self::rmdir($dir.'/'.$file);
                }
            }
        }
        closedir($dir_handle);
        rmdir($dir);

        return true;
    }

    protected function symlink(string $target, string $link): bool
    {
        // Check for symlinks
        if (is_link($target)) {
            return symlink(readlink($target), $link);
        }

        return symlink(
            $target,
            $link
        );
    }

    /**
     * We create symlink for all assets.
     */
    protected function assetsToStatic(): self
    {
        $dir = dir($this->webDir);
        while (false !== $entry = $dir->read()) {
            if ('.' == $entry || '..' == $entry) {
                continue;
            }
            if ('index.php' != $entry) {
                $this->symlink(
                    str_replace($this->params->get('kernel.project_dir').'/', '../', $this->webDir.'/'.$entry),
                    $this->staticDir.'/'.$entry
                );
            }
        }
        $dir->close();

        return $this;
    }

    protected function pageToStatic(): self
    {
        $pages = $this->getPages();

        $this->params->get('app.locales');

        $locales = explode('|', $this->params->get('app.locales'));

        foreach ($locales as $locale) {
            $this->filesystem->mkdir($this->staticDir.'/'.$locale);
            //$this->generateErrorPage($locale);

            foreach ($pages as $page) {
                // set current locale to avoid twig error
                $request = new Request();
                $request->setLocale($locale);
                $this->requesStack->push($request);

                $route = $this->pageCanonical->generatePathForPage('' == $page->getRealSlug() ? 'index' : $page->getRealSlug(), $locale);
                $filepath = $this->staticDir.$route.'.html';

                // check if it's a redirection
                if (false !== $page->getRedirection()) {
                    $this->addToHtaccess .= 'Redirect '.$page->getRedirectionCode().' '.$route.' '.$page->getRedirection().chr(10);
                    continue;
                }

                $dump = $this->render($page);

                if ('' == $page->getRealSlug() && $this->params->get('app.locale') == $locale) {
                    $this->filesystem->dumpFile($this->staticDir.'/index.html', $dump);
                } else {
                    $this->filesystem->dumpFile($filepath, $dump);
                }
            }
        }

        // todo i18n error
        $this->generateErrorPage();

        return $this;
    }

    protected function generateErrorPage($locale = null)
    {
        if (null !== $locale) {
            $request = new Request();
            $request->setLocale($locale);
            $this->requesStack->push($request);
        }

        $dump = $this->parser->compress($this->twig->render('@Twig/Exception/error.html.twig'));
        $this->filesystem->dumpFile($this->staticDir.(null !== $locale ? '/'.$locale : '').'/_error.html', $dump);
    }

    protected function getPages()
    {
        $qb = $this->em->getRepository($this->params->get('app.entity_page'))->getQueryToFindPublished('p');

        return $qb->getQuery()->getResult();
    }

    protected function render(Page $page)
    {
        $template = method_exists($this->params->get('app.entity_page'), 'getTemplate') && null !== $page->getTemplate() ? $page->getTemplate() : $this->params->get('app.default_page_template');

        return $this->parser->compress($this->twig->render($template, ['page' => $page]));
    }
}
