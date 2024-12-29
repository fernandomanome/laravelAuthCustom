<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Atualiza a senha do usuário na tabela 'usuarios'.
     * 
     * Este método substitui o comportamento padrão do método `setUserPassword()` 
     * da trait `ResetsPasswords`, garantindo que a senha do usuário seja 
     * salva no campo `senha` em vez de `password`, que não existe na tabela 
     * `usuarios`. O método aplica o algoritmo de hash para garantir que a 
     * senha seja armazenada de forma segura.
     * 
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user  O objeto do usuário cuja senha será atualizada.
     * @param  string  $password  A nova senha que será atribuída ao usuário.
     * @return void
     */
    protected function setUserPassword($user, $password)
    {
        $user->senha = Hash::make($password);
    }

    /**
     * Local onde serão redirecionados os usuários após a redefinição de senha.
     *
     * @var string
     */
    protected $redirectTo = '/home';
}
