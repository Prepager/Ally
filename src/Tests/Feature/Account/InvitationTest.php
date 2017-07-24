<?php

namespace ZapsterStudios\Ally\Tests\Feature\Account;

use App\User;
use Laravel\Passport\Passport;
use ZapsterStudios\Ally\Tests\TestCase;
use ZapsterStudios\Ally\Models\TeamInvitation;

class InvitationTest extends TestCase
{
    /**
     * @test
     * @group Account
     */
    public function guestCanNotRetrieveInvitiations()
    {
        $response = $this->json('GET', route('account.invitations.index'));

        $response->assertStatus(401);
    }

    /**
     * @test
     * @group Account
     */
    public function userCanRetrieveInvitiations()
    {
        $user = factory(User::class)->create();

        $invitations = factory(TeamInvitation::class, 10)->create([
            'email' => $user->email,
        ]);

        Passport::actingAs($user, ['invitations.show']);
        $response = $this->json('GET', route('account.invitations.index'));

        $response->assertStatus(200);
        $response->assertJson([
            [
                'email' => $user->email,
                'team' => [
                    'id' => $invitations->get(0)->team_id,
                ],
            ],
        ]);

        $this->assertCount(10, json_decode($response->getContent()));
    }

    /**
     * @test
     * @group Account
     */
    public function userCanAcceptInvitiation()
    {
        $user = factory(User::class)->create();

        $invitation = factory(TeamInvitation::class)->create([
            'email' => $user->email,
        ]);

        Passport::actingAs($user, ['invitations.update']);
        $response = $this->json('PUT', route('account.invitations.update', $invitation->id));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $invitation->team_id,
        ]);

        $this->assertDatabaseHas('team_members', [
            'team_id' => $invitation->team_id,
            'user_id' => $user->id,
            'group' => $invitation->group,
        ]);

        $this->assertDatabaseMissing('team_invitations', [
            'team_id' => $invitation->team_id,
            'email' => $invitation->email,
        ]);
    }

    /**
     * @test
     * @group Account
     */
    public function userCanDeclineInvitiation()
    {
        $user = factory(User::class)->create();

        $invitation = factory(TeamInvitation::class)->create([
            'email' => $user->email,
        ]);

        Passport::actingAs($user, ['invitations.update']);
        $response = $this->json('DELETE', route('account.invitations.destroy', $invitation->id));

        $response->assertStatus(200);

        $this->assertDatabaseMissing('team_invitations', [
            'team_id' => $invitation->team_id,
            'email' => $invitation->email,
        ]);
    }
}
