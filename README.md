# Very short description of the package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/cyberlpkf/clicksign.svg?style=flat-square)](https://packagist.org/packages/cyberlpkf/clicksign)
[![Total Downloads](https://img.shields.io/packagist/dt/cyberlpkf/clicksign.svg?style=flat-square)](https://packagist.org/packages/cyberlpkf/clicksign)
![GitHub Actions](https://github.com/cyberlpkf/clicksign/actions/workflows/main.yml/badge.svg)

O objetivo deste pacote é facilitar a integração com os serviços do ClickSign.

This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what PSRs you support to avoid any confusion with users and contributors.

## Instalação

Para instalar este pacote via composer, use a seguinte linha de comando:

```bash
composer require cyberlpkf/clicksign
```

## Configuração

### Para configuração utilizando um único access token

Preencha as seguintes variáveis:

```bash
CLICKSIGN_USE_CONFIG_ON_DATABASE=false
CLICKSIGN_DEV_MODE=true
CLICKSIGN_DEV_URL=https://sandbox.clicksign.com
CLICKSIGN_PROD_URL=https://app.clicksign.com
CLICKSIGN_DOCUMENT_VERSION="/api/v1/documents"
CLICKSIGN_LIST_VERSION="api/v1/lists"
CLICKSIGN_NOTIFICATION_VERSION="/api/v1/notifications"
CLICKSIGN_SIGNERS_VERSION="api/v1/signers"
CLICKSIGN_ACCESS_TOKEN=
```

### Para configuração utilizando múltiplos access token
Publique a migration a ser executada:
```bash
php artisan vendor:publish --provider="cyberlpkf\clicksign\ClickSignServiceProvider" --tag="migrations"
```

Execute a migration:
```bash
php artisan migrate
```

Preencha a seguinte variável de configuração:
```bash
CLICKSIGN_USE_CONFIG_ON_DATABASE=true
```

Será necessário criar um registro na tabela api para armazenar as diferentes configurações.

#### Conteúdo dos campos
| Campo         | Valor       | Conteúdo                                                                                                                                                                                           |
|---------------|-------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| **api_id**    | *seu valor* | Contém a identificação da API. Você pode utilizar esta tabela para armazenar configuração de outras API's.                                                                                         |
| **filial_id** | *seu valor* | Contém a identificação de uma filial. Este campo diferencia as diversas configurações de uma mesma API. No caso da ClickSign, use-o para identificar as diversas configurações a serem utilizadas. |
|**credencial**|*seu valor* | Contém a configuração da API no formato JSON. Configuração de outras API's também deverão ser armazenadas neste formato.                                                                           |

Para que a configuração para a ClickSign seja considerada como válida, os seguintes atributos deverão estar presentes no campo credencial:

| Atributo | Valor padrão | Conteúdo |
|----------|--------------|----------|
|          |              |          |



## Usage

#### To create a document
``` php
$response = (new Clicksign())->createDocument($path, $clicksignPath = null, $mimetype = 'application/pdf', $deadline = null, $autoClose = true, $locale = 'pt-BR', $sequence_enabled = false);
```

#### To create a signer
``` php
$response = (new Clicksign())->createSigner(String $email, String $name, $phoneNumber = null, $documentation = false, $birthday = null, $has_documentation = false);
```

#### To add a signer to the document
``` php
$response =  (new Clicksign())->signerToDocument(String $document_key, $signer_key, $sign_as = 'approve', $message = null);
```
#### to view a document
``` php
$response =  (new Clicksign())->visualizaDocumento($DocumentKey);
```
#### To Cancel Document
``` php
$response = (new Clicksign())->cancelDocument($DocumentKey);
```
#### To Delete Document
``` php
$response = (new Clicksign())->deleteDocument($DocumentKey);
```
#### To Notify Signer By Email
``` php
$response = (new Clicksign())->notificationsByEmail($signer_key, $message = null);
```


### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email cyberlpkf@gmail.com instead of using the issue tracker.

## Credits

-   [Mateus Galasso](https://github.com/stonkeep) (original package)
-   [Luis Fernando Kieça](https://github.com/cyberlpkf)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
