<?php

namespace App\Http\Controllers\Auth;

use App\CryptoAddress;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

use Coinbase\Wallet\Client;
use Coinbase\Wallet\Configuration;
use Coinbase\Wallet\Resource\Address;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $configuration = Configuration::apiKey(env('COINBASE_API_KEY'), env('COINBASE_API_SECRET'));
        $client = Client::create($configuration);
        $client->enableActiveRecord();

        $btc_wal = $client->getAccount(env('BTC_ACCOUNT_ID'));
        $eth_wal=  $client->getAccount(env('ETH_ACCOUNT_ID'));
        $etc_wal = $client->getAccount(env('ETC_ACCOUNT_ID'));
        $bat_wal=  $client->getAccount(env('BAT_ACCOUNT_ID'));
        $ltc_wal=  $client->getAccount(env('LTC_ACCOUNT_ID'));


        $address = new Address(['name' => $data['email']]);
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
        $coin_address->useremail = $data['email'];
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

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
