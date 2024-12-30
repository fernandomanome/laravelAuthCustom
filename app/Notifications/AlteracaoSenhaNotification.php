<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AlteracaoSenhaNotification extends Notification
{
	use Queueable;

	// Definição das variáveis da classe //
	protected string $token;
	protected string $email;
	protected string $nome;
	protected string $url;
	protected string $tempoExpiracao;
	protected string $saudacaoUsuario;

	/**
	 * Crie uma nova instância do objeto notification.
	 */
	public function __construct(string $token, string $email, string $nome)
	{
        $this->token = $token;
        $this->email = $email;
        $this->nome = $nome;
        $this->tempoExpiracao = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');
        $this->saudacaoUsuario = 'Olá ' . $this->nome;
        $this->url = env('APP_URL') . 'password/reset/' . $this->token . '?email=' . $this->email;
	}

	/**
	 * Obtem os canais de entrega da notificação.
	 *
	 * @return array<int, string>
	 */
	public function via(object $notifiable): array
	{
		return ['mail'];
	}

	/**
	 * Obtendo o conteúdo do email da notificação.
	 */
	public function toMail(object $notifiable): MailMessage
	{
		return (new MailMessage)
			->subject('Atualização de senha')
			->greeting($this->saudacaoUsuario ?? 'Olá!')
			->line('Você está recebendo este email porque recebemos uma solicitação de redefinição de senha para sua conta.')
			->action('Redefinir senha', $this->url)
			->line('Este link de redefinição de senha expirará em ' . $this->tempoExpiracao . ' minutos.')
			->line('Se você não solicitou uma redefinição de senha, nenhuma ação é necessária.')
			->salutation('Até breve!');
	}

	/**
	 * Obtem o array representativo da notificação.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(object $notifiable): array
	{
		return [
			//
		];
	}
}
