<?php

namespace App\Http\Controllers;

use App\CryptoAddress;
use Illuminate\Http\Request;
use Coinbase\Wallet\Client;
use Coinbase\Wallet\Configuration;
use Coinbase\Wallet\Resource\Address;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $configuration = Configuration::apiKey(env('COINBASE_API_KEY'), env('COINBASE_API_SECRET'));
        $client = Client::create($configuration);
        $client->enableActiveRecord();

        $rates = $client->getExchangeRates();
        $btcx = $client->getSpotPrice('BTC-USD');
        $btc = $client->decodeLastResponse();
        $ethx = $client->getSpotPrice('ETH-USD');
        $eth = $client->decodeLastResponse();
        $etcx = $client->getSpotPrice('ETC-USD');
        $etc = $client->decodeLastResponse();
        $ltcx = $client->getSpotPrice('LTC-USD');
        $ltc = $client->decodeLastResponse();
        $batx = $client->getSpotPrice('BAT-USD');
        $bat = $client->decodeLastResponse();

        $total_btc_usd=Auth::user()->btc * $btc['data']['amount'];
        $total_eth_usd=Auth::user()->eth * $eth['data']['amount'];
        $total_etc_usd=Auth::user()->etc * $etc['data']['amount'];
        $total_ltc_usd=Auth::user()->ltc * $ltc['data']['amount'];
        $total_bat_usd=Auth::user()->bat * $bat['data']['amount'];

        $total_bal_usd = $total_btc_usd + $total_eth_usd + $total_etc_usd + $total_ltc_usd + $total_bat_usd;

        $btc_wal = $client->getAccount(env('BTC_ACCOUNT_ID'));
        $eth_wal=  $client->getAccount(env('ETH_ACCOUNT_ID'));
        $etc_wal = $client->getAccount(env('ETC_ACCOUNT_ID'));
        $bat_wal=  $client->getAccount(env('BAT_ACCOUNT_ID'));
        $ltc_wal=  $client->getAccount(env('LTC_ACCOUNT_ID'));

        $address = new Address(['name' => Auth::user()->email]);
        $client->createAccountAddress($btc_wal, $address);
        $btc_data = $client->decodeLastResponse();

        $client->createAccountAddress($eth_wal, $address);
        $eth_data = $client->decodeLastResponse();

        $client->createAccountAddress($etc_wal, $address);
        $etc_data = $client->decodeLastResponse();

        $client->createAccountAddress($ltc_wal, $address);
        $ltc_data = $client->decodeLastResponse();

        $client->createAccountAddress($bat_wal, $address);
        $bat_data = $client->decodeLastResponse();

        $coin_address = new CryptoAddress;
        $coin_address->useremail = Auth::user()->email;
        $coin_address->btc = $btc_data['data']['address'];
        $coin_address->btc_more = $btc_data['data']['id'];
        $coin_address->eth = $eth_data['data']['address'];
        $coin_address->eth_more = $eth_data['data']['id'];
        $coin_address->etc = $etc_data['data']['address'];
        $coin_address->etc_more = $etc_data['data']['id'];
        $coin_address->bat = $bat_data['data']['address'];
        $coin_address->bat_more = $bat_data['data']['id'];
        $coin_address->ltc = $ltc_data['data']['address'];
        $coin_address->ltc_more = $ltc_data['data']['id'];
        $coin_address->save();

        $wallet = $coin_address;

        return view('home', compact('total_bal_usd', 'wallet'));
    }
}
