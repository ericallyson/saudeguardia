<?php

namespace Tests\Feature;

use App\Models\Meta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MetaTipoTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_accepts_texto_tipo(): void
    {
        $meta = Meta::create([
            'nome' => 'Pressão Arterial',
            'descricao' => 'Informe sua pressão arterial',
            'tipo' => 'texto',
            'periodicidade_padrao' => 'diario',
        ]);

        $this->assertDatabaseHas('metas', [
            'id' => $meta->id,
            'tipo' => 'texto',
        ]);
    }
}
