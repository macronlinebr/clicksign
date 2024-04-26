# cyberlpkf/clicksign

[![Latest Version on Packagist](https://img.shields.io/packagist/v/cyberlpkf/clicksign.svg?style=flat-square)](https://packagist.org/packages/cyberlpkf/clicksign)
[![Total Downloads](https://img.shields.io/packagist/dt/cyberlpkf/clicksign.svg?style=flat-square)](https://packagist.org/packages/cyberlpkf/clicksign)


O objetivo deste pacote é facilitar a integração com os serviços do ClickSign. Suporta múltiplas credenciais para múltiplas empresas(filiais).

## Instalação

Para instalar este pacote via composer, use a seguinte linha de comando:

```bash
composer require cyberlpkf/clicksign
```

## Configuração

### Para configuração utilizando uma única empresa

Preencha as seguintes variáveis:

```php
CLICKSIGN_USE_CONFIG_ON_DATABASE=false
CLICKSIGN_USE_INTEGRATION=false
CLICKSIGN_DEV_MODE=true
CLICKSIGN_DEV_URL=https://sandbox.clicksign.com
CLICKSIGN_PROD_URL=https://app.clicksign.com
CLICKSIGN_DOCUMENT_VERSION="/api/v1/documents"
CLICKSIGN_LIST_VERSION="api/v1/lists"
CLICKSIGN_NOTIFICATION_VERSION="/api/v1/notifications"
CLICKSIGN_SIGNERS_VERSION="api/v1/signers"
CLICKSIGN_DEV_ACCESS_TOKEN="SEU TOKEN PARA A ÁREA DE DESENVOLVIMENTO"
CLICKSIGN_PROD_ACCESS_TOKEN="SEU TOKEN PARA A ÁREA DE PRODUÇÃO"
CLICKSIGN_DOCUMENT_SIGN_DURATION=0
```

### Para configuração utilizando múltiplas empresas
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
| Campo           | Valor       | Conteúdo                                                                                                                                                                                           |
|-----------------|-------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| **api_id**      | *seu valor* | Contém a identificação da API. Você pode utilizar esta tabela para armazenar configuração de outras API's.                                                                                         |
| **filial_id**   | *seu valor* | Contém a identificação de uma filial. Este campo diferencia as diversas configurações de uma mesma API. No caso da ClickSign, use-o para identificar as diversas configurações a serem utilizadas. |
| **credencial**  | *seu valor* | Contém a configuração da API no formato JSON. Configuração de outras API's também deverão ser armazenadas neste formato.                                                                           |

Para que a configuração da ClickSign seja considerada como válida, os seguintes atributos deverão estar presentes no campo credencial:

| Atributo               | Conteúdo                                                                                                                                                                                                                                       |
|------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| documentUrlVersion     | Deve armazenar a versão da API a ser utilizada para a gestão de documentos, como por exemplo */api/v1/documents*<br/>O não preenchimento deste atributo irá gerar a excessão *InvalidDocumentUrlConfigurationException*.                       |
| listUrlVersion         | Deve armazenar a versão da API a ser utilizada para a gestão de listas, como por exemplo */api/v1/lists*<br/>O não preenchimento deste atributo irá gerar a excessão *InvalidListUrlConfigurationException*.                                   |
| notificationUrlVersion | Deve armazenar a versão da API a ser utilizada para as notificações, como por exemplo */api/v1/notifications*<br/>O não preenchimento deste atributo irá gerar a excessão *InvalidNotificationUrlConfigurationException*.                      |
| signerUrlVersion       | Deve armazenar a versão da API a ser utilizada para a gestão das pessoas que irão assinar o documento, como por exemplo */api/v1/signers*<br/>O não preenchimento deste atributo irá gerara excessão *InvalidSignerUrlConfigurationException*. |
| developmentUrl         | Deve armazenar a URL para a área de desenvolvimento (sandbox), como por exemplo *https://sandbox.clicksign.com*<br/>O não preenchimento deste atributo irá gerar a excessão *InvalidDevelopmentUrlConfigurationException*.                     |
| productionUrl          | Deve armazenar a URL para a área de produção, como por exemplo *https://app.clicksign.com*<br/>O não preenchimento deste atributo irá gerar a excessão *InvalidProductionUrlConfigurationException*.                                           |
| devMode                | Deve armazenar os valores *true* ou *false* indicando se está (true) ou não está (false) sendo utilizada a área de desenvolvimento (sandbox).                                                                                                  |
| devAccessToken         | Deve conter o token de acesso para a área de desenvolvimento. <br/>O não preenchimento deste atributo irá gerar a excessão *NoAccessTokenException*.                                                                                           |
| prodAccessToken        | Deve conter o token de acesso para a área de produção. <br/>O não preenchimento deste atributo irá gerar a excessão *NoAccessTokenException*.                                                                                                  |
| useIntegration         | Deve conter true ou false indicando se a integração com a Clicksign será utilizada.                                                                                                                                                            |
| documentSignDuration   | Deve conter a duração padrão para assinatura em dias. Deve ser maior que zero.                                                                                                                                                                 |

> Caso o atributo *devMode* não esteja configurado, o ambiente de desenvolvimento será utilizado.

Utilizar a opção de armazenar as configurações da Clicksign no banco de dados, requer que dois métodos sejam chamados *antes* de realizar a chamada efetiva do método desejado. Será necessário configurar o *apiId* e a *filialId* e deverá ser feito da seguinte maneira:
``` php
$response = (new Clicksign())
                ->setApiId(1)
                ->setFilialId(3)
                ->createDocument($path, $clicksignPath = null, $mimetype = 'application/pdf', $deadline = null, $autoClose = true, $locale = 'pt-BR', $sequence_enabled = false);

```
> Não configurar *ApiId* irá gerar a exception *NoApiSetException*.

> Não configurar *FilialId* irá gerar a exception *NoFilialSetException*.

## Usage

#### Para criar um documento
``` php
$response = (new Clicksign())->createDocument($path, $clicksignPath = null, $mimetype = 'application/pdf', $deadline = null, $autoClose = true, $locale = 'pt-BR', $sequence_enabled = false);
```
> Não informar *path* irá gerar a excessão *InvalidPathException*.

#### Para criar um signatário
``` php
$response = (new Clicksign())->createSigner(String $email, String $name, $phoneNumber = null, $documentation = false, $birthday = null, $has_documentation = false);
```

> Não informar *name* irá gerar a excessão *InvalidNameException*.

> Não informar *email* irá gerar a excessão *InvalidEmailException*. 

#### Para adicionar um signatário a um documento
``` php
$response =  (new Clicksign())->signerToDocument(String $DocumentKey, $SignerKey, $sign_as = 'approve', $message = null);
```
> Não informar *DocumentKey* irá gerar a excessão *InvalidDocumentKeyException*.

> Não informar #SignerKey* irá gerar a excessão *InvalidSignerKeyException*.

#### Para visualizar um documento
``` php
$response =  (new Clicksign())->showDocument($DocumentKey);
```
> Não informar *DocumentKey* irá gerar a excessão *InvalidDocumentKeyException*.

#### Para cancelar um documento
``` php
$response = (new Clicksign())->cancelDocument($DocumentKey);
```
> Não informar *DocumentKey* irá gerar a excessão *InvalidDocumentKeyException*.

#### Para apagar um documento
``` php
$response = (new Clicksign())->deleteDocument($DocumentKey);
```
> Não informar *DocumentKey* irá gerar a excessão *InvalidDocumentKeyException*.

#### Para notificar um signatário por e-mail
``` php
$response = (new Clicksign())->notificationsByEmail($SignerKey, $message = null);
```

> Não informar *SignerKey* irá gerar a excessão *InvalidSignerKeyException*.

### Testes

```bash
composer test
```

### Changelog

Veja [o arquivo de alterações](CHANGELOG.md) mais finformações do que foi alterado recentemente.

## Contribuindo

Veja mais detalhes em [contribuindo](CONTRIBUTING.md).

### Segurança

Se você descobrir qualquer problema relacionado a segurança, por favor, entre em contato através do e-mail cyberlpkf@gmail.com.

## Créditos

-   [Mateus Galasso](https://github.com/stonkeep) (pacote original)
-   [Luis Fernando Kieça](https://github.com/cyberlpkf)

## Licença

The MIT License (MIT). Por favor veja [o arquivo de licença](LICENSE.md) para mais informações.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
