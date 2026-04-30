<?php

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

// ─── User Post & Comment Tests ────────────────────────────────────────────────

// ── Helpers ───────────────────────────────────────────────────────────────────

function regularUser(): User
{
    return User::factory()->create(['role' => 'user']);
}

function seedPost(string $title = 'Test Post'): Post
{
    $category = Category::create(['name' => 'Category ' . uniqid()]);
    return Post::create([
        'title'       => $title,
        'content'     => 'Some interesting content',
        'category_id' => $category->id,
    ]);
}

// ─── Post Index ───────────────────────────────────────────────────────────────

it('user can view the posts listing', function () {
    $this->actingAs(regularUser())
        ->get(route('user.posts.index'))
        ->assertOk();
});

it('guest cannot view user posts listing', function () {
    $this->get(route('user.posts.index'))
        ->assertRedirect();
});

it('admin is redirected away from user posts index', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin)
        ->get(route('user.posts.index'))
        ->assertRedirect();
});

it('user posts index returns posts in response', function () {
    $user = regularUser();
    seedPost('Laravel is Great');

    $response = $this->actingAs($user)
        ->get(route('user.posts.index'));

    $response->assertOk();
});

// ─── Post Show ────────────────────────────────────────────────────────────────

it('user can view a single post', function () {
    $user = regularUser();
    $post = seedPost('Single Post View');

    $this->actingAs($user)
        ->get(route('user.posts.show', $post->id))
        ->assertOk();
});

it('viewing a non-existent post returns 404', function () {
    $user = regularUser();

    $this->actingAs($user)
        ->get(route('user.posts.show', 99999))
        ->assertNotFound();
});

// ─── Comments – Store ─────────────────────────────────────────────────────────

it('authenticated user can add a comment to a post', function () {
    $user = regularUser();
    $post = seedPost('Comment Me');

    $this->actingAs($user)
        ->post(route('user.posts.comments.store', $post->id), [
            'content' => 'This is my comment!',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('comments', [
        'content' => 'This is my comment!',
        'post_id' => $post->id,
        'user_id' => $user->id,
    ]);
});

it('comment content is required', function () {
    $user = regularUser();
    $post = seedPost('Validation Post');

    $this->actingAs($user)
        ->post(route('user.posts.comments.store', $post->id), [
            'content' => '',
        ])
        ->assertSessionHasErrors(['content']);
});

it('comment content cannot exceed 1000 characters', function () {
    $user = regularUser();
    $post = seedPost('Long Comment Post');

    $this->actingAs($user)
        ->post(route('user.posts.comments.store', $post->id), [
            'content' => str_repeat('a', 1001),
        ])
        ->assertSessionHasErrors(['content']);
});

it('guest cannot post a comment', function () {
    $post = seedPost('Guest Comment Post');

    $this->post(route('user.posts.comments.store', $post->id), [
        'content' => 'I am anonymous',
    ])
        ->assertRedirect();

    $this->assertDatabaseMissing('comments', ['content' => 'I am anonymous']);
});

it('adding a comment to a non-existent post returns 404', function () {
    $user = regularUser();

    $this->actingAs($user)
        ->post(route('user.posts.comments.store', 99999), [
            'content' => 'Orphan comment',
        ])
        ->assertNotFound();
});

// ─── Comments – Destroy ───────────────────────────────────────────────────────

it('user can delete their own comment', function () {
    $user    = regularUser();
    $post    = seedPost('Delete Comment Post');
    $comment = $post->comments()->create([
        'content' => 'Delete me',
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->delete(route('user.posts.comments.destroy', [$post->id, $comment->id]))
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});

it('user cannot delete another user\'s comment', function () {
    $owner   = regularUser();
    $other   = User::factory()->create(['role' => 'user']);
    $post    = seedPost('Ownership Test');
    $comment = $post->comments()->create([
        'content' => 'Owner comment',
        'user_id' => $owner->id,
    ]);

    // other tries to delete owner's comment
    $this->actingAs($other)
        ->delete(route('user.posts.comments.destroy', [$post->id, $comment->id]))
        ->assertNotFound();

    $this->assertDatabaseHas('comments', ['id' => $comment->id]);
});

it('deleting a non-existent comment returns 404', function () {
    $user = regularUser();
    $post = seedPost('Missing Comment');

    $this->actingAs($user)
        ->delete(route('user.posts.comments.destroy', [$post->id, 99999]))
        ->assertNotFound();
});
