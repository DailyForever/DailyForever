@extends('layouts.app')

@section('title', 'Cookies Policy - DailyForever')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-slate-50/50 to-slate-100/30 dark:from-slate-900/50 dark:to-slate-800/30">
    <div class="max-w-4xl w-full px-4">
        <div class="content-card p-8">
        <h1 class="text-3xl font-bold text-yt-text mb-4" data-i18n="legal.cookies.title">Cookies Policy</h1>
        <p class="text-yt-text-secondary mb-8 text-sm"><span data-i18n="legal.common.last_updated">Last updated:</span> September 23, 2025</p>

        <div class="bg-yt-accent/10 border border-yt-accent rounded-lg p-6 mb-8">
            <h2 class="text-lg font-semibold text-yt-accent mb-3" data-i18n="legal.cookies.banner_title">Privacy-First Cookie Usage</h2>
            <p class="text-yt-text leading-relaxed">
                DailyForever uses cookies and similar technologies in a privacy-preserving manner that respects your 
                fundamental right to privacy. We only use cookies that are essential for service operation and security, 
                and we do not use tracking cookies or analytics that compromise your privacy. Our cookie usage is 
                designed to enhance your security and user experience while maintaining our commitment to privacy protection.
            </p>
        </div>

        <div class="prose prose-invert max-w-none space-y-10">
            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">1. What Are Cookies?</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    Cookies are small text files that are stored on your device when you visit a website. They are widely used 
                    to make websites work more efficiently and to provide information to website owners. However, many cookies 
                    are used for tracking and advertising purposes that compromise user privacy.
                </p>
                <p class="text-yt-text leading-relaxed">
                    DailyForever takes a different approach, using only essential cookies that are necessary for service 
                    operation and security, while avoiding privacy-invasive tracking cookies.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">2. How DailyForever Uses Cookies</h2>
                
                <h3 class="text-xl font-medium text-yt-accent mb-4">2.1 Essential Cookies</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    We use only essential cookies that are necessary for the basic operation of our service:
                </p>
                
                <div class="bg-yt-elevated border border-yt-border rounded-lg p-6 mb-6">
                    <h4 class="text-lg font-semibold text-yt-text mb-3">Session Cookies</h4>
                    <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                        <li><strong>Purpose:</strong> Maintain your login session and security state</li>
                        <li><strong>Duration:</strong> Session only (deleted when you close your browser)</li>
                        <li><strong>Data:</strong> Encrypted session identifier, no personal information</li>
                        <li><strong>Privacy Impact:</strong> Minimal - only contains session state, no tracking data</li>
                    </ul>
                </div>

                <div class="bg-yt-elevated border border-yt-border rounded-lg p-6 mb-6">
                    <h4 class="text-lg font-semibold text-yt-text mb-3">Security Cookies</h4>
                    <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                        <li><strong>Purpose:</strong> Protect against CSRF attacks and maintain security</li>
                        <li><strong>Duration:</strong> Session only</li>
                        <li><strong>Data:</strong> CSRF tokens, security flags</li>
                        <li><strong>Privacy Impact:</strong> None - purely security-related, no personal data</li>
                    </ul>
                </div>

                <div class="bg-yt-elevated border border-yt-border rounded-lg p-6 mb-6">
                    <h4 class="text-lg font-semibold text-yt-text mb-3">Preference Cookies</h4>
                    <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                        <li><strong>Purpose:</strong> Remember your theme preference (light/dark mode)</li>
                        <li><strong>Duration:</strong> 1 year (or until you change preference)</li>
                        <li><strong>Data:</strong> Theme setting only</li>
                        <li><strong>Privacy Impact:</strong> Minimal - only contains UI preference</li>
                    </ul>
                </div>

                <h3 class="text-xl font-medium text-yt-accent mb-4">2.2 Third-Party Service Cookies</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    To support our free service, we may use third-party services that set their own cookies. 
                    These are configured to minimize data collection and protect your privacy:
                </p>
                
                <div class="bg-yt-elevated border border-yt-border rounded-lg p-6 mb-6">
                    <h4 class="text-lg font-semibold text-yt-text mb-3">Google AdSense Cookies</h4>
                    <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                        <li><strong>Purpose:</strong> Display contextual advertisements without personal data</li>
                        <li><strong>Duration:</strong> Varies (typically 30 days to 2 years)</li>
                        <li><strong>Data:</strong> Anonymous usage patterns for ad relevance</li>
                        <li><strong>Privacy Impact:</strong> Minimal - no personal identification</li>
                    </ul>
                </div>

                <div class="bg-yt-elevated border border-yt-border rounded-lg p-6 mb-6">
                    <h4 class="text-lg font-semibold text-yt-text mb-3">Google Analytics Cookies</h4>
                    <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                        <li><strong>Purpose:</strong> Understand website usage and improve service</li>
                        <li><strong>Duration:</strong> 2 years maximum</li>
                        <li><strong>Data:</strong> Anonymized website statistics and user behavior</li>
                        <li><strong>Privacy Impact:</strong> Low - data is anonymized and aggregated</li>
                    </ul>
                </div>

                <h3 class="text-xl font-medium text-yt-accent mb-4">2.3 What We Don't Use</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    We do not use the following privacy-invasive cookies:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                    <li><strong>Behavioral tracking cookies:</strong> No detailed user profiling or tracking</li>
                    <li><strong>Social media cookies:</strong> No social media integration or tracking</li>
                    <li><strong>Cross-site tracking:</strong> No tracking across multiple websites</li>
                    <li><strong>Personal data cookies:</strong> No cookies containing personal information</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">3. Cookie Categories and Legal Basis</h2>
                
                <h3 class="text-xl font-medium text-yt-accent mb-4">3.1 Strictly Necessary Cookies</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    These cookies are essential for the website to function properly and cannot be disabled. 
                    They are set in response to actions made by you which amount to a request for services, 
                    such as setting your privacy preferences, logging in, or filling in forms.
                </p>
                <div class="bg-yt-bg border border-yt-border rounded-lg p-4 mb-6">
                    <p class="text-yt-text text-sm">
                        <strong>Legal Basis:</strong> Legitimate interest in providing secure, functional services
                    </p>
                </div>

                <h3 class="text-xl font-medium text-yt-accent mb-4">3.2 Functional Cookies</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    These cookies enable the website to provide enhanced functionality and personalization. 
                    They may be set by us or by third-party providers whose services we have added to our pages.
                </p>
                <div class="bg-yt-bg border border-yt-border rounded-lg p-4 mb-6">
                    <p class="text-yt-text text-sm">
                        <strong>Legal Basis:</strong> Consent (you can opt out at any time)
                    </p>
                </div>

                <h3 class="text-xl font-medium text-yt-accent mb-4">3.3 Advertising and Analytics Cookies</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    These cookies are used by third-party services to display advertisements and analyze website usage. 
                    They are configured to minimize data collection and protect user privacy.
                </p>
                <div class="bg-yt-bg border border-yt-border rounded-lg p-4 mb-6">
                    <p class="text-yt-text text-sm">
                        <strong>Legal Basis:</strong> Consent (you can opt out at any time)
                    </p>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">4. Managing Your Cookie Preferences</h2>
                
                <h3 class="text-xl font-medium text-yt-accent mb-4">4.1 Browser Settings</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    You can control cookies through your browser settings. Most browsers allow you to:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>View which cookies are stored on your device</li>
                    <li>Delete cookies individually or all at once</li>
                    <li>Block cookies from specific websites</li>
                    <li>Block third-party cookies</li>
                    <li>Set your browser to ask before storing cookies</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">4.2 DailyForever Cookie Management</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    For cookies that require consent, you can manage your preferences:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Change your theme preference in account settings</li>
                    <li>Clear your session by logging out</li>
                    <li>Use private/incognito browsing mode</li>
                    <li>Disable cookies in your browser (may affect functionality)</li>
                </ul>

                <div class="bg-yt-warning/20 border border-yt-warning rounded-lg p-4 mb-6">
                    <h4 class="text-lg font-semibold text-yt-warning mb-2">Important Note</h4>
                    <p class="text-yt-text text-sm">
                        Disabling essential cookies may prevent DailyForever from functioning properly, as they are 
                        necessary for security and basic operation. We recommend keeping essential cookies enabled 
                        for the best experience and security.
                    </p>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">5. Third-Party Services</h2>
                
                <h3 class="text-xl font-medium text-yt-accent mb-4">5.1 CDN and Infrastructure</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    We use content delivery networks (CDNs) and infrastructure services that may set their own cookies. 
                    However, we have configured these services to minimize cookie usage and protect your privacy:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>CDN Cookies:</strong> Only for load balancing and performance, no tracking</li>
                    <li><strong>Security Services:</strong> Only for DDoS protection and security, no personal data</li>
                    <li><strong>Analytics:</strong> We do not use third-party analytics services</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">5.2 No Third-Party Tracking</h3>
                <p class="text-yt-text leading-relaxed">
                    We do not integrate with third-party tracking services, social media widgets, or advertising networks 
                    that would compromise your privacy. Any third-party services we use are configured to respect your 
                    privacy and minimize data collection.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">6. Data Protection and Privacy</h2>
                
                <h3 class="text-xl font-medium text-yt-accent mb-4">6.1 Cookie Data Protection</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    All cookies used by DailyForever are designed to protect your privacy:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Minimal Data:</strong> Only necessary information is stored in cookies</li>
                    <li><strong>Encryption:</strong> Sensitive cookie data is encrypted</li>
                    <li><strong>Short Duration:</strong> Most cookies expire quickly or are session-only</li>
                    <li><strong>No Tracking:</strong> No cookies are used for tracking or profiling</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">6.2 Your Rights</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    Under applicable data protection laws, you have the right to:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Know what cookies are being used and why</li>
                    <li>Consent to non-essential cookies</li>
                    <li>Withdraw consent at any time</li>
                    <li>Access information about cookies stored on your device</li>
                    <li>Request deletion of cookie data</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">7. Updates to This Policy</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    We may update this Cookies Policy from time to time to reflect changes in our practices or 
                    applicable laws. When we make significant changes, we will:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Update the "Last updated" date at the top of this policy</li>
                    <li>Notify users through our website or service</li>
                    <li>Obtain new consent if required by law</li>
                    <li>Maintain transparency about any changes</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    We encourage you to review this policy periodically to stay informed about our cookie practices.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">8. Contact Information</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    If you have questions about our use of cookies or this policy, please contact us:
                </p>
                <div class="bg-yt-elevated border border-yt-border rounded-lg p-6 mb-6">
                    <ul class="text-yt-text space-y-2">
                        <li><strong>Support Center:</strong> <a href="{{ route('support.index') }}" class="text-link">Contact Support</a></li>
                        <li><strong>Email:</strong> dailyforever@proton.me</li>
                        <li><strong>General Support:</strong> dailyforever@proton.me</li>
                    </ul>
                </div>
                <p class="text-yt-text leading-relaxed">
                    We are committed to addressing your privacy concerns and ensuring that our cookie usage 
                    respects your fundamental right to privacy.
                </p>
            </section>

            <section class="bg-yt-accent/5 border border-yt-accent rounded-lg p-6">
                <h2 class="text-2xl font-semibold text-yt-accent mb-4">Related Policies</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    For more information about our privacy practices, please review:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6">
                    <li><a href="{{ route('legal.privacy') }}" class="text-link" data-i18n="support.info.quick.privacy">Privacy Policy</a></li>
                    <li><a href="{{ route('legal.terms') }}" class="text-link" data-i18n="support.info.quick.terms">Terms of Service</a></li>
                    <li><a href="{{ route('legal.no-logs') }}" class="text-link" data-i18n="support.info.quick.no_logs">No Logs Policy</a></li>
                    <li><a href="{{ route('legal.philosophy') }}" class="text-link" data-i18n="support.info.quick.philosophy">Our Philosophy</a></li>
                </ul>
            </section>
        </div>
    </div>
</div>
</div>
@endsection
