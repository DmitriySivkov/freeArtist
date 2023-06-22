<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		foreach (json_decode(file_get_contents(__DIR__ . '/../tags.json')) as $tag) {
			Tag::create(['name' => $tag]);
		}
    }
}
