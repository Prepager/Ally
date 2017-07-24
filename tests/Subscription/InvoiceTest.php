<?php

namespace ZapsterStudios\Ally\Tests\Subscription;

use App\Team;
use App\User;
use Laravel\Passport\Passport;
use ZapsterStudios\Ally\Tests\TestCase;

class InvoiceTest extends TestCase
{
    /**
     * @test
     * @group Subscription
     */
    public function ownerCanRetrieveInvoices()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create(['user_id' => $user->id]));

        $team->newSubscription('default', 'valid-first-plan')->create('fake-valid-nonce');
        $team->newSubscription('default', 'valid-second-plan')->create('fake-valid-nonce');

        Passport::actingAs($user, ['teams.invoices']);
        $response = $this->json('GET', route('invoices.index', $team->slug));

        $this->assertCount(2, $response->getData());
        $response = $this->json('GET', route('invoices.show', [
            $team->slug,
            $response->getData()[0]->id,
        ]));

        $this->assertSame($response->headers->get('content-type'), 'application/pdf');
    }
}
