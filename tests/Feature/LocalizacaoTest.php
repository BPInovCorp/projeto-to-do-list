<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\App;
use Tests\TestCase;

class LocalizacaoTest extends TestCase
{
    public function test_permite_alterar_idioma_via_post_locale(): void
    {
        $response = $this->postJson('/locale', [
            'locale' => 'en',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'sucesso')
            ->assertJsonPath('locale', 'en');

        $this->assertEquals('en', session('locale'));
        $this->assertEquals('en', App::getLocale());
    }

    public function test_rejeita_idioma_invalido_com_erro_de_validacao(): void
    {
        $response = $this->postJson('/locale', [
            'locale' => 'fr',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['locale']);
    }

    public function test_middleware_aplica_idioma_a_partir_do_cabecalho_x_locale(): void
    {
        $response = $this->withHeader('X-Locale', 'en')
            ->getJson('/');

        $response->assertStatus(200)
            ->assertHeader('Content-Language', 'en');

        $this->assertEquals('en', App::getLocale());
    }

    public function test_resposta_http_inclui_cabecalho_content_language(): void
    {
        $responsePt = $this->withHeader('X-Locale', 'pt_PT')
            ->getJson('/');

        $responsePt->assertStatus(200)
            ->assertHeader('Content-Language', 'pt-PT');

        $responseEn = $this->withHeader('X-Locale', 'en')
            ->getJson('/');

        $responseEn->assertStatus(200)
            ->assertHeader('Content-Language', 'en');
    }
}