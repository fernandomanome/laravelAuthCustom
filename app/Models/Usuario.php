<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Obtem o nome do atributo de password para o usuário.
     * Este método sobreescreve o métdodo getAuthPasswordName() da Classe Authenticatable
     * @return string
     */
    public function getAuthPasswordName()
    {
        return $this->senha;
    }

    /**
     * Os atributos do Model que são atribuíveis em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nome',
        'email',
        'senha',
    ];

    /**
     * Os atributos que devem estar ocultos para serialização.
     *
     * @var list<string>
     */
    protected $hidden = [
        'senha',
        'remember_token',
    ];

    /**
     * Obtendo os atributos que devem ser cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'senha' => 'hashed',
        ];
    }
}
