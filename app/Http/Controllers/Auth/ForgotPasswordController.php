<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\UsuarioHelper;
use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use stdClass;

class ForgotPasswordController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Password Reset Controller
	|--------------------------------------------------------------------------
	|
	| Este controlador é responsável por lidar com os emails de redefinição de senha e
	| inclui um trait que ajuda no envio dessas notificações aos seus usuários.
	|
	*/

	use SendsPasswordResetEmails;

	/**
	 * Valida o email da requisição, verificando se o email existe na tabela 'usuarios' 
	 * e se o usuário está ativo. Caso o email seja válido, retorna o usuário, 
	 * caso contrário, valida e retorna uma mensagem de erro.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return mixed Retorna o usuário se válido, ou uma falha na validação do email.
	 */
	protected function validateEmailCustomForgot(Request $request)
	{
		// Inicializa a variável que armazenará o usuário validado.
		$user = null;

		// Validação do email:
		// - Verifica se o valor não está vazio
		// - Verifica se o valor é uma `string` no formato válido de email
		// - Verifica se existe na tabela 'usuarios' e está 'ativo'
		$request->validate([
			'email' => [
				'required',
				'email',
				function ($attribute, $value, $fail) use (&$user) {
					// Verifica se o email está registrado e se o usuário está ativo
					$user = Usuario::where('email', $value)->where('ativo', true)->first();

					// Se o usuário não for encontrado ou estiver inativo, cria a falha da validação.
					if (!$user) {
						$fail('O email fornecido não está registrado ou está inativo.');
					}
				},
			],
		]);

		// Retorna o usuário ou null em caso de falha.
		return $user;
	}


	/**
	 * Envia um link para redefinição de senha para o usuário.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
	 */
	public function sendResetLinkEmail(Request $request)
	{
		// Inicializa a resposta
		$response = "passwords.invalid";

		// Valida o email da requisição
		$user = $this->validateEmailCustomForgot($request);

		// Verifica se o acesso do usuário é válido
		if ($user && UsuarioHelper::validarCadastroUsuario($request, $user)) {
			// Envia o link para redefinição de senha
			$response = $this->broker()->sendResetLink(
				$this->credentials($request)
			);
		}

		// Retorna a resposta de sucesso ou falha ao enviar o link de redefinição
		return $response == Password::RESET_LINK_SENT
			? $this->sendResetLinkResponse($request, $response)
			: $this->sendResetLinkFailedResponse($request, $response);
	}
}
