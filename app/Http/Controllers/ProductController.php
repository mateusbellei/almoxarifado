<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Usuário não autenticado'], 401);
        }

        $query = Product::where('user_id', $request->user()->id); // Produtos do usuário autenticado

        if ($request->has('name')) {
            $query->where('produto', 'like', '%' . $request->name . '%');
        }

        if ($request->has('sort')) {
            $query->orderBy($request->get('sort'), $request->get('order', 'asc')); // Ordenação dinâmica
        }

        $produtos = $query->paginate($request->get('perPage', 30));

        return response([
            'produtos' => $produtos,
            'user_name' => $request->user()->name, // Incluído o nome do usuário autenticado
        ]);
    }

    public function showById(Product $product)
    {
        return response(['produto' => $product]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'produto' => 'required|string|max:255|unique:products',
            'unidade_medida' => 'required|in:Unidade,Pacote,Rolo,Caixa,Bloco,Maço,Metro,Frasco,Tubo,Galão',
            'estoque' => 'required|numeric',
            'validade' => 'required|date',
        ]);

        $product = Product::create([
            'user_id' => $request->user()->id, // Associa ao usuário autenticado
            'produto' => $request->produto,
            'unidade_medida' => $request->unidade_medida,
            'estoque' => $request->estoque,
            'validade' => $request->validade,
            'updated_by' => $request->updated_by
        ]);

        return response([
            'produto' => $product,
            'message' => 'Produto registrado com sucesso!'
        ], 201);
    }

    public function updateEstoque(Request $request, Product $product)
    {
        $request->validate([
            'entrada' => 'nullable|numeric',
            'saida' => 'nullable|numeric',
        ]);

        if ($request->entrada) {
            $product->estoque += $request->entrada;
        }

        if ($request->saida) {
            $product->estoque -= $request->saida;
        }

        // Verifica se o estoque é menor ou igual a zero
        if ($product->estoque <= 0) {
            return response(['message' => 'Estoque não pode ser vazio ou negativo.'], 400); // 400 é o código de "Bad Request"
        }

        $product->validade = $request->validade;
        $product->updated_by = $request->updated_by;
        $product->save();

        return response(['product' => $product]);
    }

    public function destroy(Product $product)
    {
        try {
            $product->delete();
            return response(['message' => 'Produto excluído com sucesso!'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Erro ao excluir produto.'], 500);
        }
    }
}