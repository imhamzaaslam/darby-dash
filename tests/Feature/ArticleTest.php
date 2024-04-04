<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_admin_is_able_to_create_an_article_with_category()
    {
        $admin = $this->user();
        $category = ArticleCategory::factory()->create();
        $title = $this->faker->title;
        $content = $this->faker->paragraph;

        $response = $this->actingAs($admin)->postJson('api/v1/admin/support/articles', [
            'title' => $title,
            'content' => $content,
            'category_id' => $category->id,
        ]);
        $response->assertCreated();
        $response->assertJsonFragment([
            'title' => $title,
            'content' => $content,
            'category' => $category->toArray()
        ]);
    }

    public function test_admin_is_able_to_create_an_article_without_category()
    {
        $this->withoutExceptionHandling();
        $admin = $this->user();
        $title = $this->faker->title;
        $content = $this->faker->paragraph;

        $response = $this->actingAs($admin)->postJson('api/v1/admin/support/articles', [
            'title' => $title,
            'content' => $content,
            'category_id' => null
        ]);
        $response->assertCreated();
        $response->assertJsonFragment([
            'title' => $title,
            'content' => $content,
        ]);
    }

    public function test_admin_is_able_to_update_the_category_of_an_article()
    {
        $admin = $this->user();
        $category = ArticleCategory::factory()->create();
        $categoryB = ArticleCategory::factory()->create();
        $article = Article::factory(['category_id' => $category->id])->create();

        $response = $this->actingAs($admin)->patch("api/v1/admin/support/articles/{$article->id}", [
            'category_id' => $categoryB->id
        ]);

        $article->refresh();
        $response->assertOk();
        $response->assertJsonFragment([
            'category' => $categoryB->toArray()
        ]);
    }

    public function test_admin_is_able_to_update_an_article()
    {
        $admin = $this->user();
        $title = $this->faker->title;
        $content = $this->faker->paragraph;
        $article = Article::factory()->create();

        $response = $this->actingAs($admin)->patch("api/v1/admin/support/articles/{$article->id}", [
            'title' => $title,
            'content' => $content
        ]);

        $article->refresh();
        $response->assertOk();
        $this->assertEquals($title, $article->title);
        $this->assertEquals($content, $article->content);
        $response->assertJsonFragment([
            'title' => $title,
            'content' => $content
        ]);
    }

    public function test_that_admin_is_able_to_view_all_articles()
    {
        $admin = $this->user();
        Article::factory(10)->create();

        $response = $this->actingAs($admin)->get("api/v1/support/articles");

        $response->assertJsonCount(10, 'data');
    }

    public function test_admin_is_able_to_delete_an_article()
    {
        $admin = $this->user();
        $article = Article::factory()->create();

        $response = $this->actingAs($admin)->delete("api/v1/admin/support/articles/{$article->id}");

        $response->assertOk();
        $this->assertSoftDeleted($article);
    }

    public function test_user_is_able_to_view_an_article()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $article = Article::factory()->create();

        $response = $this->actingAs($user)->get("api/v1/support/articles/{$article->id}");

        $response->assertOk();
        $response->assertJsonFragment([
            'title' => $article->title,
            'content' => $article->content
        ]);
    }

    public function test_that_unauthenticated_users_are_not_able_to_create_an_article()
    {
        $title = $this->faker->title;
        $content = $this->faker->paragraph;

        $response = $this->postJson('api/v1/admin/support/articles', [
            'title' => $title,
            'content' => $content
        ]);

        $response->assertUnauthorized();
    }

    public function test_that_admin_able_to_retrieve_all_categories()
    {
        $admin = $this->user();
        ArticleCategory::factory(10)->create();

        $response = $this->actingAs($admin)->getJson('api/v1/support/categories');

        $response->assertJsonCount(10, 'data');
    }

    public function test_that_admin_is_able_to_retrieve_category_by_article()
    {
        $admin = $this->user();
        $articleCategories = ArticleCategory::factory(2)->create();
        Article::factory(20);
        Article::factory(10)->create([
            'category_id' => $articleCategories->first()->id
        ]);

        $response = $this->actingAs($admin)->getJson("api/v1/support/articles?category={$articleCategories->first()->id}");

        $response->assertJsonCount(10, 'data');
    }

    public function test_that_customer_is_not_able_to_create_an_article()
    {
        $admin = $this->user('customer');
        $category = ArticleCategory::factory()->create();
        $title = $this->faker->title;
        $content = $this->faker->paragraph;

        $response = $this->actingAs($admin)->postJson('api/v1/admin/support/articles', [
            'title' => $title,
            'content' => $content,
            'category_id' => $category->id,
        ]);

        $response->assertForbidden();
    }

    public function test_that_customer_is_not_able_to_update_an_article()
    {
        $admin = $this->user('customer');
        $title = $this->faker->title;
        $content = $this->faker->paragraph;
        $article = Article::factory()->create();

        $response = $this->actingAs($admin)->patch("api/v1/admin/support/articles/{$article->id}", [
            'title' => $title,
            'content' => $content
        ]);

        $response->assertForbidden();
    }

    public function test_that_client_is_not_able_to_delete_an_article()
    {
        $admin = $this->user('customer');
        $article = Article::factory()->create();

        $response = $this->actingAs($admin)->delete("api/v1/admin/support/articles/{$article->id}");

        $response->assertForbidden();
    }

    public function test_that_admin_is_able_to_create_a_category()
    {
        $admin = $this->user();
        $name = $this->faker->name;
        $description = $this->faker->paragraph;

        $response = $this->actingAs($admin)->postJson("api/v1/admin/support/categories", [
            'name' => $name,
            'description' => $description
        ]);

        $response->assertCreated();
        $response->assertJsonFragment([
            'name' => $name,
            'description' => $description,
        ]);
    }

    public function test_that_customer_is_not_able_to_create_a_category()
    {
        $admin = $this->user('customer');
        $name = $this->faker->name;
        $description = $this->faker->paragraph;

        $response = $this->actingAs($admin)->postJson("api/v1/admin/support/categories", [
            'name' => $name,
            'description' => $description
        ]);

        $response->assertForbidden();
    }
}
