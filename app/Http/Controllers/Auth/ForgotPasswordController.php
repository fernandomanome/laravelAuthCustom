<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\UsuarioHelper;
use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

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
	 * Valida o email da requisição.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return void
	 */
	protected function validateEmail(Request $request)
	{
		// Valida o email, verifica se existe na tabela 'usuarios' e se o campo 'ativo' é verdadeiro
		$request->validate([
			'email' => [
				'required',
				'email',
				function ($attribute, $value, $fail) {
					// Verifica se o email está na tabela 'usuarios' e se o usuário está ativo
					$user = Usuario::where('email', $value)->where('ativo', true)->first();
					if (!$user) {
						$fail('O email fornecido não está registrado ou está inativo.');
					}
				},
			],
		]);
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
		$this->validateEmail($request);

		// Consulta os dados do usuário
		$user = Usuario::where('email', $request->email)->where('ativo', true)->first();

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
