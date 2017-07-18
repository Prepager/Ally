<?php

namespace ZapsterStudios\TeamPay\Tests\Feature\Team;

use TeamPay;
use App\Team;
use App\User;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Event;
use ZapsterStudios\TeamPay\Tests\TestCase;
use ZapsterStudios\TeamPay\Models\TeamInvitation;
use ZapsterStudios\TeamPay\Events\Teams\Members\TeamMemberInvited;

class InvitationTest extends TestCase
{
    /** @test */
    public function guestCanNotRetrieveInvitations()
    {
        $team = factory(Team::class)->create();
        $response = $this->json('GET', route('teams.invitations.index', $team->slug));

        $response->assertStatus(401);
    }

    /** @test */
    public function nonMemberCanNotRetrieveInvitations()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create();

        Passport::actingAs($user, ['view-teams']);
        $response = $this->json('GET', route('teams.invitations.index', $team->slug));

        $response->assertStatus(403);
    }

    /** @test */
    public function memberCanRetrieveInvitations()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create());

        $invitations = factory(TeamInvitation::class, 2)->create([
            'team_id' => $team->id,
        ]);

        Passport::actingAs($user, ['view-teams']);
        $response = $this->json('GET', route('teams.invitations.index', $team->slug));

        $response->assertStatus(200);
        $response->assertJson([
            ['email' => $invitations->get(0)->email],
            ['email' => $invitations->get(1)->email],
        ]);

        $this->assertCount(2, $response->getData());
    }

    /** @test */
    public function memberCanRetrieveInvitation()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create());

        $invitation = factory(TeamInvitation::class)->create([
            'team_id' => $team->id,
        ]);

        Passport::actingAs($user, ['view-teams']);
        $response = $this->json('GET', route('teams.invitations.show', [$team->slug, $invitation->id]));

        $response->assertStatus(200);
        $response->assertJson([
            'email' => $invitation->email,
        ]);
    }

    /** @test */
    public function ownerCanNotCreateInvitationWithInvalidGroup()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create([
            'user_id' => $user->id,
        ]));

        Passport::actingAs($user, ['manage-teams']);
        $response = $this->json('POST', route('teams.invitations.store', [$team->slug]), [
            'email' => 'some-valid-email@example.com',
            'group' => 'invalid-group',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'group',
        ]);
    }

    /** @test */
    public function ownerCanCreateInvitation()
    {
        Event::fake();

        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create([
            'user_id' => $user->id,
        ]));

        Passport::actingAs($user, ['manage-teams']);
        $response = $this->json('POST', route('teams.invitations.store', [$team->slug]), [
            'email' => 'some-valid-email@example.com',
            'group' => 'member',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'email' => 'some-valid-email@example.com',
            'group' => 'member',
        ]);

        $this->assertDatabaseHas('team_invitations', [
            'email' => 'some-valid-email@example.com',
            'group' => 'member',
        ]);

        Event::assertDispatched(TeamMemberInvited::class, function ($e) use ($team) {
            return $e->team->slug == $team->slug
                && $e->email == 'some-valid-email@example.com';
        });

        // Notification...
    }

    /** @test */
    public function ownerCanNotUpdateInvitationWithInvalidGroup()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create([
            'user_id' => $user->id,
        ]));

        $invitation = factory(TeamInvitation::class)->create([
            'team_id' => $team->id,
        ]);

        Passport::actingAs($user, ['manage-teams']);
        $response = $this->json('PUT', route('teams.invitations.update', [$team->slug, $invitation->id]), [
            'group' => 'invalid-group',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'group',
        ]);
    }

    /** @test */
    public function ownerCanUpdateInvitation()
    {
        TeamPay::addGroup('extra', 'Extra Team');

        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create([
            'user_id' => $user->id,
        ]));

        $invitation = factory(TeamInvitation::class)->create([
            'team_id' => $team->id,
        ]);

        Passport::actingAs($user, ['manage-teams']);
        $response = $this->json('PUT', route('teams.invitations.update', [$team->slug, $invitation->id]), [
            'group' => 'extra',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'email' => $invitation->email,
            'group' => 'extra',
        ]);

        $this->assertDatabaseHas('team_invitations', [
            'email' => $invitation->email,
            'group' => 'extra',
        ]);
    }

    /** @test */
    public function ownerCanDeleteInvitation()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create([
            'user_id' => $user->id,
        ]));

        $invitation = factory(TeamInvitation::class)->create([
            'team_id' => $team->id,
        ]);

        Passport::actingAs($user, ['manage-teams']);
        $response = $this->json('DELETE', route('teams.invitations.destroy', [$team->slug, $invitation->id]));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('team_invitations', [
            'team_id' => $team->id,
            'email' => $invitation->email,
        ]);
    }
}
