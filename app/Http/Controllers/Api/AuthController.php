<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller {
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="API для выбора служебного автомобиля",
     *      description="API метод получения списка доступных текущему пользователю на запланированное время автомобилей с возможностью фильтрации по модели автомобиля, по категории комфорта",
     *      @OA\Contact(
     *          email="masyanov.aleksei@gmail.com"
     *      )
     * )
     * @OA\Post(
     *     path="/api/login",
     *     summary="Авторизация и получение Bearer токена (данные для авторизации подставлены из сидера, нужно просто нажать Execute)",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", example="dir@example.com"),
     *             @OA\Property(property="password", type="string", example="secret")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bearer Token",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="2|CkDCul..."),
     *             @OA\Property(property="bearer_token", type="string", example="Bearer 2|CkDCul...")
     *         )
     *     )
     * )
     */
    public function login( Request $request ) {

        $request->validate( [
            'email'    => 'required|email',
            'password' => 'required'
        ] );

        if ( ! Auth::attempt( $request->only( 'email', 'password' ) ) ) {
            return response()->json( [ 'message' => 'Invalid credentials' ], 401 );
        }

        $user = Auth::user();

        $token = $user->createToken( 'api-token' )->plainTextToken;

        return response()->json( [
            'access_token' => $token,
            'bearer_token' => 'Bearer ' . $token,
        ] );
    }

    public function logout( Request $request ) {
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json( [ 'message' => 'Logged out' ] );
    }
}

