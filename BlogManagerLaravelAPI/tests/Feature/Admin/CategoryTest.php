<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;

// ─── Admin Category Tests ─────────────────────────────────────────────────────
// Requires auth + role:admin middleware

// ── Helpers ──────────────────────────────────────────────────────────────────

function adminUser(): User
{
    return User::factory()->create(['role' => 'admin']);
}

// ─── Index ────────────────────────────────────────────────────────────────────

it('admin can view categories index', function () {
    $admin = adminUser();
    $this->actingAs($admin)
        ->get(route('admin.categories.index'))
        ->assertOk();
});

it('guest cannot view admin categories', function () {
    $this->get(route('admin.categories.index'))
        ->assertRedirect(route('login') . '?return=' . urlencode(route('admin.categories.index')));
})->skip('redirect destination depends on Fortify config');

it('regular user is redirected away from admin categories', function () {
    $user = User::factory()->create(['role' => 'user']);
    $this->actingAs($user)
        ->get(route('admin.categories.index'))
        ->assertRedirect();
});

// ─── Store ────────────────────────────────────────────────────────────────────

it('admin can create a category', function () {
    $admin = adminUser();
    $this->actingAs($admin)
        ->post(route('admin.categories.store'), ['name' => 'Technology'])
        ->assertRedirect(route('admin.categories.index'));

    $this->assertDatabaseHas('categories', ['name' => 'Technology']);
});

it('category name is required', function () {
    $admin = adminUser();
    $this->actingAs($admin)
        ->post(route('admin.categories.store'), ['name' => ''])
        ->assertSessionHasErrors(['name']);
});

it('category name must be unique', function () {
    Category::create(['name' => 'Sports']);
    $admin = adminUser();
    $this->actingAs($admin)
        ->post(route('admin.categories.store'), ['name' => 'Sports'])
        ->assertSessionHasErrors(['name']);
});

it('category name cannot exceed 255 characters', function () {
    $admin = adminUser();
    $this->actingAs($admin)
        ->post(route('admin.categories.store'), ['name' => str_repeat('a', 256)])
        ->assertSessionHasErrors(['name']);
});

// ─── Update ───────────────────────────────────────────────────────────────────

it('admin can update a category', function () {
    $admin    = adminUser();
    $category = Category::create(['name' => 'OldName']);

    $this->actingAs($admin)
        ->put(route('admin.categories.update', $category->id), ['name' => 'NewName'])
        ->assertRedirect(route('admin.categories.index'));

    $this->assertDatabaseHas('categories', ['name' => 'NewName']);
    $this->assertDatabaseMissing('categories', ['name' => 'OldName']);
});

// ─── Destroy ─────────────────────────────────────────────────────────────────

it('admin can delete a category with no posts', function () {
    $admin    = adminUser();
    $category = Category::create(['name' => 'Empty Category']);

    $this->actingAs($admin)
        ->delete(route('admin.categories.destroy', $category->id))
        ->assertRedirect(route('admin.categories.index'));

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

it('admin cannot delete a category that has posts associated', function () {
    $admin    = adminUser();
    $category = Category::create(['name' => 'Busy Category']);
    Post::create([
        'title'       => 'Post 1',
        'content'     => 'Content',
        'category_id' => $category->id,
    ]);

    $this->actingAs($admin)
        ->delete(route('admin.categories.destroy', $category->id))
        ->assertRedirect(route('admin.categories.index'))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('categories', ['id' => $category->id]);
});
