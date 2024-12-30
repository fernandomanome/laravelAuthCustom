<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\AlteracaoSenhaNotification;
use App\Notifications\VerificarEmailNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable implements MustVerifyEmail
{
	/** @use HasFactory<\Database\Factories\UserFactory> */
	use HasFactory, Notifiable;

	/**
	 * Obtem o nome do atributo de password para o usuário.
	 * Este método sobreescreve o métdodo getAuthPasswordName() da Classe Authenticatable
	 * @return string
	 */
	public function getAuthPassword()
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

	/**
	 * **[!ATENÇÃO!]** Este método substitui o método `sendPasswordResetNotification()` 
	 * da classe `CanResetPassword` para permitir a customização do conteúdo do e-mail 
	 * enviado para a recuperação de senha. Ao invés de utilizar a notificação padrão do Laravel, 
	 * ele envia um e-mail personalizado com as informações de recuperação de senha.
	 *
	 * @param  string  $token  O token gerado para a recuperação de senha do usuário.
	 * @return void
	 * 
	 * @see \Illuminate\Auth\Passwords\CanResetPassword::sendPasswordResetNotification
	 * @version 1.0
	 * @author Fernando
	 * @access public
	 */
	public function sendPasswordResetNotification(#[\SensitiveParameter] $token)
	{
		// Validando o token para garantir que ele não seja vazio ou nulo.
		if (empty($token)) {
			throw new \InvalidArgumentException(
				'Token de redefinição de senha inválido ao enviar link de recuperação.'
			);
		}

		// Criando a notificação personalizada passando o token, email e nome do usuário.
		$templateNotification = new AlteracaoSenhaNotification($token, $this->email, $this->nome);

		// Enviando a notificação personalizada para o email do usuário.
		$this->notify($templateNotification);
	}


	/**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify( new VerificarEmailNotification($this->nome));
    }
}
