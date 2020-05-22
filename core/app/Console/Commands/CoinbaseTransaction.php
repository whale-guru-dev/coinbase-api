<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Coinbase\Wallet\Client;
use Coinbase\Wallet\Configuration;
use Coinbase\Wallet\Resource\Transaction;

use Coinbase\Wallet\Value\Money;
use Coinbase\Wallet\Resource\Address;
use Coinbase\Wallet\Enum\CurrencyCode;
use App\User;
use App\Crypto;

class CoinbaseTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Coinbase:Transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Coinbase Pending Transaction Check';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $configuration = Configuration::apiKey(env('COINBASE_API_KEY'), env('COINBASE_API_SECRET'));
        $client = Client::create($configuration);
        $client->enableActiveRecord();


        $users = User::all();
        foreach($users as $user) {
            $pending_crypto = Crypto::where('who',$user->id)->where('status','pending')->get();

            if($pending_crypto->count()>0){
                foreach($pending_crypto as $tranz){
                    try{

                        $data = $client->decodeLastResponse();

                        $desc = $data['data']['description'];
                        if(!is_null($desc) && !empty($desc)){
                            $desc = str_word_count($data['data']['description'], 1)[0];
                        }

                        if($desc != 'Withdraw'){

                            if ($data['data']['status'] == 'completed'){
                                if($tranz['type'] == 'deposit'){
                                    $tranz->status = 'confirm';
                                    $tranz->save();

                                    if ($tranz['coin'] == 'BTC') {
                                        $btc_bal = $user->btc;
                                        $user->btc = $btc_bal + $tranz['amount'];
                                        $user->save();
                                    } else if($tranz['coin'] == 'ETH') {
                                        $eth_bal = $user->eth;
                                        $user->eth = $eth_bal + $tranz['amount'];
                                        $user->save();
                                    } else if($tranz['coin'] == 'ETC') {
                                        $etc_bal = $user->etc;
                                        $user->etc = $etc_bal + $tranz['amount'];
                                        $user->save();
                                    } else if($tranz['coin'] == 'BAT') {
                                        $bat_bat = $user->bat;
                                        $user->bat = $bat_bat + $tranz['amount'];
                                        $user->save();
                                    } else if($tranz['coin'] == 'LTC') {
                                        $ltc_bal = $user->ltc;
                                        $user->ltc = $ltc_bal + $tranz['amount'];
                                        $user->save();
                                    }

                                }
                            }
                        }
                    } catch (Exception $e) {
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                    }
                }
            }
        }

    }
}
