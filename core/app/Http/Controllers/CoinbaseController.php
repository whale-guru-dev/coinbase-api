<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Coinbase\Wallet\Client;
use Coinbase\Wallet\Configuration;
use Coinbase\Wallet\Resource\Address;
use Coinbase\Wallet\Resource\Account;
use Coinbase\Wallet\Exception\HttpException;

use App\CryptoAddress;
Use App\User;
use App\Crypto;

class CoinbaseController extends Controller
{
    //
    private $client;

    public function __construct()
    {
        $configuration = Configuration::apiKey(env('COINBASE_API_KEY'), env('COINBASE_API_SECRET'));
        $this->client =  Client::create($configuration);
    }

    public function createWallet(Request $request)
    {
        try {
//            $accounts = $this->client->getAccounts();
//            dd($accounts);

            $account = new Account([
                'name' => 'BTC',
                'currency' => 'btc'
            ]);
            $this->client->createAccount($account);

            $account = new Account([
                'name' => 'ETH',
                'currency' => 'eth'
            ]);
            $this->client->createAccount($account);

            $account = new Account([
                'name' => 'ETC',
                'currency' => 'etc'
            ]);
            $this->client->createAccount($account);

            $account = new Account([
                'name' => 'BAT',
                'currency' => 'bat'
            ]);
            $this->client->createAccount($account);

            $account = new Account([
                'name' => 'LTC',
                'currency' => 'ltc'
            ]);
            $this->client->createAccount($account);

        } catch (Exception $e) {
//            echo $e->getMessage();
            dd($e);
        }

    }

    public function Ipn_Manage()
    {
        $raw_body = file_get_contents('php://input');

        $data = json_decode($raw_body,true);


        if( ($data['resource'] == 'notification') && ($data['type'] == 'wallet:addresses:new-payment') && (!empty(CryptoAddress::where('userEmail', $data['data']['name'])->first())) && (empty(Crypto::where('hash', '0x'.$data['additional_data']['hash'])->first())) ){
            $configuration = Configuration::apiKey(env('COINBASE_API_KEY'), env('COINBASE_API_SECRET'));
            $client = Client::create($configuration);
            $signature = $_SERVER['HTTP_CB_SIGNATURE'];
            $authenticity = $client->verifyCallback($raw_body, $signature);

            $trid = $data['id']; // notification id

            $type = $data['type'];

            $resource = $data['resource']; // must be notification

            $address  = $data['data']['address'];
            $name     = $data['data']['name'];  // de asociat cu user

            $hash     = $data['additional_data']['hash'];  // de asociat cu user

            $ad_cur   = $data['additional_data']['amount']['currency'];
            $ad_value = $data['additional_data']['amount']['amount'];
            $ad_trid  = $data['additional_data']['transaction']['id']; //transaction id

            $deanostru = CryptoAddress::where('userEmail', $name)->first();

            $fak = User::where('email', $name)->first();
            $hash = '0x'.$hash;

            $crypto = new Crypto;
            $crypto->who = $fak->id;
            $crypto->address = $address;
            $crypto->coin = $ad_cur;
            $crypto->amount = $ad_value;
            $crypto->trxid = $ad_trid;
            $crypto->tm = now();
            $crypto->sig = '+';
            $crypto->notification = $trid;
            $crypto->hash = $hash;
            $crypto->user = $name;
            $crypto->details = 'Deposit';
            $crypto->type = 'deposit';
            $crypto->status = 'pending';
            $crypto->save();

            return response()->json(['status'=>'ok']);
        }
    }

    public function guzzletest() {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://api.coinbase.com/v2/accounts');

        echo $response->getStatusCode(); // 200
        echo $response->getHeaderLine('content-type'); // 'application/json; charset=utf8'
        echo $response->getBody(); // '{"id": 1420053, "name": "guzzle", ...}'

// Send an asynchronous request.
        $request = new \GuzzleHttp\Psr7\Request('GET', 'http://httpbin.org');
        $promise = $client->sendAsync($request)->then(function ($response) {
            echo 'I completed! ' . $response->getBody();
        });

        $promise->wait();
    }
}
