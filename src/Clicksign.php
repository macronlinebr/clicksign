<?php

namespace Macronlinebr\Clicksign;

use Macronlinebr\Clicksign\Exceptions\IntegrationNotEnabledException;
use Macronlinebr\Clicksign\Exceptions\InvalidDevelopmentUrlConfigurationException;
use Macronlinebr\Clicksign\Exceptions\InvalidDocumentKeyException;
use Macronlinebr\Clicksign\Exceptions\InvalidDocumentSignDurationConfigurationException;
use Macronlinebr\Clicksign\Exceptions\InvalidDocumentUrlConfigurationException;
use Macronlinebr\Clicksign\Exceptions\InvalidEmailException;
use Macronlinebr\Clicksign\Exceptions\InvalidKeyException;
use Macronlinebr\Clicksign\Exceptions\InvalidListUrlConfigurationException;
use Macronlinebr\Clicksign\Exceptions\InvalidNameException;
use Macronlinebr\Clicksign\Exceptions\InvalidNotificationUrlConfiguration;
use Macronlinebr\Clicksign\Exceptions\InvalidPathException;
use Macronlinebr\Clicksign\Exceptions\InvalidProductionUrlConfigurationException;
use Macronlinebr\Clicksign\Exceptions\InvalidSignerKeyException;
use Macronlinebr\Clicksign\Exceptions\InvalidSignerUrlConfigurationException;
use Macronlinebr\Clicksign\Exceptions\InvalidUpdateDocumentUrlConfigurationException;
use Macronlinebr\Clicksign\Exceptions\NoAccessTokenException;
use Macronlinebr\Clicksign\Exceptions\NoApiSetException;
use Macronlinebr\Clicksign\Exceptions\NoConfigurationFoundException;
use Macronlinebr\Clicksign\Exceptions\NoFilialSetException;
use Macronlinebr\Clicksign\Models\Api;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Throwable;

class Clicksign
{
    protected string $devAccessToken = '';
    protected string $accessToken = '';
    protected string $prodAccessToken = '';
    protected string $documentEndPoint = '';
    protected string $updateDocumentEndPoint = '';
    protected string $listEndPoint = '';
    protected string $notificationEndPoint = '';
    protected string $signerEndPoint = '';
    protected string $urlBase = '';
    protected string $developmentUrl = '';
    protected string $productionUrl = '';
    protected bool $useIntegration = false;
    protected string $environment = '';
    protected bool $useConfigOnDatabase = false;
    protected int $documentSignDuration = 30;

    protected int $api_id = 0;
    protected int $filial_id = 0;

    protected bool $isConfigLoaded = false;
    protected bool $isConfigValidated = false;

    public function __construct($api_id = 0, $filial_id = 0)
    {
        $this->api_id = $api_id;
        $this->filial_id = $filial_id;
    }

    /**
     * @return void
     * @throws Throwable
     */
    protected function loadConfig() : void
    {
        try {
            if (!$this->isConfigLoaded) {
                $this->useConfigOnDatabase = config('clicksign.useConfigOnDatabase');

                if ($this->useConfigOnDatabase) {
                    $api = (new Api)
                        ->where('api_id', '=', $this->api_id)
                        ->where('filial_id', '=', $this->filial_id);

                    if (Schema::hasColumn('api', 'deleted_at'))
                        $api = $api->whereNull('deleted_at');

                    $api = $api->first() ?? null;

                    throw_if(!$api->id, (new NoConfigurationFoundException));

                    $this->documentEndPoint = $api?->credencial['documentUrlVersion'] ?? '';
                    $this->listEndPoint = $api?->credencial['listUrlVersion'] ?? '';
                    $this->notificationEndPoint = $api?->credencial['notificationUrlVersion'] ?? '';
                    $this->signerEndPoint = $api?->credencial['signerUrlVersion'] ?? '';
                    $this->developmentUrl = $api?->credencial['developmentUrl'] ?? '';
                    $this->productionUrl = $api?->credencial['productionUrl'] ?? '';
                    $this->updateDocumentEndPoint = $api?->credencial['updateDocumentUrlVersion'] ?? '';

                    $this->useIntegration = $api?->credencial['useIntegration'] == "true" ?? false;

                    // Caso a variável environment não esteja configurada, assume como desenvolvimento.
                    $this->environment = $api?->credencial['environment'] ?? 'dev';
                    $this->documentSignDuration = $api?->credencial['documentSignDuration'] ?? 0;

                    $this->devAccessToken = $api?->credencial['devAccessToken'] ?? '';
                    $this->prodAccessToken = $api?->credencial['prodAccessToken'] ?? '';
                } else {
                    $this->documentEndPoint = config('clicksign.documentUrlVersion');
                    $this->listEndPoint = config('clicksign.listUrlVersion');
                    $this->notificationEndPoint = config('clicksign.notificationUrlVersion');
                    $this->signerEndPoint = config('clicksign.signersUrlVersion');
                    $this->developmentUrl = config('clicksign.developmentUrl');
                    $this->productionUrl = config('clicksign.productionUrl');
                    $this->updateDocumentEndPoint = config('clicksign.updateDocumentEndPoint');

                    $this->useIntegration = config('clicksign.useIntegration', false) == "true";

                    // Caso a variável environment não esteja configurada, assume como desenvolvimento.
                    $this->environment = config('clicksign.environment', 'dev');
                    $this->documentSignDuration = config('clicksign.documentSignDuration', 0);

                    $this->devAccessToken = config('clicksign.devAccessToken');
                    $this->prodAccessToken = config('clicksign.prodAccessToken');
                }

                $this->urlBase = $this->environment == 'prod' ? $this->productionUrl : $this->developmentUrl;
                $this->accessToken = $this->environment == 'prod' ? $this->prodAccessToken : $this->devAccessToken;

                $this->isConfigLoaded = true;
            }
        } catch (\Exception $e) {
            return;
        }
    }

