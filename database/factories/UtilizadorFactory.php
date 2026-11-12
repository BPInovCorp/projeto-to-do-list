<?php

namespace Database\Factories;

use App\Models\Utilizador;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UtilizadorFactory extends Factory
{
    protected $model = Utilizador::class;

    protected static ?string $password;

    public function definition(): array
    {
        static $indice = 1;
        $numero = $indice++;

        return [
            'nome' => 'Utilizador Exemplo ' . $numero,
            'email' => 'utilizador' . $numero . '@example.com',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}