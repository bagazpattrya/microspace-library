<?php

namespace Tests\Feature;

use Tests\TestCase;

class CatalogTest extends TestCase
{
    public function test_every_page_and_preview_renders(): void
    {
        $this->get('/')->assertOk();

        foreach (array_keys(config('catalog.components')) as $slug) {
            $this->get("/c/$slug")->assertOk();
            $this->get("/preview/$slug")->assertOk();
        }
    }

    public function test_unknown_component_404s(): void
    {
        $this->get('/c/nope')->assertNotFound();
    }
}
