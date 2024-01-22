<?php

namespace Cyberlpkf\Clicksign;

use Cyberlpkf\Clicksign\Exceptions\InvalidDocumentKey;
use Cyberlpkf\Clicksign\Exceptions\InvalidEmail;
use Cyberlpkf\Clicksign\Exceptions\InvalidKey;
use Cyberlpkf\Clicksign\Exceptions\InvalidName;
use Cyberlpkf\Clicksign\Exceptions\InvalidPath;
use Cyberlpkf\Clicksign\Exceptions\InvalidSignerKey;
use Cyberlpkf\Clicksign\Exceptions\NoAccessTokenException;
use Cyberlpkf\Clicksign\Models\Api;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Throwable;

class Clicksign
{
    private string $accessToken;
    private string $documentUrlVersion = '/api/v1/documents';
    private string $listUrlVersion = '/api/v1/lists';
    private string $notificationUrlVersion = '/api/v1/notifications';
    private string $signerUrlVersion = '/api/v1/signers';
    private string $urlBase;

    public function __construct(int $api_id, int $filial)
    {
        try {
            $this->accessToken = (new Api)
                ->where('
                api_id', '=', $api_id)
                ->where('filial_id', '=', $filial)
                ->first()
                ?->credencial
                ?->accessToken ?? null;

            //get url version
            $this->documentUrlVersion = config('clicksign.documentUrlVersion');

            $this->listUrlVersion = config('clicksign.listUrlVersion');

            $this->notificationUrlVersion = config('clicksign.notificationUrlVersion');

            $this->signerUrlVersion = config('clicksign.signersUrlVersion');

            //Mount base URL
            $this->urlBase = config('clicksign.urlBase');

        } catch (\Exception $e) {
            return;
        }
    }

    /**
     * @throws Throwable
     */
    public function validateToken() : void
    {
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
        //Verify if parameters were passed
        throw_if(!isset($path), (new InvalidPath));
        //Mount body
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
        //Verify if parameters were passed
        throw_if(!isset($key), (new InvalidKey));

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
        //Verify if parameters were passed
        throw_if(!isset($key), (new InvalidKey));

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
        throw_if(!isset($name), (new InvalidName));
        throw_if(!isset($email), (new InvalidEmail));
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
        throw_if(!isset($document_key), (new InvalidDocumentKey));
        throw_if(!isset($signer_key), (new InvalidSignerKey));

//        $message = $message ?? "Prezado ,\nPor favor assine o documento.\n\nQualquer dúvida estou à disposição.\n\nAtenciosamente.";
        //Mount request body
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
        //Verify if parameters were passed
        throw_if(!isset($signer_key), (new InvalidSignerKey));
        //Mount body
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
        throw_if(!isset($document_key), (new InvalidDocumentKey));
        return Http::get("$this->urlBase$this->documentUrlVersion/$document_key?access_token=$this->accessToken");
    }
}
