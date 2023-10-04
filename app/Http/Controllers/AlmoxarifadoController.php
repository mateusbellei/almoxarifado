<?php

namespace App\Http\Controllers;

use App\Models\Almoxarifado;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AlmoxarifadoController extends Controller
{
    public function index(Request $request)
    {
        $query = Almoxarifado::with('updatedByUser'); // Carregar o relacionamento com o usuário que atualizou.

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



    public function store(Request $request)
    {
        $request->validate([
            'produto' => 'required|string|max:255|unique:almoxarifado',
            'unidade_medida' => 'required|in:Unidade,Pacote,Rolo,Caixa,Bloco,Maço,Metro,Frasco,Tubo,Galão',
            'estoque' => 'required|numeric',
        ]);

        $almoxarifado = new Almoxarifado();
        $almoxarifado->produto = $request->produto;
        $almoxarifado->unidade_medida = $request->unidade_medida;
        $almoxarifado->estoque = $request->estoque;
        $almoxarifado->validade = $request->validade;
        $almoxarifado->updated_by = $request->user()->id; // assumindo que o usuário está autenticado
        $almoxarifado->save();

        return response([
            'produto' => $almoxarifado, 
            'message' => 'Produto registrado com sucesso!'
        ], 201);
    }

    public function updateEstoque(Request $request, Almoxarifado $almoxarifado)
    {
        $request->validate([
            'entrada' => 'nullable|numeric',
            'saida' => 'nullable|numeric',
        ]);

        if ($request->entrada) {
            $almoxarifado->estoque += $request->entrada;
        }

        if ($request->saida) {
            $almoxarifado->estoque -= $request->saida;
        }

        $almoxarifado->validade = $request->validade;
        $almoxarifado->updated_by = $request->user()->id;
        $almoxarifado->save();

        return response(['almoxarifado' => $almoxarifado]);
    }

    public function destroy(Almoxarifado $almoxarifado)
    {
        try {
            $almoxarifado->delete();
            return response(['message' => 'Produto excluído com sucesso!'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Erro ao excluir produto.'], 500);
        }
    }
}