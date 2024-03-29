<?php

use App\Models\Genre;
use App\Models\Category;
use Illuminate\Database\Seeder;

class GenresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = Category::all();
        factory(\App\Models\Genre::class,100)
            ->create()
            ->each(function(Genre $genre) use ($categories){
                $categoriesId = $categories->random(5)->pluck('id')->toArray();
                $genre->categories()->attach($categoriesId);
            });

        // antigo

        // $genres = ['Terror','Comedy','Romance', 'Science Fiction','Drama','Documentary'];
        // foreach ($genres as $key => $value) {
        //     $data = Genre::create([
        //         'name' => $value
        //     ]);
        // }

    }
}
