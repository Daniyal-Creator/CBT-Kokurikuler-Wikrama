<?php

namespace Database\Factories;

use App\Enums\PeranPengguna;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'peran' => PeranPengguna::Guru,
            'disetujui_pada' => now(),
        ];
    }

    /**
     * Akun yang mendaftar sendiri dan belum disetujui Admin.
     */
    public function menungguPersetujuan(): static
    {
        return $this->state(fn (array $attributes) => ['disetujui_pada' => null]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => ['peran' => PeranPengguna::Admin]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
