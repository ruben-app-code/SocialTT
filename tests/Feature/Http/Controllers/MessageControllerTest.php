<?php

namespace Tests\Feature\Http\Controllers;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Creator\MessageController
 */
final class MessageControllerTest extends TestCase
{
    #[Test]
    public function store_behaves_as_expected(): void
    {
        $response = $this->post(route('messages.store'));
    }
}
