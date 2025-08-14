<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarCategory;
use App\Models\Driver;
use App\Models\Position;
use App\Models\User;
use App\Models\Car;
use App\Models\Trip;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Facades\DB;

class DemoSeeder extends Seeder {
    public function run() {

        DB::statement( 'SET FOREIGN_KEY_CHECKS=0;' );

        DB::table( 'trips' )->truncate();
        DB::table( 'cars' )->truncate();
        DB::table( 'users' )->truncate();
        DB::table( 'drivers' )->truncate();
        DB::table( 'positions' )->truncate();
        DB::table( 'car_categories' )->truncate();
        DB::table( 'position_car_category' )->truncate();

        DB::statement( 'SET FOREIGN_KEY_CHECKS=1;' );

        $cat1 = CarCategory::create( [ 'name' => 'Первая' ] );
        $cat2 = CarCategory::create( [ 'name' => 'Вторая' ] );
        $cat3 = CarCategory::create( [ 'name' => 'Третья' ] );

        $dir = Position::create( [ 'name' => 'Директор' ] );
        $man = Position::create( [ 'name' => 'Менеджер' ] );

        $dir->carCategories()->sync( [ $cat1->id, $cat2->id ] );
        $man->carCategories()->sync( [ $cat2->id, $cat3->id ] );

        $d1 = Driver::create( [ 'full_name' => 'Иван Иванов' ] );
        $d2 = Driver::create( [ 'full_name' => 'Петр Петров' ] );

        $carCategoryIds = CarCategory::pluck( 'id' )->toArray();
        $driverIds      = Driver::pluck( 'id' )->toArray();

        $faker = FakerFactory::create();

        $firstCar = null;
        for ( $i = 0; $i < 20; $i ++ ) {
            $car = Car::create( [
                'model'           => $faker->randomElement( [
                        'Toyota Camry',
                        'Honda Civic',
                        'Ford Focus',
                        'Hyundai Solaris',
                        'Kia Rio',
                        'Volkswagen Polo',
                        'Renault Logan',
                        'Nissan Qashqai',
                        'Skoda Octavia',
                        'Mazda 3',
                        'Lada Vesta',
                        'Chevrolet Cruze'
                    ] ) . ' ' . $faker->bothify( '?#?#' ),
                'car_category_id' => $faker->randomElement( $carCategoryIds ),
                'driver_id'       => $faker->randomElement( $driverIds ),
            ] );

            if ( $i === 0 ) {
                $firstCar = $car;
            }
        }


        $userDir = User::updateOrCreate(
            [ 'email' => 'dir@example.com' ],
            [
                'name'        => 'Test Direktor',
                'password'    => bcrypt( 'secret' ),
                'position_id' => $dir->id
            ]
        );
        $userMan = User::updateOrCreate(
            [ 'email' => 'man@example.com' ],
            [
                'name'        => 'Test Manager',
                'password'    => bcrypt( 'secret' ),
                'position_id' => $man->id
            ]
        );

        Trip::create( [
            'user_id'    => $userDir->id,
            'car_id'     => $firstCar->id,
            'start_time' => '2025-08-13 09:00:00',
            'end_time'   => '2025-08-13 18:00:00',
        ] );
    }
}