    protected function validateConfig() : void
    {
        if (!$this->isConfigValidated) {
            throw_if(is_null($this->documentEndPoint), InvalidDocumentUrlConfigurationException::class);
            throw_if(is_null($this->updateDocumentEndPoint), InvalidUpdateDocumentUrlConfigurationException::class);
            throw_if(is_null($this->listEndPoint), InvalidListUrlConfigurationException::class);
            throw_if(is_null($this->notificationEndPoint), InvalidNotificationUrlConfiguration::class);
            throw_if(is_null($this->signerEndPoint), InvalidSignerUrlConfigurationException::class);
            throw_if(is_null($this->documentSignDuration) || floatval($this->documentSignDuration) < 1, InvalidDocumentSignDurationConfigurationException::class);
            throw_if($this->useConfigOnDatabase && $this->api_id == 0, NoApiSetException::class);
            throw_if($this->useConfigOnDatabase && $this->filial_id == 0, NoFilialSetException::class);
            throw_if(!$this->environment !== 'prod' && is_null($this->developmentUrl), InvalidDevelopmentUrlConfigurationException::class);
            throw_if($this->environment === 'prod' && is_null($this->productionUrl), InvalidProductionUrlConfigurationException::class);
            $this->isConfigValidated = true;
        }
    }

    public function getApiId() : int
    {
        return $this->api_id;
    }

    public function setApiId(int $api_id) : void
    {
        $this->api_id = $api_id;
    }

    public function getFilialId() : int
    {
        return $this->filial_id;
    }

    public function setFilialId(int $filial_id) : void
    {
        $this->filial_id = $filial_id;
    }

    public function getDocumentSignDuration() : int
    {
        return $this->documentSignDuration;
    }

    public function setDocumentSignDuration(int $duration) : void
    {
        $this->documentSignDuration = $duration;
        $this->isConfigValidated = false;
    }

    public function getUseIntegration() : bool
    {
        if (!$this->isConfigLoaded) {
            $this->loadConfig();
            $this->validateConfig();
        }

        return $this->useIntegration;
    }

    public function setUseIntegration(bool $useIntegration) : void
    {
        $this->useIntegration = $useIntegration;
        $this->isConfigValidated = false;
    }

    /**
     * @throws Throwable
     */
    public function validateToken() : void
    {
        $this->loadConfig();
        $this->validateConfig();

        throw_if(is_null($this->accessToken), (new NoAccessTokenException));
    }

    /**
     * @param String $path //Path in your machine or server
     * @param String|null $clicksignPath //path in clicsksign server
     * @param String $mimetype //mimtype of file
     * @param null $deadline
     * @param bool $autoClose
     * @param string $locale
     * @param bool $sequence_enabled
     * @return Response
     * @throws Throwable
     */
    public function createDocument(String $path, String $clicksignPath = null, string $mimetype = 'application/pdf', $deadline = null, bool $autoClose = true,
                                   string $locale = 'pt-BR', bool $sequence_enabled = false) : Response
    {
        $this->validateToken();

        throw_if(!isset($path), (new InvalidPathException));
        throw_if(!$this->useIntegration, IntegrationNotEnabledException::class);

        $body = [
            "document" => [
                "path" => $clicksignPath ? "/$clicksignPath" : "/$path",
                "content_base64" => "data:$mimetype;base64," . base64_encode(Storage::get($path)),
                "deadline_at" => $deadline ?? Carbon::now()->addDays($this->documentSignDuration) ?? null,
                "auto_close" => $autoClose,
                "locale" => $locale,
                "sequence_enabled" => $sequence_enabled
            ]
        ];

        //return Http::post("$this->urlBase/api/v1/documents?access_token=$this->accessToken", $body);
        return Http::withBody(json_encode($body), 'application/json')
            ->post("$this->urlBase$this->documentEndPoint?access_token=$this->accessToken");
    }

