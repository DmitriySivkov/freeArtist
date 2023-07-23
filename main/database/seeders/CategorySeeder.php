<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		// seed categories & sync with tags
		\DB::table('categories')->truncate();

		$raw = json_decode(file_get_contents(__DIR__ . '/../tags.json'), true);

		foreach ($raw as $cat => $tags) {
			$category = Category::create(['name' => $cat]);

			$categoryTags = Tag::whereIn('name', $tags)->get();

			$category->tags()->sync($categoryTags->pluck('id'));
		}
    }
}
