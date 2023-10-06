<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products'; // especificando o nome da tabela
    protected $fillable = ['produto', 'unidade_medida', 'estoque', 'validade', 'updated_by'];

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
