<?php

// namespace Database\Seeders;

// use Illuminate\Database\Seeder;
// use App\Models\User;
// use Illuminate\Support\Facades\Hash;

// class UserSeeder extends Seeder
// {
//     /**
//      * Run the database seeds.
//      */
//     public function run(): void
//     {
//         // Create admin user
//         User::create([
//             'name' => 'Admin User',
//             'email' => 'admin@projectmanager.com',
//             'password' => Hash::make('password123'),
//             'role' => 'admin',
//             'status' => 'ready',
//             'is_verified' => true,
//             'email_verified_at' => now(),
//         ]);

//         // Create HR user
//         User::create([
//             'name' => 'HR Manager',
//             'email' => 'hr@projectmanager.com',
//             'password' => Hash::make('password123'),
//             'role' => 'hr',
//             'status' => 'ready',
//             'is_verified' => true,
//             'email_verified_at' => now(),
//         ]);

//         // Create sample employees
//         $employees = [
//             [
//                 'name' => 'Diandra Anursa Syabandira',
//                 'email' => 'diandraanursasyabandira@gmail.com',
//                 'phone_number' => '081234567890',
//                 'address' => 'Semarang, Indonesia',
//                 'birthdate' => '1990-05-15',
//             ],
//             [
//                 'name' => 'Jane Smith',
//                 'email' => 'jane@projectmanager.com',
//                 'phone_number' => '081234567891',
//                 'address' => 'Bandung, Indonesia',
//                 'birthdate' => '1992-08-20',
//             ],
//             [
//                 'name' => 'Mike Johnson',
//                 'email' => 'mike@projectmanager.com',
//                 'phone_number' => '081234567892',
//                 'address' => 'Surabaya, Indonesia',
//                 'birthdate' => '1988-12-10',
//             ],
//             [
//                 'name' => 'Sarah Wilson',
//                 'email' => 'sarah@projectmanager.com',
//                 'phone_number' => '081234567893',
//                 'address' => 'Semarang, Indonesia',
//                 'birthdate' => '1995-03-25',
//             ]
//         ];

//         foreach ($employees as $employee) {
//             User::create(array_merge($employee, [
//                 'password' => Hash::make('password123'),
//                 'role' => 'employee',
//                 'status' => 'ready',
//                 'is_verified' => true,
//                 'email_verified_at' => now(),
//             ]));
//         }
//     }
// }