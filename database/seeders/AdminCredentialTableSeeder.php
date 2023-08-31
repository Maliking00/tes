<?php

namespace Database\Seeders;

use App\Models\SecurityQuestionAndAnswer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class AdminCredentialTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => config('app.adminCredentials.name'),
            'email' => config('app.adminCredentials.email'),
            'password' => Hash::make(config('app.adminCredentials.password')),
            'idNumber' => config('app.adminCredentials.idNumber'),
            'contactNumber' => config('app.adminCredentials.contactNumber'),
            'securityAnswer' => Crypt::encrypt(config('app.adminCredentials.securityAnswer')),
            'role' => 'admin',
            'status' => 'approved'
        ]);

        $userId = User::where('email', config('app.adminCredentials.email'))->value('id');

        SecurityQuestionAndAnswer::create([
            'user_id' => $userId,
            'question' => 'What is Recursion?',
            'answer' => config('app.adminCredentials.securityAnswer')
        ]);
    }
}
