<?php

namespace Hypefactors\Laravel\Follow\Tests\Traits;

use Hypefactors\Laravel\Follow\Tests\Stubs\UserStub;
use Hypefactors\Laravel\Follow\Tests\Stubs\CompanyStub;
use Hypefactors\Laravel\Follow\Tests\FunctionalTestCase;

class CanFollowTest extends FunctionalTestCase
{
    /** @test */
    public function an_entity_can_follow_another_entity()
    {
        $user    = factory(UserStub::class)->create();
        $company = factory(CompanyStub::class)->create();

        $user->follow($company);

        $this->assertTrue($user->hasFollowings());
        $this->assertTrue($user->isFollowing($company));
    }

    /** @test */
    public function an_entity_can_follow_many_entities_at_once()
    {
        $user = factory(UserStub::class)->create();

        $companies = factory(CompanyStub::class, 3)->create();

        $user->followMany($companies);

        $this->assertCount(3, $user->followings);
        $this->assertTrue($user->hasFollowings());
    }

    /** @test */
    public function an_entity_can_unfollow_another_entity()
    {
        $user    = factory(UserStub::class)->create();
        $company = factory(CompanyStub::class)->create();

        $user->follow($company);

        $this->assertTrue($user->isFollowing($company));

        $user->unfollow($company);

        $this->assertFalse($user->isFollowing($company));
    }

    /** @test */
    public function an_entity_can_unfollow_many_entities_at_once()
    {
        $user = factory(UserStub::class)->create();

        $companies = factory(CompanyStub::class, 3)->create();

        $user->followMany($companies);

        $this->assertCount(3, $user->followings);
        $this->assertTrue($user->hasFollowings());

        $user = $user->unfollowMany($companies);

        $this->assertCount(0, $user->followings);
        $this->assertFalse($user->hasFollowings());
    }

    /** @test */
    public function an_entity_can_refollow_an_entity()
    {
        $user    = factory(UserStub::class)->create();
        $company = factory(CompanyStub::class)->create();

        $user->follow($company);

        $this->assertTrue($user->hasFollowings());
        $this->assertTrue($user->isFollowing($company));

        $user->unfollow($company);

        $this->assertFalse($user->hasFollowings());
        $this->assertFalse($user->isFollowing($company));

        $user->follow($company);

        $this->assertTrue($user->hasFollowings());
        $this->assertTrue($user->isFollowing($company));
    }

    /** @test */
    public function deleting_a_follower_entity_deletes_the_following_records()
    {
        $user    = factory(UserStub::class)->create();
        $company = factory(CompanyStub::class)->create();

        $user->follow($company);

        $user->delete();

        $this->assertFalse($company->hasFollowers());
    }

    /** @test */
    public function an_entity_can_have_many_entities_synchronized_as_followings()
    {
        $user = factory(UserStub::class)->create();

        $companies = factory(CompanyStub::class, 3)->create();

        $user->followMany($companies);

        $this->assertCount(3, $user->followings);
        $this->assertTrue($user->hasFollowings());

        $companies = factory(CompanyStub::class, 4)->create();

        $user = $user->syncManyFollowings($companies);

        $this->assertCount(4, $user->followings);
        $this->assertTrue($user->hasFollowings());
        $this->assertEquals(
            $companies->pluck('id')->toArray(),
            $user->followings->pluck('id')->toArray()
        );
    }
}