    /**
     * @param $key
     * @return Response
     * @throws Throwable
     */
    public function cancelDocument($key) : Response
    {
        $this->validateToken();

        throw_if(!isset($key), (new InvalidKeyException));

        throw_if(!$this->useIntegration, IntegrationNotEnabledException::class);

        return Http::patch("$this->urlBase$this->documentEndPoint/$key/cancel?access_token=$this->accessToken");
    }

    /**
     * @param $key
     * @return Response
     * @throws Throwable
     */
    public function deleteDocument($key) : Response
    {
        $this->validateToken();

        throw_if(!isset($key), (new InvalidKeyException));

        throw_if(!$this->useIntegration, IntegrationNotEnabledException::class);

        return Http::delete("$this->urlBase$this->documentEndPoint/$key?access_token=$this->accessToken");
    }

    /**
     * @param String $email
     * @param String $name
     * @param null $phoneNumber
     * @param string|null $documentation
     * @param null $birthday
     * @param bool $has_documentation
     * @return Response
     * @throws Throwable
     */
    public function createSigner(String $email, String $name, $phoneNumber = null, string $documentation = null, $birthday = null, bool $has_documentation = false) : Response
    {
        $this->validateToken();
        //Verify if parameters were passed
        throw_if(!isset($name), (new InvalidNameException));
        throw_if(!isset($email), (new InvalidEmailException));
        throw_if(!$this->useIntegration, IntegrationNotEnabledException::class);
        //Mount body
        $body = [
            "signer" => [
                "email" => $email,
                "phone_number" => $phoneNumber,
                "auths" => [
                    "email"
                ],
                "name" => $name,
                "documentation" => $documentation,
                "birthday" => $birthday,
                "has_documentation" => $has_documentation
            ]
        ];
        return Http::withBody(json_encode($body), 'application/json')
            ->post("$this->urlBase$this->signerEndPoint?access_token=$this->accessToken");
    }

    /**
     * @param string $document_key
     * @param string $signer_key
     * @param string $sign_as
     * @return Response
     * @throws Throwable
     */
    public function signerToDocument(string $document_key, string $signer_key, string $sign_as = 'approve') : Response
    {
        $this->validateToken();

        throw_if(!isset($document_key), (new InvalidDocumentKeyException));
        throw_if(!isset($signer_key), (new InvalidSignerKeyException));
        throw_if(!$this->useIntegration, IntegrationNotEnabledException::class);

//        $message = $message ?? "Prezado ,\nPor favor assine o documento.\n\nQualquer dúvida estou à disposição.\n\nAtenciosamente.";

        $body = [
            "list" => [
                "document_key" => $document_key,
                "signer_key" => $signer_key,
                "sign_as" => $sign_as,
//                "message" => $message
            ]
        ];
        return Http::post("$this->urlBase$this->listEndPoint?access_token=$this->accessToken", $body);
    }

    /**
     * @param string $signer_key
     * @param null $message
     * @return Response
     * @throws Throwable
     */
    public function notificationsByEmail(string $signer_key, $message = null) : Response
    {
        $this->validateToken();

        throw_if(!isset($signer_key), (new InvalidSignerKeyException));
        throw_if(!$this->useIntegration, IntegrationNotEnabledException::class);

        $body = [
            "request_signature_key" => $signer_key,
            "message" => $message ?? "Prezado Sr(a).\nPor favor, assine o documento.\n\nQualquer dúvida, estamos a disposição.\n\nAtenciosamente.",
        ];
        return Http::post("$this->urlBase$this->notificationEndPoint?access_token=$this->accessToken", $body);
    }

    /**
     * @param String $document_key
     * @return Response
     * @throws Throwable
     */
    public function showDocument(String $document_key) : Response
    {
        $this->validateToken();
        //Verify if parameters were passed
        throw_if(!isset($document_key), (new InvalidDocumentKeyException));
        throw_if(!$this->useIntegration, IntegrationNotEnabledException::class);
        return Http::get("$this->urlBase$this->documentEndPoint/$document_key?access_token=$this->accessToken");
    }

    public function updateDocumentDeadline(String $document_key, \DateTime $deadline_at) : Response
    {
        $this->validateToken();

        throw_if(!$this->useIntegration, IntegrationNotEnabledException::class);

        $body = [
            "document" => [
                "deadline_at" => $deadline_at,
            ]
        ];

        return Http::withBody(json_encode($body), 'application/json')
            ->patch("$this->urlBase$this->updateDocumentEndPoint?access_token=$this->accessToken");
    }
}