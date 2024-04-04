<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Meta;

class MetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete the table
        DB::table('meta')->delete();

        // Page metadata
        $pages = [
            [
                'path' => '/',
                'title' => 'Home',
                'description' => '',
                'keywords' => ''
            ],

            [
                'path' => '/blog',
                'title' => 'Blog',
                'description' => '',
                'keywords' => ''
            ],

            [
                'path' => '/contact',
                'title' => 'Contact',
                'description' => '',
                'keywords' => ''
            ]
        ];

        foreach ($pages as $page) {
            Meta::create([
                'path' => $page['path'],
                'title' => $page['title'],
                'description' => $page['description'],
                'keywords' => $page['keywords']
            ]);
        }
    }
}
