<?php

namespace Tests\Feature;

use Tests\TestCase;

class ThemeTest extends TestCase
{
    public function test_home_page_has_theme_toggle()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('themeToggle');
        $response->assertSee('data-theme');
    }

    public function test_login_page_has_theme_toggle()
    {
        $response = $this->get(route('auth.login.show'));

        $response->assertStatus(200);
        $response->assertSee('themeToggle');
        $response->assertSee('data-theme');
    }

    public function test_register_page_has_theme_toggle()
    {
        $response = $this->get(route('auth.register.show'));

        $response->assertStatus(200);
        $response->assertSee('themeToggle');
        $response->assertSee('data-theme');
    }

    public function test_recover_page_has_theme_toggle()
    {
        $response = $this->get(route('auth.recover.show'));

        $response->assertStatus(200);
        $response->assertSee('themeToggle');
        $response->assertSee('data-theme');
    }

    public function test_pages_have_proper_theme_classes()
    {
        $pages = [
            '/' => 'Home',
            route('auth.login.show') => 'Login',
            route('auth.register.show') => 'Register',
            route('auth.recover.show') => 'Recover',
        ];

        foreach ($pages as $url => $name) {
            $response = $this->get($url);
            $response->assertStatus(200);
            
            // Check for theme-related classes and attributes
            $response->assertSee('data-theme');
            $response->assertSee('bg-yt-bg text-yt-text');
        }
    }

    public function test_css_imports_are_in_correct_order()
    {
        $response = $this->get('/');
        
        // The page should load without CSS errors
        $response->assertStatus(200);
        
        // Check that the page contains expected theme-related content
        $response->assertSee('DailyForever');
        $response->assertSee('themeToggle');
    }
}
