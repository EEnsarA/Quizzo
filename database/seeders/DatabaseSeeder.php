<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(10)->create();

        $testUser = User::factory()->create([
            'name' => 'Ensar',
            'email' => 'ensar@gmail.com',
            'password' => bcrypt('ensar123'),
        ]);

        Quiz::factory(5)
            ->for($testUser)
            ->has(
                Question::factory(5)
                    ->has(
                        Answer::factory(4)
                            ->state(new Sequence(
                                ["is_correct" => true],
                                ["is_correct" => false],
                                ["is_correct" => false],
                                ["is_correct" => false]
                            ))
                    )
            )
            ->create();
        Quiz::factory(5)
            ->recycle($users)  // QuizFactorydeki user_id => User::factory() çağrısını mevcut $users içerisinden karşılar
            ->has(
                Question::factory(5)
                    ->has(
                        Answer::factory(4)
                            ->state(new Sequence(
                                ["is_correct" => true],
                                ["is_correct" => false],
                                ["is_correct" => false],
                                ["is_correct" => false]
                            ))
                    )
            )
            ->create();
    }
}
