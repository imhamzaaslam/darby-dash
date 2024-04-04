<?php

use App\Models\Country;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('category_id')->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
            $table->string('type')->nullable()->unique();
            $table->string('name')->nullable();
            $table->string('description',1000)->nullable();
        });

        Schema::create('category_country', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('country_id')->constrained();
            $table->timestamps();
            $table->enum('rate', ['S', 'R', 'R2', 'SR', 'Z', 'OS']);
        });

        $this->importCategories();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category_country');
        Schema::dropIfExists('categories');
    }

    private function importCategories(): void
    {
        $json = file_get_contents(base_path() . '/categories.json');
        $data = json_decode($json, true);

        if (empty($data)) {
            \Log::error(sprintf('Error trying to import categories: %s/%s does not exist.', base_path(), 'categories.json'));
            return;
        }

        $subCategories = [];

        foreach ($data['categories'] as $categories) {
            $rootCategoryId = DB::table('categories')->insertGetId([
                'uuid' => str()->uuid(),
                'category_id' => null,
                'type' => null,
                'name' => $categories['name'],
                'description' => $categories['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($categories['subcategories'] as $subCategories) {

                foreach ($subCategories as $key => $subCategory) {
                    $subCategoryId = DB::table('categories')->insertGetId([
                        'uuid' => str()->uuid(),
                        'category_id' => $rootCategoryId,
                        'type' => $key,
                        'name' => $subCategory['name'],
                        'description' => $subCategory['description'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    foreach ($subCategory as  $key2 => $countries) {
                        if ($key2 === 'countries') {
                            foreach ($countries as $countryObject) {
                                $country = Country::where('code', $countryObject['country'])->first();

                                DB::table('category_country')->insert([
                                    'category_id' => $subCategoryId,
                                    'country_id' => $country->id,
                                    'rate' => $countryObject['rate'],
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
};
