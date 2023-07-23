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
		\DB::table('tags')->truncate();

		$raw = json_decode(file_get_contents(__DIR__ . '/../tags.json'), true);

		$tags = array_unique(
			collect($raw)->reduce(fn($carry, $category) =>
				[...$carry, ...array_values($category)], []
			)
		);

		$tags = array_map(fn($tag) => ['name' => $tag], $tags);

		Tag::insert($tags);
    }
}
