<?php

namespace Database\Seeders;

use App\Models\SecurityQuestion;
use Illuminate\Database\Seeder;

class SecurityQuestionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SecurityQuestion::create(['question' => 'What is your mother\'s maiden name?']);
        SecurityQuestion::create(['question' => 'What was the name of your first pet?']);
        SecurityQuestion::create(['question' => 'What was the name of your uncle?']);
        SecurityQuestion::create(['question' => 'What is Recursion?']);
    }
}
