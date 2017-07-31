<?php

namespace ZapsterStudios\Ally\Tests\Team;

use Ally;
use App\Team;
use App\User;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Event;
use ZapsterStudios\Ally\Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use ZapsterStudios\Ally\Models\TeamInvitation;
use ZapsterStudios\Ally\Events\Teams\Members\TeamMemberInvited;
use ZapsterStudios\Ally\Notifications\TeamInvitation as TeamInvitationMail;

class InvitationTest extends TestCase
{
    /**
     * @test
     * @group Team
     */
    public function guestCanNotRetrieveInvitations()
    {
        $team = factory(Team::class)->create();
        $response = $this->json('GET', route('teams.invitations.index', $team->slug));

        $response->assertStatus(401);
    }

    /**
     * @test
     * @group Team
     */
    public function nonMemberCanNotRetrieveInvitations()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create();

        Passport::actingAs($user, ['teams.show']);
        $response = $this->json('GET', route('teams.invitations.index', $team->slug));

        $response->assertStatus(403);
    }

    /**
     * @test
     * @group Team
     */
    public function memberCanRetrieveInvitations()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create());

        $invitations = factory(TeamInvitation::class, 2)->create([
            'team_id' => $team->id,
        ]);

        Passport::actingAs($user, ['teams.show']);
        $response = $this->json('GET', route('teams.invitations.index', $team->slug));

        $response->assertStatus(200);
        $response->assertJson([
            ['email' => $invitations->get(0)->email],
            ['email' => $invitations->get(1)->email],
        ]);

        $this->assertCount(2, $response->getData());
    }

    /**
     * @test
     * @group Team
     */
    public function memberCanRetrieveInvitation()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create());

        $invitation = factory(TeamInvitation::class)->create([
            'team_id' => $team->id,
        ]);

        Passport::actingAs($user, ['teams.show']);
        $response = $this->json('GET', route('teams.invitations.show', [$team->slug, $invitation->id]));

        $response->assertStatus(200);
        $response->assertJson([
            'email' => $invitation->email,
        ]);
    }

    /**
     * @test
     * @group Team
     */
    public function ownerCanNotCreateInvitationWithInvalidGroup()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create([
            'user_id' => $user->id,
        ]));

        Passport::actingAs($user, ['teams.members.create']);
        $response = $this->json('POST', route('teams.invitations.store', [$team->slug]), [
            'email' => 'some-valid-email@example.com',
            'group' => 'invalid-group',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'errors' => [
                'group',
            ],
        ]);
    }

    /**
     * @test
     * @group Team
     */
    public function ownerCanNotCreateInvitationForTooManyUsers()
    {
        $plans = Ally::$plans;
        Ally::$plans = [];
        Ally::addPlan('free-plan', 'Free Plan')->maxMembers(1);

        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create([
            'user_id' => $user->id,
        ]));

        Passport::actingAs($user, ['teams.members.create']);
        $response = $this->json('POST', route('teams.invitations.store', [$team->slug]), [
            'email' => 'some-valid-email@example.com',
            'group' => 'member',
        ]);

        $response->assertStatus(402);

        Ally::$plans = $plans;
    }

    /**
     * @test
     * @group Team
     */
    public function ownerCanCreateInvitationForGuest()
    {
        Event::fake();
        Notification::fake();

        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create([
            'user_id' => $user->id,
        ]));

        $extra = new User([
            'email' => 'some-valid-email@example.com',
        ]);

        Passport::actingAs($user, ['teams.members.create']);
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

        Notification::assertSentTo($extra, TeamInvitationMail::class,
            function ($notification, $channels) use ($team, $extra) {
                $mail = $notification->toMail($extra)->toArray();

                $this->assertSame($mail['actionUrl'], Ally::$linkInvitations);
                $this->assertSame($notification->team->slug, $team->slug);
                $this->assertSame($notification->user->email, $extra->email);
                $this->assertFalse($notification->exists);

                return true;
            }
        );
    }

    /**
     * @test
     * @group Team
     */
    public function ownerCanCreateInvitationForUser()
    {
        Event::fake();
        Notification::fake();

        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create([
            'user_id' => $user->id,
        ]));

        $extra = factory(User::class)->create();

        Passport::actingAs($user, ['teams.members.create']);
        $response = $this->json('POST', route('teams.invitations.store', [$team->slug]), [
            'email' => $extra->email,
            'group' => 'member',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'email' => $extra->email,
            'group' => 'member',
        ]);

        $this->assertDatabaseHas('team_invitations', [
            'email' => $extra->email,
            'group' => 'member',
        ]);

        Event::assertDispatched(TeamMemberInvited::class, function ($e) use ($team, $extra) {
            return $e->team->slug == $team->slug
                && $e->email == $extra->email;
        });

        Notification::assertSentTo($extra, TeamInvitationMail::class,
            function ($notification, $channels) use ($team, $extra) {
                $mail = $notification->toMail($extra)->toArray();
                $array = $notification->toArray($extra);

                $this->assertSame($array['team_name'], $team->name);
                $this->assertSame($array['user_name'], $extra->name);

                $this->assertSame($mail['actionUrl'], Ally::$linkInvitations);
                $this->assertSame($notification->team->slug, $team->slug);
                $this->assertSame($notification->user->email, $extra->email);
                $this->assertTrue($notification->exists);

                return true;
            }
        );
    }

    /**
     * @test
     * @group Team
     */
    public function ownerCanNotUpdateInvitationWithInvalidGroup()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create([
            'user_id' => $user->id,
        ]));

        $invitation = factory(TeamInvitation::class)->create([
            'team_id' => $team->id,
        ]);

        Passport::actingAs($user, ['teams.members.update']);
        $response = $this->json('PUT', route('teams.invitations.update', [$team->slug, $invitation->id]), [
            'group' => 'invalid-group',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'errors' => [
                'group',
            ],
        ]);
    }

    /**
     * @test
     * @group Team
     */
    public function ownerCanUpdateInvitation()
    {
        Ally::addGroup('extra', 'Extra Team');

        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create([
            'user_id' => $user->id,
        ]));

        $invitation = factory(TeamInvitation::class)->create([
            'team_id' => $team->id,
        ]);

        Passport::actingAs($user, ['teams.members.update']);
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

    /**
     * @test
     * @group Team
     */
    public function ownerCanDeleteInvitation()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create([
            'user_id' => $user->id,
        ]));

        $invitation = factory(TeamInvitation::class)->create([
            'team_id' => $team->id,
        ]);

        Passport::actingAs($user, ['teams.members.delete']);
        $response = $this->json('DELETE', route('teams.invitations.destroy', [$team->slug, $invitation->id]));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('team_invitations', [
            'team_id' => $team->id,
            'email' => $invitation->email,
        ]);
    }
}
