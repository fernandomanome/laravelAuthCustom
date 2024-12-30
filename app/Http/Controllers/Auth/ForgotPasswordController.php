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
	 * Substitui o método `validateEmail()` da Trait `SendsPasswordResetEmails`, utilizado no processo de envio de redefinição de senha.
	 * 
	 * Este método valida o email fornecido na requisição, verificando se ele existe na tabela `usuarios` e se o usuário está ativo.
	 * Caso o email seja válido, o usuário é retornado. Caso contrário, uma mensagem de erro é gerada, indicando que o email não está registrado ou está inativo.
	 * 
	 * @param  \Illuminate\Http\Request  $request  A requisição HTTP contendo o email a ser validado.
	 * @return mixed Retorna o usuário caso o email seja válido e o usuário esteja ativo; ou, em caso de falha, retorna uma mensagem de erro.
	 * 
	 * @see \Illuminate\Foundation\Auth\SendsPasswordResetEmails::sendResetLinkEmail
	 * @version 1.0
	 * @author Fernando
	 * @access protected
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
					$user = Usuario::where('email', $value)->first();

					// Se o usuário não for encontrado ou estiver inativo, cria a falha da validação.
					if (!$user) {
						// Utiliza a chave de tradução para exibir a mensagem de erro
						$fail(__('validation.invalid_account', ['attribute' => $value]));
					}
				},
			],
		]);

		// Retorna o usuário ou null em caso de falha.
		return $user;
	}

	/**
	 * **[!ATENÇÃO!]** Este método pertence à Trait `AuthenticatesUsers` e está sendo sobrescrito 
	 * para permitir a execução das validações customizadas a partir do método `validateEmailCustomForgot()`
	 * Envia um link para redefinição de senha para o usuário.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
	 *
	 * @see \Illuminate\Foundation\Auth\SendsPasswordResetEmails::sendResetLinkEmail
	 * @version 1.0
	 * @author Fernando
	 * @access public
	 */
	public function sendResetLinkEmail(Request $request)
	{
		// Inicializa a resposta
		$response = __('validation.invalid_account', ['attribute' => $request->email]);

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
