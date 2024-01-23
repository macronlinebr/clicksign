<?php

namespace Cyberlpkf\Clicksign;

use Cyberlpkf\Clicksign\Exceptions\InvalidDevelopmentUrlConfigurationException;
use Cyberlpkf\Clicksign\Exceptions\InvalidDocumentKeyException;
use Cyberlpkf\Clicksign\Exceptions\InvalidDocumentUrlConfigurationException;
use Cyberlpkf\Clicksign\Exceptions\InvalidEmailException;
use Cyberlpkf\Clicksign\Exceptions\InvalidKeyException;
use Cyberlpkf\Clicksign\Exceptions\InvalidListUrlConfigurationException;
use Cyberlpkf\Clicksign\Exceptions\InvalidNameException;
use Cyberlpkf\Clicksign\Exceptions\InvalidNotificationUrlConfiguration;
use Cyberlpkf\Clicksign\Exceptions\InvalidPathException;
use Cyberlpkf\Clicksign\Exceptions\InvalidProductionUrlConfigurationException;
use Cyberlpkf\Clicksign\Exceptions\InvalidSignerKeyException;
use Cyberlpkf\Clicksign\Exceptions\InvalidSignerUrlConfigurationException;
use Cyberlpkf\Clicksign\Exceptions\NoAccessTokenException;
use Cyberlpkf\Clicksign\Exceptions\NoApiSetException;
use Cyberlpkf\Clicksign\Exceptions\NoConfigurationFoundException;
use Cyberlpkf\Clicksign\Exceptions\NoFilialSetException;
use Cyberlpkf\Clicksign\Models\Api;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Throwable;

class Clicksign
{
    protected string $devAccessToken;
    protected string $prodAccessToken;
    protected string $documentUrlVersion;
    protected string $listUrlVersion;
    protected string $notificationUrlVersion;
    protected string $signerUrlVersion;
    protected string $urlBase;
    protected string $developmentUrl;
    protected string $productionUrl;
    protected bool $devMode;
    protected bool $useConfigOnDatabase;

    protected int $api_id;
    protected int $filial_id;

    protected bool $isConfigLoaded = false;
    protected bool $isConfigValidated = false;

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
                        ->where('filial_id', '=', $this->filial_id)
                        ->first() ?? null;

                    throw_if(!$api->id, (new NoConfigurationFoundException));

                    $this->documentUrlVersion = $api?->credencial['documentUrlVersion'] ?? null;
                    $this->listUrlVersion = $api?->credencial['listUrlVersion'] ?? null;
                    $this->notificationUrlVersion = $api?->credencial['notificationUrlVersion'] ?? null;
                    $this->signerUrlVersion = $api?->credencial['signerUrlVersion'] ?? null;
                    $this->developmentUrl = $api?->credencial['developmentUrl'] ?? null;
                    $this->productionUrl = $api?->credencial['productionUrl'] ?? null;

                    // Caso a variável devMode não esteja configurada, assume como desenvolvimento.
                    $this->devMode = $api?->credencial['devMode'] ?? true;

                    $this->devAccessToken = $api?->devAccessToken ?? null;
                    $this->prodAccessToken = $api?->prodAccessToken ?? null;
                } else {
                    $this->documentUrlVersion = config('clicksign.documentUrlVersion');
                    $this->listUrlVersion = config('clicksign.listUrlVersion');
                    $this->notificationUrlVersion = config('clicksign.notificationUrlVersion');
                    $this->signerUrlVersion = config('clicksign.signersUrlVersion');
                    $this->developmentUrl = config('clicksign.developmentUrl');
                    $this->productionUrl = config('clicksign.productionUrl');

                    // Caso a variável devMode não esteja configurada, assume como desenvolvimento.
                    $this->devMode = config('clicksign.devMode', true);
                    $this->urlBase = $this->devMode ? $this->developmentUrl : $this->productionUrl;

                    $this->devAccessToken = config('clicksign.devAccessToken');
                    $this->prodAccessToken = config('clicksign.prodAccessToken');
                }

                $this->urlBase = $this->devMode ? $this->developmentUrl : $this->productionUrl;
                $this->accessToken = $this->devMode ? $this->devAccessToken : $this->prodAccessToken;

                $this->isConfigLoaded = true;
            }
        } catch (\Exception $e) {
            return;
        }
    }

    protected function validateConfig() : void
    {
        if (!$this->isConfigValidated) {
            throw_if(is_null($this->documentUrlVersion), (new InvalidDocumentUrlConfigurationException));
            throw_if(is_null($this->listUrlVersion), (new InvalidListUrlConfigurationException));
            throw_if(is_null($this->notificationUrlVersion), (new InvalidNotificationUrlConfiguration));
            throw_if(is_null($this->signerUrlVersion), (new InvalidSignerUrlConfigurationException));
            throw_if($this->useConfigOnDatabase && is_null($this->api_id), (new NoApiSetException));
            throw_if($this->useConfigOnDatabase && is_null($this->filial_id), (new NoFilialSetException));
            throw_if($this->devMode && is_null($this->developmentUrl), (new InvalidDevelopmentUrlConfigurationException));
            throw_if(!$this->devMode && is_null($this->productionUrl), (new InvalidProductionUrlConfigurationException));
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

        $body = [
            "document" => [
                "path" => $clicksignPath ? "/$clicksignPath" : "/$path",
                "content_base64" => "data:$mimetype;base64," . base64_encode(Storage::get($path)),
                "deadline_at" => $deadline,
                "auto_close" => $autoClose,
                "locale" => $locale,
                "sequence_enabled" => $sequence_enabled
            ]
        ];
        //return Http::post("$this->urlBase/api/v1/documents?access_token=$this->accessToken", $body);
        return Http::post("$this->urlBase$this->documentUrlVersion?access_token=$this->accessToken", $body);
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

        return Http::patch("$this->urlBase$this->documentUrlVersion/$key/cancel?access_token=$this->accessToken");
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

        return Http::delete("$this->urlBase$this->documentUrlVersion/$key?access_token=$this->accessToken");
    }

    /**
     * @param String $email
     * @param String $name
     * @param null $phoneNumber
     * @param bool $documentation
     * @param null $birthday
     * @param bool $has_documentation
     * @return Response
     * @throws Throwable
     */
    public function createSigner(String $email, String $name, $phoneNumber = null, bool $documentation = false, $birthday = null, bool $has_documentation = false) : Response
    {
        $this->validateToken();
        //Verify if parameters were passed
        throw_if(!isset($name), (new InvalidNameException));
        throw_if(!isset($email), (new InvalidEmailException));
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
        return Http::post("$this->urlBase$this->signerUrlVersion?access_token=$this->accessToken", $body);
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

//        $message = $message ?? "Prezado ,\nPor favor assine o documento.\n\nQualquer dúvida estou à disposição.\n\nAtenciosamente.";

        $body = [
            "list" => [
                "document_key" => $document_key,
                "signer_key" => $signer_key,
                "sign_as" => $sign_as,
//                "message" => $message
            ]
        ];
        return Http::post("$this->urlBase$this->listUrlVersion?access_token=$this->accessToken", $body);
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

        $body = [
            "request_signature_key" => $signer_key,
            "message" => $message ?? "Prezado Sr(a).\nPor favor, assine o documento.\n\nQualquer dúvida, estamos a disposição.\n\nAtenciosamente.",
        ];
        return Http::post("$this->urlBase$this->notificationUrlVersion?access_token=$this->accessToken", $body);
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
        return Http::get("$this->urlBase$this->documentUrlVersion/$document_key?access_token=$this->accessToken");
    }
}
