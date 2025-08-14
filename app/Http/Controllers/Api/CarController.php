<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Bearer token (токен из /login)",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth",
 * )
 */
class CarController extends Controller {
    /**
     * @OA\Get(
     *     path="/api/cars/available",
     *     summary="Получить доступные автомобили по фильтрам и времени",
     *     description="Возвращает список автомобилей, доступных для поездок в указанный промежуток времени по заданным фильтрам",
     *     operationId="getAvailableCars",
     *     tags={"Cars"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="model",
     *         in="query",
     *         description="Фильтр по модели автомобиля",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Фильтр по категории автомобиля (id категории)",
     *         required=false,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Parameter(
     *         name="start_time",
     *         in="query",
     *         description="Начало периода доступности автомобиля (формат: YYYY-MM-DD HH:MM:SS)",
     *         required=true,
     *         @OA\Schema(type="string", format="date-time", example="2025-08-13 09:00:00")
     *     ),
     *     @OA\Parameter(
     *         name="end_time",
     *         in="query",
     *         description="Конец периода доступности автомобиля (формат: YYYY-MM-DD HH:MM:SS)",
     *         required=true,
     *         @OA\Schema(type="string", format="date-time", example="2025-08-13 18:00:00")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Неавторизованный пользователь"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации параметров запроса"
     *     )
     * )
     */
    public function available( Request $request ) {
        $user = Auth::user();

        $categories = $user->position
            ? $user->position->carCategories()->pluck( 'car_categories.id' )->toArray()
            : [];

        if ( empty( $categories ) ) {
            return response()->json( [
                'position_id'          => $user->position ? $user->position->id : null,
                'available_categories' => [],
                'cars'                 => [],
            ] );
        }

        $query = Car::with( 'driver', 'carCategory' )
                    ->whereIn( 'car_category_id', $categories );

        if ( $request->filled( 'model' ) ) {
            $query->where( 'model', 'like', '%' . $request->model . '%' );
        }
        if ( $request->filled( 'category_id' ) ) {
            $query->where( 'car_category_id', $request->category_id );
        }

        $cars = $query->whereDoesntHave( 'trips', function ( $q ) use ( $request ) {
            $q->where( function ( $q2 ) use ( $request ) {
                $q2->whereBetween( 'start_time', [ $request->start_time, $request->end_time ] )
                   ->orWhereBetween( 'end_time', [ $request->start_time, $request->end_time ] )
                   ->orWhere( function ( $q3 ) use ( $request ) {
                       $q3->where( 'start_time', '<', $request->start_time )
                          ->where( 'end_time', '>', $request->end_time );
                   } );
            } );
        } )->get();

        return response()->json( [
            'position_id'          => $user->position ? $user->position->id : null,
            'available_categories' => $categories,
            'cars'                 => $cars,
        ] );
    }

    /**
     * @OA\Get(
     *     path="/api/cars/booked",
     *     summary="Получить забронированные автомобили по времени",
     *     description="Возвращает список автомобилей, которые заняты (забронированы) хотя бы на часть заданного периода и указывает, кто их забронировал",
     *     operationId="getBookedCars",
     *     tags={"Cars"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="start_time",
     *         in="query",
     *         description="Начало периода запроса (формат: YYYY-MM-DD HH:MM:SS)",
     *         required=true,
     *         @OA\Schema(type="string", format="date-time", example="2025-08-13 09:00:00")
     *     ),
     *     @OA\Parameter(
     *         name="end_time",
     *         in="query",
     *         description="Конец периода запроса (формат: YYYY-MM-DD HH:MM:SS)",
     *         required=true,
     *         @OA\Schema(type="string", format="date-time", example="2025-08-13 18:00:00")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список забронированных автомобилей",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="cars", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=3, description="ID автомобиля"),
     *                     @OA\Property(property="model", type="string", example="Ford Focus", description="Модель автомобиля"),
     *                     @OA\Property(property="car_category_id", type="integer", example=1, description="ID категории автомобиля"),
     *                     @OA\Property(property="driver", type="object",
     *                         @OA\Property(property="id", type="integer", example=6),
     *                         @OA\Property(property="name", type="string", example="Петр Петров")
     *                     ),
     *                     @OA\Property(property="carCategory", type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Седан")
     *                     ),
     *                     @OA\Property(property="trips", type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=15),
     *                             @OA\Property(property="start_time", type="string", example="2025-08-13 12:00:00"),
     *                             @OA\Property(property="end_time", type="string", example="2025-08-13 14:00:00"),
     *                             @OA\Property(property="user", type="object",
     *                                 @OA\Property(property="id", type="integer", example=9),
     *                                 @OA\Property(property="name", type="string", example="Анна Смирнова"),
     *                                 @OA\Property(property="email", type="string", example="anna@example.com")
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Неавторизованный пользователь"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации параметров запроса"
     *     )
     * )
     */

    public function booked( Request $request ) {
        $user = Auth::user();

        $query = Car::with( [
            'driver',
            'carCategory',
            'trips' => function ( $q ) use ( $request ) {
                $q->where( function ( $q2 ) use ( $request ) {
                    $q2->whereBetween( 'start_time', [ $request->start_time, $request->end_time ] )
                       ->orWhereBetween( 'end_time', [ $request->start_time, $request->end_time ] )
                       ->orWhere( function ( $q3 ) use ( $request ) {
                           $q3->where( 'start_time', '<', $request->start_time )
                              ->where( 'end_time', '>', $request->end_time );
                       } );
                } );
            }
        ] );

        $cars = $query->whereHas( 'trips', function ( $q ) use ( $request ) {
            $q->where( function ( $q2 ) use ( $request ) {
                $q2->whereBetween( 'start_time', [ $request->start_time, $request->end_time ] )
                   ->orWhereBetween( 'end_time', [ $request->start_time, $request->end_time ] )
                   ->orWhere( function ( $q3 ) use ( $request ) {
                       $q3->where( 'start_time', '<', $request->start_time )
                          ->where( 'end_time', '>', $request->end_time );
                   } );
            } );
        } )->get();

        return response()->json( [
            'cars' => $cars,
        ] );
    }
}
