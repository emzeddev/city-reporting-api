<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Otp;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use \App\Http\Middleware\AuthTokenMiddleware;

class AuthController extends Controller implements HasMiddleware
{


    public static function middleware(): array
    {
        return [
            new Middleware(AuthTokenMiddleware::class, ['except' => ['sendCode' , 'signIn', 'signUp']])
        ];
    }


    public function get_user(Request $request){
        return response()->json(auth()->guard('api')->user());
    }

    public function logout(Request $request){
        auth()->guard("api")->logout();
        return response()->json([
            'status' => 200 ,
            'message' => 'خروج از حساب با موقیت انجام گردید',
            'result' => null
        ]);
    }

    public function sendCode(Request $request) {

        $vlidateRequest = $this->coreValidator($request , [
            'mobile' => 'required|numeric'
        ]);

        if(!$vlidateRequest){
            return response()->json([
                'status' => 500 ,
                'message' => 'خطای اعتبارسنجی اطلاعات ، لطفا با دقت همه فیلد هارا پر کنید' ,
                "result" => null
            ]);
        }


        /* Generate An OTP */
        $userOtp = $this->generateOtp($request);
        $userOtp->sendOTP($request->mobile);

        return response()->json([
            'status' => 200 ,
            'message' => 'کد تایید با موفقیت به شماره همراه شما ارسال گردید',
            'mobile' => $request->mobile,
        ]);
    }

    public function generateOtp($request){
        /* User Does not Have Any Existing OTP */
        $userOtp = Otp::where('mobile', $request->mobile)->latest()->first();

        // return $userOtp;
        $now = now();

        if($userOtp && $now->isBefore($userOtp->expire_at)){
            return $userOtp;
        }

        /* Create a New OTP */
        return Otp::create([
            'mobile' => $request->mobile,
            'otp' => rand(123456, 999999),
            'expire_at' => $now->addMinutes(2)
        ]);
    }

    public function signUp(Request $request) {
        $validator = $this->coreValidator($request , [
            "name" => "required|string",
            'otp' => 'required',
            'mobile' => 'required|numeric|digits:11'
        ]);

        if(!$validator){
            return response()->json([
                'status' => 400,
                'message' => "خطا در اعتبارسنجی فرم !",
                'result' => null
            ]);
        }

        $findUserWithMobile = User::where("mobile" , $request->mobile)->first();
        if($findUserWithMobile instanceof User){



            return response()->json([
                "status" => 500,
                "message" => "این شماره همراه قبلا در سایت ثبت نام کرده !",
                "result" => null
            ]);

        }else{
            return $this->registerUser($request);
        }
    }

    public function registerUser(Request $request) {
        $userOtp = Otp::where("mobile" , $request->mobile)
        ->where("otp" , (int)$request->otp)
        ->first();
        $now = now();

        if(!$userOtp){
            return response()->json([
                'status' => 100,
                'message' => 'کد تایید وارد شده معتبر نمیباشد',
                'result' => null
            ]);
        }else if($userOtp && $now->isAfter($userOtp->expire_at)){
            return response()->json([
                'status' => 101 ,
                'message' => 'کد تایید وارد شده منقضی شده است',
                'result' => null
            ]);
        }



        $userOtp->update([
            'expire_at' => now()
        ]);

        $data = [
            "name" => $request->name,
            "mobile" => $request->mobile
        ];


        $createUser = User::create($data);
        if($createUser instanceof User){
            $token = auth()->guard("api")->login($createUser);


            return response()->json([
                "status" => 200,
                "message" => "success",
                "result" => null,
                "access_token" => $token
            ]);

        }else {
            return response()->json([
                "status" => 500,
                "message" => "خطا در ثبت اطلاعات حامی !",
                "result" => null
            ]);
        }

    }

    public function signIn(Request $request) {

        $validator = $this->coreValidator($request , [
            'otp' => 'required',
            'mobile' => 'required|numeric|digits:11'
        ]);

        if(!$validator){
            return response()->json([
                'status' => 400,
                'message' => "خطا در اعتبارسنجی فرم !",
                'result' => null
            ]);
        }


        $findUserWithMobile = User::where("mobile" , $request->mobile)->first();
        if($findUserWithMobile instanceof User){

            $userOtp = Otp::where("mobile" , $request->mobile)
            ->where("otp" , (int)$request->otp)->first();
            $now = now();

            if(!$userOtp){
                return response()->json([
                    'status' => 100,
                    'message' => 'کد تایید وارد شده معتبر نمیباشد',
                    'result' => null
                ]);
            }else if($userOtp && $now->isAfter($userOtp->expire_at)){
                return response()->json([
                    'status' => 101 ,
                    'message' => 'کد تایید وارد شده منقضی شده است',
                    'result' => null
                ]);
            }


            $userOtp->update([
                'expire_at' => now()
            ]);


            $token = auth()->guard("api")->login($findUserWithMobile);
            return response()->json([
                "status" => 200,
                "message" => "success",
                "result" => $findUserWithMobile,
                "access_token" => $token
            ]);


        }else{
            return response()->json([
                "status" => 404,
                "message" => "ابتدا با این شماره همراه ثبت نام کنید سپس دوباره امتحان کنید",
                "result" => null
            ]);
        }

    }



}
