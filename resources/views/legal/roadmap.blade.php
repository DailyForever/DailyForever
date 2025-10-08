@extends('layouts.app')

@section('title', 'Product roadmap - DailyForever')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-slate-50/50 to-slate-100/30 dark:from-slate-900/50 dark:to-slate-800/30">
    <div class="max-w-4xl w-full px-4">
        <div class="content-card p-8">
            <h1 class="text-3xl font-bold text-yt-text mb-2">Product roadmap</h1>
            <p class="text-yt-text-secondary mb-8 text-sm">Last updated: October 4, 2025 <span class="mx-2">•</span> Current version: v2.0</p>

            <div class="bg-yt-accent/10 border border-yt-accent rounded-lg p-6 mb-10">
                <h2 class="text-lg font-semibold text-yt-accent mb-3">Our Mission</h2>
                <p class="text-yt-text leading-relaxed">
                    Building the most secure and accessible encrypted sharing platform. Zero-knowledge architecture, 
                    client-side encryption, and no data collection. This roadmap reflects our commitment to incremental 
                    improvements based on user needs and technical capabilities.
                </p>
            </div>

            <section class="mb-10">
                <h2 class="text-2xl font-semibold text-yt-text mb-4">Current State Analysis</h2>
                <p class="text-yt-text-secondary mb-4">Based on codebase audit performed October 2025</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-yt-surface rounded-lg p-4 border border-yt-border">
                        <h3 class="text-lg font-semibold text-yt-text mb-2">Strengths</h3>
                        <ul class="list-disc list-inside text-yt-text-secondary space-y-1 text-sm">
                            <li>encryption (AES-256-GCM)</li>
                            <li>SRP-6a authentication working</li>
                            <li>160MB file uploads with chunking</li>
                            <li>Post-quantum ready (ML-KEM)</li>
                            <li>Admin dashboard functional</li>
                            <li>Basic i18n (EN/ES)</li>
                        </ul>
                    </div>
                    <div class="bg-yt-surface rounded-lg p-4 border border-yt-border">
                        <h3 class="text-lg font-semibold text-yt-text mb-2">Gaps</h3>
                        <ul class="list-disc list-inside text-yt-text-secondary space-y-1 text-sm">
                            <li>Spanish translation incomplete</li>
                            <li>Missing i18n.js implementation</li>
                            <li>No API documentation</li>
                            <li>Limited mobile responsiveness</li>
                            <li>CSP headers not configured</li>
                            <li>No automated testing</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-semibold text-yt-text mb-4">Immediate Priorities (Q4 2025)</h2>
                <div class="grid grid-cols-1 gap-4">
                    <div class="bg-green-900/20 border border-green-500/30 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-yt-text mb-2">Complete Spanish Translation</h3>
                        <p class="text-yt-text-secondary mb-2">Finish translating remaining UI elements and error messages to Spanish</p>
                        <div class="flex items-center gap-2 mt-3">
                            <div class="flex-1 bg-yt-surface rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: 70%"></div>
                            </div>
                            <span class="text-xs text-yt-text-secondary">70% complete</span>
                        </div>
                    </div>
                    <div class="bg-blue-900/20 border border-blue-500/30 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-yt-text mb-2">Fix i18n Implementation</h3>
                        <p class="text-yt-text-secondary">Create missing i18n.js file and properly integrate translation system</p>
                        <span class="inline-block text-xs px-3 py-1 bg-blue-500/20 text-blue-400 rounded-full mt-2">Critical</span>
                    </div>
                    <div class="bg-yt-surface rounded-lg p-6 border border-yt-border">
                        <h3 class="text-lg font-semibold text-yt-text mb-2">Security Headers</h3>
                        <p class="text-yt-text-secondary">Implement CSP, HSTS, X-Frame-Options, and other security headers</p>
                        <span class="inline-block text-xs px-3 py-1 bg-yt-accent/20 text-yt-accent rounded-full mt-2">High Priority</span>
                    </div>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-semibold text-yt-text mb-4">Q1 2026 Targets</h2>
                <div class="grid grid-cols-1 gap-4">
                    <div class="bg-yt-surface rounded-lg p-6 border border-yt-border">
                        <h3 class="text-lg font-semibold text-yt-text mb-2">Language Pack 1</h3>
                        <p class="text-yt-text-secondary mb-2">Add French, German, and Portuguese translations</p>
                        <ul class="list-disc list-inside text-yt-text-secondary text-sm mt-2">
                            <li>Professional translation service for accuracy</li>
                            <li>Community review for context</li>
                            <li>RTL support preparation</li>
                        </ul>
                    </div>
                    <div class="bg-yt-surface rounded-lg p-6 border border-yt-border">
                        <h3 class="text-lg font-semibold text-yt-text mb-2">Mobile-First Responsive Design</h3>
                        <p class="text-yt-text-secondary mb-2">Complete mobile UI overhaul before native apps</p>
                        <ul class="list-disc list-inside text-yt-text-secondary text-sm mt-2">
                            <li>PWA capabilities</li>
                            <li>Touch-optimized interactions</li>
                            <li>Offline mode with service workers</li>
                        </ul>
                    </div>
                    <div class="bg-yt-surface rounded-lg p-6 border border-yt-border">
                        <h3 class="text-lg font-semibold text-yt-text mb-2">API Documentation</h3>
                        <p class="text-yt-text-secondary mb-2">Public API with comprehensive docs</p>
                        <ul class="list-disc list-inside text-yt-text-secondary text-sm mt-2">
                            <li>OpenAPI/Swagger specification</li>
                            <li>Rate limiting documentation</li>
                            <li>Example implementations</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-semibold text-yt-text mb-4">Q2 2026 Roadmap</h2>
                <div class="grid grid-cols-1 gap-4">
                    <div class="bg-yt-surface rounded-lg p-6 border border-yt-border">
                        <h3 class="text-lg font-semibold text-yt-text mb-2">Language Pack 2</h3>
                        <p class="text-yt-text-secondary mb-2">Add Chinese (Simplified/Traditional), Japanese, and Arabic</p>
                        <ul class="list-disc list-inside text-yt-text-secondary text-sm mt-2">
                            <li>Full RTL support for Arabic</li>
                            <li>Asian font optimization</li>
                            <li>Date/time localization</li>
                        </ul>
                    </div>
                    <div class="bg-yt-surface rounded-lg p-6 border border-yt-border">
                        <h3 class="text-lg font-semibold text-yt-text mb-2">Performance Optimization</h3>
                        <p class="text-yt-text-secondary mb-2">Speed improvements and resource optimization</p>
                        <ul class="list-disc list-inside text-yt-text-secondary text-sm mt-2">
                            <li>Lazy loading for large files</li>
                            <li>CDN integration</li>
                            <li>Database query optimization</li>
                            <li>Caching strategy implementation</li>
                        </ul>
                    </div>
                    <div class="bg-yt-surface rounded-lg p-6 border border-yt-border">
                        <h3 class="text-lg font-semibold text-yt-text mb-2">Testing Framework</h3>
                        <p class="text-yt-text-secondary mb-2">Comprehensive testing suite</p>
                        <ul class="list-disc list-inside text-yt-text-secondary text-sm mt-2">
                            <li>Unit tests for critical functions</li>
                            <li>Integration tests for API</li>
                            <li>E2E tests for user flows</li>
                            <li>Security penetration testing</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-semibold text-yt-text mb-4">Q3-Q4 2026 - Mobile Apps</h2>
                <div class="grid grid-cols-1 gap-4">
                    <div class="bg-yt-surface rounded-lg p-6 border border-yt-border">
                        <h3 class="text-lg font-semibold text-yt-text mb-2">iOS Native App</h3>
                        <p class="text-yt-text-secondary mb-2">Native Swift application with full feature parity</p>
                        <ul class="list-disc list-inside text-yt-text-secondary text-sm mt-2">
                            <li>Local encryption using CryptoKit</li>
                            <li>Biometric authentication</li>
                            <li>Share extension for quick paste</li>
                            <li>Offline mode with sync</li>
                        </ul>
                        <span class="inline-block text-xs px-3 py-1 bg-yt-accent/20 text-yt-accent rounded-full mt-2">Target: Q3 2026</span>
                    </div>
                    <div class="bg-yt-surface rounded-lg p-6 border border-yt-border">
                        <h3 class="text-lg font-semibold text-yt-text mb-2">Android Native App</h3>
                        <p class="text-yt-text-secondary mb-2">Native Kotlin application matching iOS features</p>
                        <ul class="list-disc list-inside text-yt-text-secondary text-sm mt-2">
                            <li>Android Keystore integration</li>
                            <li>Material Design 3 UI</li>
                            <li>Intent sharing support</li>
                            <li>Background sync service</li>
                        </ul>
                        <span class="inline-block text-xs px-3 py-1 bg-yt-accent/20 text-yt-accent rounded-full mt-2">Target: Q4 2026</span>
                    </div>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-semibold text-yt-text mb-4">Future Considerations</h2>
                <div class="bg-yt-surface rounded-lg p-6 border border-yt-border">
                    <h3 class="text-lg font-semibold text-yt-text mb-3">Long-term Vision Items</h3>
                    <ul class="list-disc list-inside text-yt-text-secondary space-y-2">
                        <li>Browser extensions (Chrome, Firefox, Safari)</li>
                        <li>Desktop applications (Electron-based)</li>
                        <li>CLI tool for developers</li>
                        <li>Team collaboration features</li>
                        <li>Self-hosted enterprise version</li>
                        <li>Advanced analytics dashboard</li>
                        <li>Webhook integrations</li>
                        <li>2FA hardware key support (FIDO2)</li>
                    </ul>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-semibold text-yt-text mb-4">Success Metrics</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-yt-surface rounded-lg p-4 border border-yt-border text-center">
                        <div class="text-2xl font-bold text-yt-accent">99.9%</div>
                        <div class="text-sm text-yt-text-secondary">Uptime Target</div>
                    </div>
                    <div class="bg-yt-surface rounded-lg p-4 border border-yt-border text-center">
                        <div class="text-2xl font-bold text-yt-accent"><2s</div>
                        <div class="text-sm text-yt-text-secondary">Page Load Time</div>
                    </div>
                    <div class="bg-yt-surface rounded-lg p-4 border border-yt-border text-center">
                        <div class="text-2xl font-bold text-yt-accent">10+</div>
                        <div class="text-sm text-yt-text-secondary">Languages by 2027</div>
                    </div>
                </div>
            </section>

            <section>
                <div class="bg-yt-accent/10 border border-yt-accent rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-yt-text mb-3">Community Feedback</h3>
                    <p class="text-yt-text leading-relaxed mb-4">
                        This roadmap is based on user feedback and technical analysis. Your input helps us prioritize features 
                        and improvements. Please share your thoughts through our support system or contribute on GitHub.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('support.index') }}" class="inline-flex items-center px-4 py-2 bg-yt-accent text-white rounded-lg hover:bg-yt-accent/80 transition-colors">
                            Send Feedback
                        </a>
                        <a href="https://github.com/DailyForever/DailyForever" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-4 py-2 bg-yt-surface text-yt-text rounded-lg hover:bg-yt-surface/80 transition-colors">
                            Contribute on GitHub
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
