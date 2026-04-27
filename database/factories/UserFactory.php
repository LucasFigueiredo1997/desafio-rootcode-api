<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name'                       => fake()->name(),
            'email'                      => fake()->unique()->safeEmail(),
            'role'                       => fake()->randomElement(['gestor', 'colaborador']), // Escolhe aleatoriamente entre gestor e colaborador
            'email_verified_at'          => now(),
            'password'                   => static::$password ??= Hash::make('password123'), // Senha padrão para todos os usuários de teste
            'remember_token'             => Str::random(10),
            'two_factor_secret'          => null,
            'two_factor_recovery_codes'  => null,
            'two_factor_confirmed_at'    => null,
        ];
    }

    // Estado especial: cria usuário com e-mail não verificado
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    // Estado especial: cria usuário com dois fatores de autenticação configurados
    public function withTwoFactor(): static
    {
        return $this->state(fn (array $attributes) => [
            'two_factor_secret'          => encrypt('secret'),
            'two_factor_recovery_codes'  => encrypt(json_encode(['recovery-code-1'])),
            'two_factor_confirmed_at'    => now(),
        ]);
    }
}