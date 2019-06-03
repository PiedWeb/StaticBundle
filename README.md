<p align="center"><a href="https://piedweb.com">
<img src="https://raw.githubusercontent.com/PiedWeb/piedweb-devoluix-theme/master/src/img/logo_title.png" width="200" height="200" alt="theme devoluix bootstrap 4" />
</a></p>

# Static Bundle

[![Latest Version](https://img.shields.io/github/tag/PiedWeb/StaticBundle.svg?style=flat&label=release)](https://github.com/PiedWeb/StaticBundle/tags)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE)
[![Build Status](https://img.shields.io/travis/PiedWeb/StaticBundle/master.svg?style=flat)](https://travis-ci.org/PiedWeb/StaticBundle)
[![Quality Score](https://img.shields.io/scrutinizer/g/PiedWeb/StaticBundle.svg?style=flat)](https://scrutinizer-ci.com/g/PiedWeb/StaticBundle)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/PiedWeb/StaticBundle.svg?style=flat)](https://scrutinizer-ci.com/g/PiedWeb/StaticBundle/code-structure)
[![Total Downloads](https://img.shields.io/packagist/dt/piedweb/static-bundle.svg?style=flat)](https://packagist.org/packages/piedweb/static-bundle)

Generate Static Website powered by Pied Web CMS

Initially dev to be used with [PiedWeb CMS](https://github.com/PiedWeb/CMS).


## Installation

Via [Packagist](https://packagist.org/packages/piedweb/static-bundle) :

```
composer require piedweb/static-bundle
```

## Usage

On simple command line permit to generate your site in the `static` folder :
```
php bin/console static:generate
```

Or you can do it via an HTTP request
```
# Add route in your config/routes.yml
static:
    resource: '@PiedWebStaticBundle/Resources/config/routes/static.yaml'

And request /~static
```

### Exemple : Deploying on a shared APACHE hosting with a functionnal online backend :

1. On a local installation of PiedWebCMS (and generate assets), install this bundle
2. Create a domain pointer on `%project_dir%/static` and add it to your `config/services.yaml` > `app.static_domain: mydomain.tld`
3. Create a second domain pointer for your admin on `/public`
4. Change in `.env`  `APP_ENV = prod` and send online via rsync
   `rsync -avz --delete-after --exclude="node_modules" -e 'ssh -p 5022' ./ user@my-domain.com:/`

You can also
- Add in the new `.htaccess` and create a `.htpasswd`
  ```
  AuthType Basic
  AuthName "/"
  AuthUserFile .htpasswd
  Require valid-user
  ```
- Optimize
   ```
   composer install --no-dev --optimize-autoloader
   composer dump-autoload --optimize --no-dev --classmap-authoritative
   ```
- Block Search engine bots access to your dynamic site (avoid duplicate content)
  In your `public/.htaccess` :
   ```
   RewriteCond %{HTTP_USER_AGENT} ^.*(googlebot|bingbot|baiduspider) [NC]
   RewriteRule . - [F,L]
   ```

Resources :
- https://medium.com/@runawaycoin/deploying-symfony-4-application-to-shared-hosting-with-just-ftp-access-e65d2c5e0e3d
- https://symfony.com/doc/current/deployment.html

### Use it on a non-apache service (like github page)

Edit your routes and add a `.html` suffix to `piedweb_cms_page`.

## TODO

- test

## Contributing

Please see [contributing](https://dev.piedweb.com/contributing)

## Credits

- [PiedWeb](https://piedweb.com)
- [All Contributors](https://github.com/PiedWeb/:package_skake/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

[![Latest Version](https://img.shields.io/github/tag/PiedWeb/StaticBundle.svg?style=flat&label=release)](https://github.com/PiedWeb/StaticBundle/tags)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE)
[![Build Status](https://img.shields.io/travis/PiedWeb/StaticBundle/master.svg?style=flat)](https://travis-ci.org/PiedWeb/StaticBundle)
[![Quality Score](https://img.shields.io/scrutinizer/g/PiedWeb/StaticBundle.svg?style=flat)](https://scrutinizer-ci.com/g/PiedWeb/StaticBundle)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/PiedWeb/StaticBundle.svg?style=flat)](https://scrutinizer-ci.com/g/PiedWeb/StaticBundle/code-structure)
[![Total Downloads](https://img.shields.io/packagist/dt/piedweb/static-bundle.svg?style=flat)](https://packagist.org/packages/piedweb/static-bundle)
