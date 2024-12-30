<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class UsuarioHelper
{
	/**
	 * Verifica se o cadastro do usuário atende as características, caso contrário, realiza o logout.
	 *
	 * @param  \Illuminate\Http\Request  $request  A requisição HTTP.
	 * @param  mixed  $user  O usuário autenticado.
	 * @return bool Retorna se o cadastro está válido ou não.
	 * 
	 * @version 1.0
	 * @author Fernando
	 * @access public
	 */
	public static function validarCadastroUsuario($request, $user): bool
	{
		// Verifica se o usuário está ativo
		if (!self::validacoesCadastroUsuario($user)) {
			// Se o usuário não estiver ativo, realiza o logout e invalida a sessão
			Auth::logout();
			$request->session()->invalidate();
			// Gera um novo token CSRF por segurança
			$request->session()->regenerateToken();
			return false;
		}

		return true;
	}

	/**
	 * Realiza a validação do cadastro do usuário. 
	 * **[Atualmente somente verifica se está ativo]**
	 * 
	 * @param  mixed  $user  O usuário autenticado.
	 * @return bool Retorna se o cadastro está válido.
	 * 
	 * @version 1.0
	 * @author Fernando
	 * @access private
	 */
	private static function validacoesCadastroUsuario($user): bool
	{
		// Regras da validação do cadastro
		// Atualmente só verifica se está ativo
		return isset($user->ativo) && $user->ativo === true;
	}
}
