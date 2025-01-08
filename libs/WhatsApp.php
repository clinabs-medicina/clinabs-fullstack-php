<?php
namespace HalloAPI;

class WhatsApp
{
    private string $baseUrl;
    private string $login;

    public function __construct($instanceKey, $instanceToken, $login)
    {
        $this->login = $login;
        $this->baseUrl = "https://app.hallo-api.com/v1/instance/$instanceKey/token/$instanceToken";
    }

    public function requestProfileImage($phoneNumber)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->baseUrl . '/profile',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('fLogin' => $this->login, 'ACTION' => 'IMAGE_URL', 'number' => preg_replace('/[^0-9]+/', '', $phoneNumber)),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }

    public function sendDocMessage($phoneNumber, $text, $docUrl, $linkTitle = '')
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->baseUrl . '/message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'fLogin' => $this->login,
                'ACTION' => 'DOCUMENT',
                'destination' => '55' . preg_replace('/[^0-9]+/', '', $phoneNumber),
                'text' => $text,
                'document_name' => $linkTitle,
                'document_url' => $docUrl
            )
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    public function sendLinkMessage($phoneNumber, $text, $linkUrl, $linkTitle = '', $linkDescription = '', $linkImage = '')
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->baseUrl . '/message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'fLogin' => $this->login,
                'ACTION' => 'LINK',
                'destination' => '55' . preg_replace('/[^0-9]+/', '', $phoneNumber),
                'text' => $text,
                'linkUrl' => $linkUrl,
                'linkTitulo' => $linkTitle,
                'linkDescription' => $linkDescription,
                'linkImage' => $linkImage
            )
        ));

        $response = curl_exec($curl);

        $response = json_decode($response);

        $response->request = array(
            'fLogin' => $this->login,
            'ACTION' => 'LINK',
            'destination' => preg_replace('/[^0-9]+/', '', $phoneNumber),
            'text' => $text,
            'linkUrl' => $linkUrl,
            'linkTitulo' => $linkTitle,
            'linkDescription' => $linkDescription,
            'linkImage' => $linkImage
        );

        $resp = [
            'request' => $response->request,
            'response' => $response
        ];

        curl_close($curl);

        return $response;
    }

    public function sendTextMessage(string $phoneNumber, string $text)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->baseUrl . '/message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'fLogin' => $this->login,
                'ACTION' => 'TEXT',
                'destination' => preg_replace('/[^0-9]+/', '', $phoneNumber),
                'text' => $text,
            )
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response, true);
    }

    public function sendDocumentLink($phoneNumber, $linkName, $linkUrl, $text)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->baseUrl . '/message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'fLogin' => $this->login,
                'ACTION' => 'DOCUMENT',
                'destination' => '55' . preg_replace('/[^0-9]+/', '', $phoneNumber),
                'text' => $text,
                'document_name' => $linkName,
                'document_url' => $linkUrl
            )
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }
}
