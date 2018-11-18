<p align="center"><a href="https://piedweb.com">
<img src="https://raw.githubusercontent.com/PiedWeb/piedweb-devoluix-theme/master/src/img/logo_title.png" width="200" height="200" alt="theme devoluix bootstrap 4" />
</a></p>

# Static Bundle

Generate Static Website powered by Pied Web CMS

Initially dev to be use with [PiedWeb CMS](https://github.com/PiedWeb/CMS).


## Installation

Via [Packagist](https://packagist.org/packages/piedweb/static-bundle) :

```
# Get the Bundle
composer require piedweb/static-bundle

# Add route
static:
    resource: '@PiedWebStaticBundle/Resources/config/routes/static.yaml'
```

## Usage

1. Create a domain pointer on `%project_dir%/static` and add it to your `config/parameters.yaml` > `app.static_domain: mydomain.tld`

2. (Optionnal) Create domain or subdomain manager  like `secret-admin.my-domain.tld` and point on your public folder
   Protect your public folder with a .htpasswd to avoid duplicate content

4. Generate your static site : `secret-admin.my-domain.tld/~static` or via the command `php bin/console static:generate`

### Use it on a non-apache service (like github page)

Edit your routes and add a `.html` suffix to `piedweb_cms_page`.

Not test yet : Copy your data to the server with `rsync` and `--copy-links` for example.

## TODO

- test

## Contributors

* [Robin](https://www.robin-d.fr/) / [Pied Web](https://piedweb.com)
* ...

Check coding standard before to commit :
```
php-cs-fixer fix src --rules=@Symfony --verbose
php-cs-fixer fix src --rules='{"array_syntax": {"syntax": "short"}}' --verbose
```

## License

MIT (see the LICENSE file for details)
