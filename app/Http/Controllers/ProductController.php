<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('updatedByUser'); // Carregar o relacionamento com o usuário que atualizou.

        if ($request->has('name')) {
            $query->where('produto', 'like', '%' . $request->name . '%');
        }

        $produtos = $query->paginate($request->get('perPage', 30));

        // Ajustar os links
        $links = $produtos->toArray()['links'];
        
        // Ajustar o link "Previous" na primeira página
        if ($produtos->currentPage() == 1 && !$links[0]['url']) {
            $links[0]['url'] = $produtos->url(1);
        }

        // Ajustar o link "Next" na última página
        if ($produtos->currentPage() == $produtos->lastPage() && !$links[count($links)-1]['url']) {
            $links[count($links)-1]['url'] = $produtos->url($produtos->lastPage());
        }

        // Substituir os links originais
        $produtosArr = $produtos->toArray();

        // Substituir o ID pelo nome do usuário em cada produto
        foreach ($produtosArr['data'] as &$produto) {
            $produto['updated_by'] = $produto['updated_by_user']['name'];
            unset($produto['updated_by_user']); // Remova a chave 'updated_by_user' se você não quiser retornar os detalhes completos do usuário.
        }

        $produtosArr['links'] = $links;

        return response(['produtos' => $produtosArr]);
    }

    public function showById(Product $product)
    {
        $product->load('updatedByUser'); // Carrega o relacionamento com o usuário que atualizou.
    
        // Substituir o ID pelo nome do usuário
        $product->updated_by = $product->updatedByUser->name;
        unset($product->updatedByUser); // Remova a chave 'updatedByUser' se você não quiser retornar os detalhes completos do usuário.
    
        return response(['produto' => $product]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'produto' => 'required|string|max:255|unique:products',
            'unidade_medida' => 'required|in:Unidade,Pacote,Rolo,Caixa,Bloco,Maço,Metro,Frasco,Tubo,Galão',
            'estoque' => 'required|numeric',
        ]);

        $product = new Product();
        $product->produto = $request->produto;
        $product->unidade_medida = $request->unidade_medida;
        $product->estoque = $request->estoque;
        $product->validade = $request->validade;
        $product->updated_by = $request->user()->id; // assumindo que o usuário está autenticado
        $product->save();

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
        $product->updated_by = $request->user()->id;
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