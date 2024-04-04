<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Credential;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\VatNumber;
use Faker\Generator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param Generator $faker
     * @return void
     */
    public function run(Generator $faker): void
    {
         $demoUser = User::create([
             'uuid'              => $faker->uuid,
             'name_first'        => $faker->firstName,
             'name_last'         => $faker->lastName,
             'email'             => 'demo@demo.com',
             'password'          => Hash::make('password'),
             'email_verified_at' => now(),
             'state'             => 'active',
         ]);

         $this->addDummyInfo($faker, $demoUser);

         $demoUser2 = User::create([
             'uuid'              => $faker->uuid,
             'name_first'        => $faker->firstName,
             'name_last'         => $faker->lastName,
             'email'             => 'admin@demo.com',
             'password'          => Hash::make('password'),
             'email_verified_at' => now(),
             'state'             => 'active',
         ]);

         $this->addDummyInfo($faker, $demoUser2);

        User::factory(100)->create()->each(function (User $user) use ($faker) {
            $this->addDummyInfo($faker, $user);
        });

        VatNumber::create([
            'user_id' => $demoUser->id,
            'country_id' => 22,
            'number' => $faker->phoneNumber
        ]);

        VatNumber::create([
            'user_id' => $demoUser->id,
            'country_id' => 12,
            'number' => $faker->phoneNumber
        ]);
    }

    private function addDummyInfo(Generator $faker, User $user): void
    {
        $dummyInfo = [
            'company'  => $faker->company,
            'phone'    => $faker->phoneNumber,
            'website'  => $faker->url,
            'communication_language' => $faker->languageCode,
            'country'  => $faker->countryCode,
        ];

        $info = new UserInfo();

        foreach ($dummyInfo as $key => $value) {
            $info->$key = $value;
        }

        $info->user()->associate($user);
        $info->save();
    }
}
