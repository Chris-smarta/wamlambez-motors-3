<script>
// app/Http/Controllers/MpesaController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class MpesaController extends Controller
{
    private $lipaNaMpesaUrl = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    private $shortcode = 'your_shortcode';
    private $lipaNaMpesaPasskey = 'your_passkey';
    private $lipaNaMpesaShortcode = 'your_shortcode';
    private $lipaNaMpesaSecret = 'your_api_secret';
    private $lipaNaMpesaConsumerKey = 'your_consumer_key';

    public function initiatePayment(Request $request)
    {
        $phoneNumber = $request->input('phoneNumber'); // User's phone number
        $amount = $request->input('amount'); // Payment amount

        $timestamp = date("YmdHis");
        $password = base64_encode($this->lipaNaMpesaShortcode . $this->lipaNaMpesaPasskey . $timestamp);

        $client = new Client();
        $response = $client->post($this->lipaNaMpesaUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'BusinessShortCode' => $this->shortcode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => $amount,
                'PartyA' => $phoneNumber,
                'PartyB' => $this->shortcode,
                'PhoneNumber' => $phoneNumber,
                'CallBackURL' => 'https://yourdomain.com/callback', // Your callback URL
                'AccountReference' => 'Test123',
                'TransactionDesc' => 'Payment for testing',
            ],
        ]);

        return response()->json(json_decode($response->getBody()));
    }

    private function getAccessToken()
    {
        $client = new Client();
        $response = $client->post('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials', [
            'auth' => [$this->lipaNaMpesaConsumerKey, $this->lipaNaMpesaSecret],
        ]);

        $response = json_decode($response->getBody());
        return $response->access_token;
    }
}
</script>
