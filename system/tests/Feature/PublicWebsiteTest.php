<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Service;
use App\Services\FileUploader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicWebsiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_listings_and_empty_states_render(): void
    {
        foreach (['/', '/home-2.html', '/about.html', '/services.html', '/team.html', '/portfolio.html', '/case-studies.html', '/packages.html', '/faq.html', '/careers.html', '/blog.html', '/contact.html', '/search', '/404.html', '/sitemap.xml', '/admin/login', '/admin/forgot-password'] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_unpublished_and_missing_content_is_not_public(): void
    {
        Service::create(['title' => 'Hidden', 'slug' => 'hidden', 'status' => false]);
        Blog::create(['title' => 'Future', 'slug' => 'future', 'status' => 'published', 'content' => 'Test article body', 'published_at' => now()->addDay()]);
        foreach (['/service/hidden', '/blog/future', '/portfolio/missing', '/case-study/missing', '/team/999', '/page/missing', '/does-not-exist'] as $path) {
            $this->get($path)->assertNotFound();
        }
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/blog-details.html')->assertRedirect('/blog.html');
    }

    public function test_search_rejects_arrays_and_overlong_terms_without_server_errors(): void
    {
        $this->get('/search?q[]=web')->assertStatus(400);
        $this->get('/search?q='.str_repeat('x', 201))->assertStatus(400);
        Service::create(['title' => 'Searchable service', 'slug' => 'searchable', 'status' => true]);
        $this->get('/search?q=Searchable')->assertOk()->assertSee('Searchable service');
    }

    public function test_nested_menu_queries_do_not_grow_with_partial_count(): void
    {
        $menu = Menu::create(['name' => 'Header', 'slug' => 'header', 'location' => 'header', 'status' => true]);
        $parent = null;
        for ($i = 0; $i < 5; $i++) {
            $parent = MenuItem::create(['menu_id' => $menu->id, 'parent_id' => $parent?->id, 'label' => 'Level '.$i, 'url' => '/about.html', 'status' => true]);
        }
        DB::enableQueryLog();
        $this->get('/')->assertOk()->assertSee('Level 4');
        $menuQueries = collect(DB::getQueryLog())->filter(fn ($query) => str_contains($query['query'], '"menus"') || str_contains($query['query'], '"menu_items"'));
        $this->assertCount(2, $menuQueries);
        DB::disableQueryLog();
    }

    public function test_blog_pagination_has_distinct_pages(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            Blog::create(['title' => 'Article '.$i, 'slug' => 'article-'.$i, 'status' => 'published', 'content' => 'Test article body', 'published_at' => now()->subDays($i)]);
        }
        $this->get('/blog.html')->assertOk()->assertSee('Next page')->assertDontSee('Article 10');
        $this->get('/blog.html?page=2')->assertOk()->assertSee('Article 10')->assertSee('aria-current="page"', false);
    }

    public function test_inquiry_validation_and_success_without_outbound_messages(): void
    {
        Mail::fake();
        Notification::fake();
        $this->from('/contact.html')->post('/contact/send', ['name' => 'Test visitor'])->assertSessionHasErrors('email');
        $this->from('/contact.html')->post('/contact/send', ['name' => 'Test visitor', 'email' => 'visitor@example.test', 'website_url' => 'spam'])->assertSessionHasErrors('website_url');
        $this->assertDatabaseCount('website_inquiries', 0);
        $this->from('/contact.html')->post('/contact/send', ['name' => 'Test visitor', 'email' => 'visitor@example.test', 'message' => 'Test inquiry'])->assertRedirect('/contact.html')->assertSessionHas('inquiry_success');
        $this->assertDatabaseHas('website_inquiries', ['email' => 'visitor@example.test']);
    }

    public function test_upload_extension_is_inferred_from_content(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->createWithContent('picture.php', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aX1sAAAAASUVORK5CYII='));
        $realFile = new UploadedFile($file->getPathname(), 'picture.php', 'application/x-php', null, true);
        $path = app(FileUploader::class)->store($realFile, 'audit');
        $this->assertStringEndsWith('.png', $path);
        Storage::disk('public')->assertExists($path);
    }
}
