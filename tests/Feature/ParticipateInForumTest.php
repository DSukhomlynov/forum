<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ParticipateInForum extends TestCase
{
    use DatabaseMigrations;

    function test_unauthenticated_users_may_not_add_replies()
    {
        $this->withExceptionHandling()
            ->post('/threads/some-channel/1/replies', [])
            ->assertRedirect('/login');
    }

    function test_an_authenticated_user_may_participate_in_forum_threads()
    {
        $this->be($user = factory('App\User')->create());

        $thread = factory('App\Thread')->create();
        $reply = factory('App\Reply')->make();

        $this->post($thread->path(). '/replies', $reply->toArray());

        $this->get($thread->path())
            ->assertSee($reply->body);
    }

    function test_a_reply_requires_a_body()
    {
        $this->withExceptionHandling()->signIn();

        $thread = factory('App\Thread')->create();
        $reply = factory('App\Reply', ['body' => null])->make();

        $this->post($thread->path(). '/replies', $reply->toArray())
            ->assertSessionHasErrors('body');
    }
}
