<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\UsuarioHelper;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | Este é o controlador reponsável por lidar com a autenticação dos usuários e
    | os redireciona para telas conforme a necessidade. O controlador utiliza um trait
    | para fornecer funcionalidade de autenticação de forma mais conveniente.
    |
    */

	/* Invoca a Trait disponibilizando as funcionalidades da lib de Login */
    use AuthenticatesUsers;

    /**
     * Onde os usuários serão redirecionados após o login bem-sucedido.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Cria uma nova instância do controlador.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Método executado após a autenticação bem-sucedida do usuário com email e senha.
     * 
     * [!ATENÇÃO!] Este método pertence à Trait `AuthenticatesUsers` e está sendo sobrescrito 
     * para permitir a execução de validações adicionais durante o processo de login,
     * como verificações extras de permissões ou condições específicas do usuário.
     * O objetivo é fornecer um controle mais detalhado sobre o fluxo pós-login,
     * garantindo que o cadastro do usuário atenda a requisitos adicionais antes de prosseguir.
     * 
     * @param  \Illuminate\Http\Request  $request  A requisição HTTP com dados do login.
     * @param  mixed  $user  O usuário autenticado, que pode ser um modelo de usuário ou instância personalizada.
     * @return \Illuminate\Http\RedirectResponse  O redirecionamento após validações.
     */
    protected function authenticated(Request $request, $user)
    {
        // Realiza validações adicionais do cadastro do usuário        
		if(!UsuarioHelper::validarCadastroUsuario($request, $user)) {
			// Se não for válido redireciona o usuário
			return redirect()->route('login')->withErrors(['email' => __('validation.invalid_account', ['attribute' => $user->email])]);
		}
        // Se o usuário estiver ativo, continua o fluxo de login
        return redirect()->intended($this->redirectTo);
    }
}
