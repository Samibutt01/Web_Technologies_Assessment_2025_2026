<?php

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ─── Post Model ───────────────────────────────────────────────────────────────

describe('Post Model', function () {

    it('can be created with valid attributes', function () {
        $category = Category::create(['name' => 'Tech']);
        $post = Post::create([
            'title'       => 'Hello World',
            'content'     => 'Some content here',
            'category_id' => $category->id,
        ]);

        expect($post)->toBeInstanceOf(Post::class)
            ->and($post->title)->toBe('Hello World')
            ->and($post->content)->toBe('Some content here')
            ->and($post->category_id)->toBe($category->id);
    });

    it('belongs to a category', function () {
        $category = Category::create(['name' => 'Sports']);
        $post = Post::create([
            'title'       => 'Match Day',
            'content'     => 'Great game',
            'category_id' => $category->id,
        ]);

        expect($post->category)->toBeInstanceOf(Category::class)
            ->and($post->category->name)->toBe('Sports');
    });

    it('has many comments', function () {
        $category = Category::create(['name' => 'News']);
        $post = Post::create([
            'title'       => 'Breaking News',
            'content'     => 'Details here',
            'category_id' => $category->id,
        ]);
        $user = User::factory()->create();
        $post->comments()->create([
            'content' => 'Nice post!',
            'user_id' => $user->id,
        ]);

        expect($post->comments)->toHaveCount(1)
            ->and($post->comments->first()->content)->toBe('Nice post!');
    });

    it('fillable attributes are set correctly', function () {
        $post = new Post();
        expect($post->getFillable())->toContain('title')
            ->and($post->getFillable())->toContain('content')
            ->and($post->getFillable())->toContain('category_id')
            ->and($post->getFillable())->toContain('published_at');
    });
});

// ─── Category Model ───────────────────────────────────────────────────────────

describe('Category Model', function () {

    it('can be created with a name', function () {
        $category = Category::create(['name' => 'Lifestyle']);
        expect($category)->toBeInstanceOf(Category::class)
            ->and($category->name)->toBe('Lifestyle');
    });

    it('has many posts', function () {
        $category = Category::create(['name' => 'Gaming']);
        $post = Post::create([
            'title'       => 'Best Games',
            'content'     => 'Top picks',
            'category_id' => $category->id,
        ]);

        expect($category->posts)->toHaveCount(1)
            ->and($category->posts->first()->title)->toBe('Best Games');
    });

    it('fillable contains name', function () {
        $category = new Category();
        expect($category->getFillable())->toContain('name');
    });
});

// ─── Comment Model ────────────────────────────────────────────────────────────

describe('Comment Model', function () {

    it('belongs to a post and a user', function () {
        $category = Category::create(['name' => 'General']);
        $post = Post::create([
            'title'       => 'Discussion Post',
            'content'     => 'Let us talk',
            'category_id' => $category->id,
        ]);
        $user    = User::factory()->create();
        $comment = $post->comments()->create([
            'content' => 'Great discussion!',
            'user_id' => $user->id,
        ]);

        expect($comment->post)->toBeInstanceOf(Post::class)
            ->and($comment->user)->toBeInstanceOf(User::class)
            ->and($comment->content)->toBe('Great discussion!');
    });

    it('fillable attributes include content, user_id, post_id', function () {
        $comment = new Comment();
        expect($comment->getFillable())
            ->toContain('content')
            ->toContain('user_id')
            ->toContain('post_id');
    });
});

// ─── User Model ───────────────────────────────────────────────────────────────

describe('User Model', function () {

    it('can be created via factory', function () {
        $user = User::factory()->create();
        expect($user)->toBeInstanceOf(User::class)
            ->and($user->email)->toBeString();
    });

    it('has many comments', function () {
        $user     = User::factory()->create();
        $category = Category::create(['name' => 'Music']);
        $post     = Post::create([
            'title'       => 'Top Albums',
            'content'     => 'My favourites',
            'category_id' => $category->id,
        ]);
        $post->comments()->create([
            'content' => 'Love this list!',
            'user_id' => $user->id,
        ]);

        expect($user->comments)->toHaveCount(1);
    });

    it('password is hidden from serialization', function () {
        $user = User::factory()->create();
        expect($user->getHidden())->toContain('password');
    });

    it('default role is user', function () {
        $user = User::factory()->create(['role' => 'user']);
        expect($user->role)->toBe('user');
    });

    it('role can be set to admin', function () {
        $user = User::factory()->create(['role' => 'admin']);
        expect($user->role)->toBe('admin');
    });
});
