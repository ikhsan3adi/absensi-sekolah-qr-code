<?php

namespace App\Libraries\Whatsapp\Fonnte;

use App\Libraries\Whatsapp\Whatsapp;

class Fonnte implements Whatsapp
{
    private string $urlApi = 'https://api.fonnte.com/send';

    public function __construct(public ?string $token) {}

    public function getProvider(): string
    {
        return 'Fonnte';
    }

    public function getToken(): string
    {
        return $this->token ?? env('WHATSAPP_TOKEN');
    }

    /**
     * Send message to Fonnte API
     * @param array|string $messages
     * @return string
     */
    public function sendMessage(array|string $messages): string
    {
        $messages = isset($messages[0]) ? $messages : [$messages];
        $fonnteMessage = new FonnteBulkMessage($messages);
        $curl = curl_init();
        $jsonMessage = $fonnteMessage->toJson();

        // Enable verbose output for debugging
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->urlApi,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('data' => $jsonMessage),
            CURLOPT_HTTPHEADER => array(
                "Authorization: {$this->getToken()}" //change TOKEN to your actual token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        try {
            $responseStatus = json_decode($response, 1);
            $resultMessage = $responseStatus['reason'] ?? '';

            if ($responseStatus['status']) {
                $resultMessage = $responseStatus['detail'] ?? 'Sukses';
            }

            return $resultMessage;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
