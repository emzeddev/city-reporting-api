<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Http\JsonResponse;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class AuthTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        try {
            // اعتبارسنجی توکن
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            return new JsonResponse([
                'status' => 401,
                'message' => "توکن شما منقضی شده . دوباره وارد حساب خود شوید",
            ], JsonResponse::HTTP_UNAUTHORIZED , [], JSON_UNESCAPED_UNICODE);
        } catch (TokenInvalidException $e) {
            return new JsonResponse([
                'status' => 401,
                'message' => "توکن نامعتبر هست",
            ],  JsonResponse::HTTP_UNAUTHORIZED , [], JSON_UNESCAPED_UNICODE);
        } catch (JWTException $e) {
            return new JsonResponse([
                'status' => 401,
                'message' => "ارسال توکن الزامی میباشد",
            ],  JsonResponse::HTTP_UNAUTHORIZED , [], JSON_UNESCAPED_UNICODE);
        }

        return $next($request);
    }
}