<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;

// ─── Role Middleware Tests ────────────────────────────────────────────────────
// Verifies that the RoleMiddlewer correctly gates admin vs user routes.

// ── Helpers ───────────────────────────────────────────────────────────────────

function mkAdmin(): User  { return User::factory()->create(['role' => 'admin']); }
function mkUser(): User   { return User::factory()->create(['role' => 'user']); }

function mkPost(): Post
{
    $cat = Category::create(['name' => 'Role Test Cat ' . uniqid()]);
    return Post::create(['title' => 'Role Post', 'content' => 'Body', 'category_id' => $cat->id]);
}

// ─── Unauthenticated ──────────────────────────────────────────────────────────

it('unauthenticated user cannot access admin routes', function () {
    $this->get(route('admin.posts.index'))->assertRedirect();
    $this->get(route('admin.categories.index'))->assertRedirect();
});

it('unauthenticated user cannot access user routes', function () {
    $this->get(route('user.posts.index'))->assertRedirect();
});

// ─── Admin Access ─────────────────────────────────────────────────────────────

it('admin can access all admin routes', function () {
    $admin = mkAdmin();
    $this->actingAs($admin)->get(route('admin.posts.index'))->assertOk();
    $this->actingAs($admin)->get(route('admin.categories.index'))->assertOk();
});

it('admin is redirected when hitting user routes', function () {
    $admin = mkAdmin();
    // Role middleware redirects admin → admin posts when they hit a user route
    $this->actingAs($admin)
        ->get(route('user.posts.index'))
        ->assertRedirect();
});

// ─── Regular User Access ──────────────────────────────────────────────────────

it('regular user can access user routes', function () {
    $user = mkUser();
    $this->actingAs($user)->get(route('user.posts.index'))->assertOk();
});

it('regular user is redirected when hitting admin routes', function () {
    $user = mkUser();
    $this->actingAs($user)
        ->get(route('admin.posts.index'))
        ->assertRedirect(route('user.posts.index'));
});

it('regular user is redirected from admin categories', function () {
    $user = mkUser();
    $this->actingAs($user)
        ->get(route('admin.categories.index'))
        ->assertRedirect(route('user.posts.index'));
});

// ─── Dashboard ────────────────────────────────────────────────────────────────

it('authenticated user can view the dashboard', function () {
    $this->actingAs(mkUser())
        ->get(route('dashboard'))
        ->assertOk();
});

it('admin can view the dashboard', function () {
    $this->actingAs(mkAdmin())
        ->get(route('dashboard'))
        ->assertOk();
});

it('guest cannot view the dashboard', function () {
    $this->get(route('dashboard'))->assertRedirect();
});
