<?php

namespace ZapsterStudios\Ally\Tests\Configuration;

use Ally;
use ZapsterStudios\Ally\Tests\TestCase;

class PlanTest extends TestCase
{
    /**
     * @test
     * @group Configuration
     */
    public function canAddPlan()
    {
        Ally::addPlan('added-plan', 'Added Plan', 5);
        $plan = collect(Ally::$plans)->first(function ($info) {
            return $info->id == 'added-plan';
        });

        $this->assertTrue($plan !== null);
        $this->assertSame($plan->name, 'Added Plan');
        $this->assertSame($plan->price, 5);
        $this->assertSame($plan->active, 1);

        $plan->archive();
        $this->assertSame($plan->active, 0);

        $plan->maxMembers(5);
        $this->assertSame($plan->members, 5);

        $features = [
            'First awesome feature',
            'Second awesome feature',
        ];
        $plan->features($features);
        $this->assertSame($plan->features, $features);

        $permissions = [
            'can-do-first-thing' => true,
            'limit-this-other-thing' => 2,
        ];
        $plan->permissions($permissions);
        $this->assertSame($plan->permissions, $permissions);
    }

    /**
     * @test
     * @group Configuration
     */
    public function canDuplicatePlan()
    {
        $original = Ally::addPlan('some-plan', 'Some Plan', 5)
            ->archive()
            ->maxMembers(5)
            ->features([
                'First awesome feature',
                'Second awesome feature',
            ])
            ->permissions([
                'can-do-first-thing' => true,
                'limit-this-other-thing' => 2,
            ]);

        Ally::duplicatePlan('some-plan', 'duplicated-plan', 'Duplicated Plan');
        $plan = collect(Ally::$plans)->first(function ($info) {
            return $info->id == 'duplicated-plan';
        });

        $this->assertTrue($plan !== null);
        $this->assertSame($plan->name, 'Duplicated Plan');
        $this->assertSame($plan->price, $original->price);
        $this->assertSame($plan->active, $original->active);
        $this->assertSame($plan->members, $original->members);
        $this->assertSame($plan->features, $original->features);
        $this->assertSame($plan->permissions, $original->permissions);
    }

    /**
     * @test
     * @group Configuration
     */
    public function canNotDuplicateInvalidPlan()
    {
        $this->expectException(\Exception::class);

        Ally::duplicatePlan('invalud-plan', 'duplicated-invalid-plan', 'Duplicated Plan');
        $plan = collect(Ally::$plans)->first(function ($info) {
            return $info->id == 'duplicated-invalid-plan';
        });

        $this->assertFalse($plan !== null);
    }

    /**
     * @test
     * @group Configuration
     */
    public function canRetrievePlans()
    {
        $this->assertSame(Ally::plans()->all(), Ally::$plans);
        $this->assertSame(Ally::plan(Ally::$plans[2]->id), Ally::$plans[2]);

        $this->assertSame(Ally::freePlans()->all(), [Ally::$plans[0]]);
        $this->assertSame(Ally::freePlan(), Ally::$plans[0]);

        $activePlans = array_merge([], Ally::$plans);
        unset($activePlans[3]);

        Ally::addPlan('disabled-plan', 'Disabled plan', 1)->archive();
        $this->assertSame(Ally::activePlans()->all(), $activePlans);
        $this->assertSame(Ally::activePlanIDs()->all(), array_map(function ($active) {
            return $active->id;
        }, $activePlans));

        $this->assertSame(Ally::archivedPlans()->all(), [3 => Ally::$plans[3]]);
    }
}
