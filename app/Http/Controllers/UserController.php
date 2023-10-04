<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $secretWord = 'newacc!(@%#&$$66!)@%!@66)#'; // Defina sua palavra secreta aqui

        $request->validate([
            'name' => 'required|string|max:255|unique:users',
            'secret_word' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verifique se a palavra secreta está correta
        if ($request->secret_word !== $secretWord) {
            return response(['message' => 'Palavra secreta incorreta'], 403); // 403 Forbidden
        }

        $user = User::create([
            'name' => $request->name,
            'password' => Hash::make($request->password),
        ]);

        return response(['user' => $user, 'message' => 'Usuário registrado com sucesso'], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required|string', // mude de email para name
            'password' => 'required|string',
        ]);

        // Use o campo de nome para a autenticação
        $credentials = $request->only(['name', 'password']);

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'name' => ['As credenciais fornecidas estão incorretas.'], // mude a chave e a mensagem de erro
            ]);
        }

        return response([
            'user' => auth()->user(),
            'token' => auth()->user()->createToken('API Token')->plainTextToken
        ]);
    }

    public function logout()
    {
        try {
            // Invalidar todos os tokens para o usuário
            Auth::user()->tokens->each(function ($token) {
                $token->delete();
            });

            return response()->json(['message' => 'Logout bem-sucedido!']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro no logout: ' . $e->getMessage()], 400);
        }
    }
}
