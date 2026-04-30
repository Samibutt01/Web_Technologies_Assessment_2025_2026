<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;

// ─── Admin Post Tests ─────────────────────────────────────────────────────────

// ── Helper ───────────────────────────────────────────────────────────────────

function makeAdmin(): User
{
    return User::factory()->create(['role' => 'admin']);
}

function makeCategory(string $name = 'Test Category'): Category
{
    return Category::create(['name' => $name]);
}

// ─── Index ────────────────────────────────────────────────────────────────────

it('admin can view posts index', function () {
    $this->actingAs(makeAdmin())
        ->get(route('admin.posts.index'))
        ->assertOk();
});

it('regular user is redirected from admin posts index', function () {
    $user = User::factory()->create(['role' => 'user']);
    $this->actingAs($user)
        ->get(route('admin.posts.index'))
        ->assertRedirect();
});

// ─── Store ────────────────────────────────────────────────────────────────────

it('admin can create a post', function () {
    $admin    = makeAdmin();
    $category = makeCategory('Science');

    $this->actingAs($admin)
        ->post(route('admin.posts.store'), [
            'title'       => 'My First Post',
            'content'     => 'Hello world content',
            'category_id' => $category->id,
        ])
        ->assertRedirect(route('admin.posts.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('posts', [
        'title'       => 'My First Post',
        'category_id' => $category->id,
    ]);
});

it('post title is required', function () {
    $admin    = makeAdmin();
    $category = makeCategory('Music');

    $this->actingAs($admin)
        ->post(route('admin.posts.store'), [
            'title'       => '',
            'content'     => 'Some content',
            'category_id' => $category->id,
        ])
        ->assertSessionHasErrors(['title']);
});

it('post content is required', function () {
    $admin    = makeAdmin();
    $category = makeCategory('Art');

    $this->actingAs($admin)
        ->post(route('admin.posts.store'), [
            'title'       => 'Title Here',
            'content'     => '',
            'category_id' => $category->id,
        ])
        ->assertSessionHasErrors(['content']);
});

it('post category_id must exist in categories', function () {
    $admin = makeAdmin();

    $this->actingAs($admin)
        ->post(route('admin.posts.store'), [
            'title'       => 'Ghost Post',
            'content'     => 'No category',
            'category_id' => 9999,
        ])
        ->assertSessionHasErrors(['category_id']);
});



it('post can be created with a published_at date', function () {
    $admin    = makeAdmin();
    $category = makeCategory('Events');

    $this->actingAs($admin)
        ->post(route('admin.posts.store'), [
            'title'        => 'Future Event',
            'content'      => 'Details here',
            'category_id'  => $category->id,
            'published_at' => '2025-12-25',
        ])
        ->assertRedirect(route('admin.posts.index'));

    $post = Post::where('title', 'Future Event')->first();
    expect($post->published_at)->not->toBeNull();
});

// ─── Update ───────────────────────────────────────────────────────────────────

it('admin can update a post', function () {
    $admin    = makeAdmin();
    $category = makeCategory('Travel');
    $post     = Post::create([
        'title'       => 'Old Title',
        'content'     => 'Old Content',
        'category_id' => $category->id,
    ]);

    $this->actingAs($admin)
        ->put(route('admin.posts.update', $post->id), [
            'title'       => 'Updated Title',
            'content'     => 'Updated Content',
            'category_id' => $category->id,
        ])
        ->assertRedirect(route('admin.posts.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('posts', ['title' => 'Updated Title']);
    $this->assertDatabaseMissing('posts', ['title' => 'Old Title']);
});

it('update returns 404 for non-existent post', function () {
    $admin    = makeAdmin();
    $category = makeCategory('History');

    $this->actingAs($admin)
        ->put(route('admin.posts.update', 99999), [
            'title'       => 'Ghost Update',
            'content'     => 'Content',
            'category_id' => $category->id,
        ])
        ->assertNotFound();
});

// ─── Destroy ─────────────────────────────────────────────────────────────────

it('admin can delete a post', function () {
    $admin    = makeAdmin();
    $category = makeCategory('Delete Cat');
    $post     = Post::create([
        'title'       => 'To Be Deleted',
        'content'     => 'Bye',
        'category_id' => $category->id,
    ]);

    $this->actingAs($admin)
        ->delete(route('admin.posts.destroy', $post->id))
        ->assertRedirect(route('admin.posts.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('posts', ['id' => $post->id]);
});

it('deleting a non-existent post returns 404', function () {
    $admin = makeAdmin();

    $this->actingAs($admin)
        ->delete(route('admin.posts.destroy', 99999))
        ->assertNotFound();
});
