<?php

namespace Tests\Feature\Http\Controllers;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Creator\SubscriptionController
 */
final class SubscriptionControllerTest extends TestCase
{
    #[Test]
    public function store_behaves_as_expected(): void
    {
        $response = $this->post(route('subscriptions.store'));
    }
}
