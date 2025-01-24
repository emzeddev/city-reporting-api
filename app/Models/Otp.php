<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $table = 'otps';


    protected $fillable = [
        "mobile",
        "user_id",
        "otp",
        "expire_at",
        "created_at",
        "updated_at"
    ];

    protected $casts = [
        'expire_at' => 'datetime'
    ];


    public function sendOTP($receiverNumber)
    {
        try {
            $phone = $receiverNumber;
            $randCode = $this->otp;
            ini_set("soap.wsdl_cache_enabled", "0");

            $client = new \SoapClient("https://ippanel.com/class/sms/wsdlservice/server.php?wsdl");
            $user = config('app.payamak_user');
            $pass = config('app.payamak_pass');
            $fromNum = config('app.payamak_from');
            $toNum = array($phone);
            $pattern_code = "hdsxp8rpsuerefo";
            $input_data = array("verification-code" => $randCode);
            $client->sendPatternSms($fromNum, $toNum, $user, $pass, $pattern_code, $input_data);


        } catch (Exception $e) {
            info("Error: ". $e->getMessage());
        }
    }
}
