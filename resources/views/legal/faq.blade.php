@extends('layouts.app')

@section('title', 'Frequently Asked Questions - DailyForever')
@section('meta_description', 'Find answers to common questions about DailyForever\'s encrypted pastebin service. Learn about security features, privacy protection, and how to use our platform.')
@section('keywords', 'faq, frequently asked questions, help, support, encrypted pastebin, secure file sharing, zero-knowledge encryption, privacy questions')

@section('faq_schema')
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [
        [
            '@type' => 'Question',
            'name' => 'What is DailyForever?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'DailyForever is a zero-knowledge encrypted platform that provides secure sharing of text content and files. We believe that privacy and encryption are fundamental human rights, and our platform is designed to protect these rights through client-side encryption, zero-knowledge architecture, and minimal data collection.'
            ]
        ],
        [
            '@type' => 'Question',
            'name' => 'How does DailyForever protect my privacy?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'We implement multiple layers of privacy protection: Client-side encryption (all content is encrypted on your device before transmission), Zero-knowledge architecture (we cannot access your unencrypted content), Minimal data collection (we only collect data necessary for service operation), No logging policy (we do not log user activity or content), and Post-quantum ready (ML-KEM 512 encryption for future quantum resistance).'
            ]
        ],
        [
            '@type' => 'Question',
            'name' => 'Is DailyForever free to use?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'Yes, DailyForever is completely free to use. We believe that privacy tools should be accessible to everyone, regardless of their economic circumstances. To support our free service, we may display advertisements and use analytics services, but these are implemented in a privacy-preserving manner. Any advertising or analytics do not compromise the zero-knowledge architecture of our core encryption services.'
            ]
        ],
        [
            '@type' => 'Question',
            'name' => 'Do I need to create an account?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'No, you can use DailyForever without creating an account. However, creating an account provides additional features: Private pastes and files (visible only to you when logged in), Two-factor authentication for enhanced security, Post-quantum prekey management, and Account recovery options. Account creation is optional and designed to enhance your privacy and security, not to collect personal data.'
            ]
        ],
        [
            '@type' => 'Question',
            'name' => 'What encryption does DailyForever use?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'We use industry-standard encryption protocols: AES-GCM 256-bit for content encryption, Argon2id for password hashing, ML-KEM 512 for post-quantum key encapsulation, and TOTP for two-factor authentication. All encryption is performed client-side, meaning your content is encrypted before it leaves your device.'
            ]
        ],
        [
            '@type' => 'Question',
            'name' => 'Can DailyForever read my encrypted content?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'No, DailyForever cannot read your encrypted content. Our zero-knowledge architecture ensures that encryption keys are never sent to our servers. Content is encrypted on your device, and only you and those you share the decryption key with can access it.'
            ]
        ],
        [
            '@type' => 'Question',
            'name' => 'How long is my content stored?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'You can set custom expiration times for your content: Never (content remains until manually deleted), 1 hour, 1 day, 1 week, or 1 month. You can also enable \'view once\' mode, where content is automatically deleted after being viewed once.'
            ]
        ],
        [
            '@type' => 'Question',
            'name' => 'What is the maximum file size I can upload?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'The maximum file size for encrypted uploads is 100MB. Files are encrypted client-side before upload, ensuring that we never have access to your unencrypted file content.'
            ]
        ]
    ]
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
@endsection

@section('breadcrumbs')
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => url('/')
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => 'FAQ',
            'item' => url('/faq')
        ]
    ]
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
@endsection

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-slate-50/50 to-slate-100/30 dark:from-slate-900/50 dark:to-slate-800/30">
    <div class="max-w-4xl w-full px-4">
        <div class="content-card p-8">
        <h1 class="text-3xl font-bold text-yt-text mb-4" data-i18n="legal.faq.title">Frequently Asked Questions</h1>
        <p class="text-yt-text-secondary mb-8 text-sm"><span data-i18n="legal.common.last_updated">Last updated:</span> September 23, 2025</p>

        <div class="bg-yt-accent/10 border border-yt-accent rounded-lg p-6 mb-8">
            <h2 class="text-lg font-semibold text-yt-accent mb-3" data-i18n="legal.faq.banner_title">Privacy-First Design Philosophy</h2>
            <p class="text-yt-text leading-relaxed">
                DailyForever is built on the fundamental principle that privacy and encryption are universal human rights. 
                Our FAQ addresses common questions while emphasizing our commitment to protecting these rights through 
                technical measures and transparent practices. Every feature is designed with privacy by design principles.
            </p>
        </div>

        <div class="prose prose-invert max-w-none space-y-10">
            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">General Questions</h2>
                
                <div class="space-y-8">
                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">What is DailyForever?</h3>
                        <p class="text-yt-text leading-relaxed mb-4">
                            DailyForever is a zero-knowledge encrypted platform that provides secure sharing of text content and files. 
                            We believe that privacy and encryption are fundamental human rights, and our platform is designed to 
                            protect these rights through client-side encryption, zero-knowledge architecture, and minimal data collection.
                        </p>
                        <p class="text-yt-text leading-relaxed">
                            Our service allows users to share sensitive information securely without compromising their privacy, 
                            using industry-standard encryption and privacy-preserving technologies.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">How does DailyForever protect my privacy?</h3>
                        <p class="text-yt-text leading-relaxed mb-4">
                            We implement multiple layers of privacy protection:
                        </p>
                        <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                            <li><strong>Client-side encryption:</strong> All content is encrypted on your device before transmission</li>
                            <li><strong>Zero-knowledge architecture:</strong> We cannot access your unencrypted content</li>
                            <li><strong>Minimal data collection:</strong> We only collect data necessary for service operation</li>
                            <li><strong>No logging policy:</strong> We do not log user activity or content</li>
                            <li><strong>Post-quantum ready:</strong> ML-KEM 512 encryption for future quantum resistance</li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">Is DailyForever free to use?</h3>
                        <p class="text-yt-text leading-relaxed mb-4">
                            Yes, DailyForever is completely free to use. We believe that privacy tools should be accessible to everyone, 
                            regardless of their economic circumstances. To support our free service, we may display advertisements 
                            and use analytics services, but these are implemented in a privacy-preserving manner.
                        </p>
                        <p class="text-yt-text leading-relaxed">
                            <strong>Important:</strong> Any advertising or analytics do not compromise the zero-knowledge architecture 
                            of our core encryption services. Your encrypted content remains private and secure.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">Do I need to create an account?</h3>
                        <p class="text-yt-text leading-relaxed mb-4">
                            No, you can use DailyForever without creating an account. However, creating an account provides additional features:
                        </p>
                        <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                            <li>Private pastes and files (visible only to you when logged in)</li>
                            <li>Two-factor authentication for enhanced security</li>
                            <li>Post-quantum prekey management</li>
                            <li>Account recovery options</li>
                        </ul>
                        <p class="text-yt-text leading-relaxed">
                            Account creation is optional and designed to enhance your privacy and security, not to collect personal data.
                        </p>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">Security and Encryption</h2>
                
                <div class="space-y-8">
                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">What encryption does DailyForever use?</h3>
                        <p class="text-yt-text leading-relaxed mb-4">
                            We use industry-standard encryption protocols:
                        </p>
                        <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                            <li><strong>AES-GCM 256-bit:</strong> For content encryption</li>
                            <li><strong>Argon2id:</strong> For password hashing</li>
                            <li><strong>ML-KEM 512:</strong> Post-quantum key encapsulation</li>
                            <li><strong>TOTP:</strong> For two-factor authentication</li>
                        </ul>
                        <p class="text-yt-text leading-relaxed">
                            All encryption is performed client-side, meaning your content is encrypted before it leaves your device.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">Can DailyForever decrypt my content?</h3>
                        <p class="text-yt-text leading-relaxed">
                            No, we cannot decrypt your content. Our zero-knowledge architecture ensures that:
                        </p>
                        <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                            <li>Encryption keys are generated on your device</li>
                            <li>Keys are only transmitted to our servers when owners choose to store them for convenient access</li>
                            <li>We only store encrypted ciphertext</li>
                            <li>We have no technical means to decrypt your content</li>
                        </ul>
                        <p class="text-yt-text leading-relaxed">
                            This is a fundamental design principle that protects your privacy even if our servers are compromised.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">What is post-quantum encryption?</h3>
                        <p class="text-yt-text leading-relaxed mb-4">
                            Post-quantum encryption refers to cryptographic algorithms that are resistant to attacks from quantum computers. 
                            We implement ML-KEM 512 (Module-Lattice-based Key Encapsulation Mechanism) to ensure your content remains 
                            secure even when quantum computers become available.
                        </p>
                        <p class="text-yt-text leading-relaxed">
                            This forward-looking approach ensures that your privacy is protected not just today, but in the quantum computing era.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">How secure is the password protection?</h3>
                        <p class="text-yt-text leading-relaxed">
                            Password protection uses Argon2id hashing, which is considered the gold standard for password security. 
                            Passwords are hashed on the server using a secure random salt, making them extremely difficult to crack 
                            even with modern computing power. We never store plaintext passwords.
                        </p>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">Privacy and Data Protection</h2>
                
                <div class="space-y-8">
                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">What data does DailyForever collect?</h3>
                        <p class="text-yt-text leading-relaxed mb-4">
                            We collect minimal data necessary for service operation:
                        </p>
                        <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                            <li><strong>Account data:</strong> Username, email (if provided), encrypted password hash</li>
                            <li><strong>Service data:</strong> Encrypted content, metadata (expiration, view limits)</li>
                            <li><strong>Technical data:</strong> IP addresses (for security), user agent (for compatibility)</li>
                            <li><strong>No content logging:</strong> We do not log or store unencrypted content</li>
                        </ul>
                        <p class="text-yt-text leading-relaxed">
                            We follow the principle of data minimization, collecting only what is absolutely necessary.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">Do you log user activity?</h3>
                        <p class="text-yt-text leading-relaxed">
                            No, we do not log user activity. Our no-logs policy means we do not track:
                        </p>
                        <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                            <li>What content you create or view</li>
                            <li>When you access the service</li>
                            <li>Your browsing patterns or behavior</li>
                            <li>Any personal information beyond what's necessary</li>
                        </ul>
                        <p class="text-yt-text leading-relaxed">
                            This policy is technically enforced, not just a promise, ensuring your privacy is protected by design.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">Can you see my content?</h3>
                        <p class="text-yt-text leading-relaxed">
                            No, we cannot see your content. Our zero-knowledge architecture ensures that:
                        </p>
                        <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                            <li>Content is encrypted on your device before transmission</li>
                            <li>We only receive encrypted ciphertext</li>
                            <li>We have no access to encryption keys</li>
                            <li>We cannot decrypt your content even if we wanted to</li>
                        </ul>
                        <p class="text-yt-text leading-relaxed">
                            This is a fundamental technical limitation, not just a policy choice.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">Can I store my encryption keys for easier access?</h3>
                        <p class="text-yt-text leading-relaxed mb-4">
                            Yes! As a registered user, you can choose to store your encryption keys for convenient access to your own pastes:
                        </p>
                        <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                            <li><strong>Automatic Storage:</strong> Keys are automatically stored when you create pastes while logged in</li>
                            <li><strong>Owner-Only Access:</strong> Only you can access your stored keys</li>
                            <li><strong>Convenient Decryption:</strong> Your pastes automatically decrypt when you view them</li>
                            <li><strong>Optional Feature:</strong> You can still use the traditional URL-based key method</li>
                        </ul>
                        <p class="text-yt-text leading-relaxed">
                            This feature makes it easier to manage your pastes while maintaining the same security level.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">How long is my data stored?</h3>
                        <p class="text-yt-text leading-relaxed mb-4">
                            Data retention follows strict privacy principles:
                        </p>
                        <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                            <li><strong>Content:</strong> Automatically deleted after expiration or view limit</li>
                            <li><strong>Account data:</strong> Retained only while account is active</li>
                            <li><strong>Logs:</strong> No user activity logs are created</li>
                            <li><strong>Backups:</strong> Encrypted backups are automatically purged</li>
                        </ul>
                        <p class="text-yt-text leading-relaxed">
                            We implement automatic data purging to minimize data retention and protect your privacy.
                        </p>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">Technical Questions</h2>
                
                <div class="space-y-8">
                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">What file types can I upload?</h3>
                        <p class="text-yt-text leading-relaxed mb-4">
                            You can upload any file type up to 160MB. All files are encrypted client-side before upload, 
                            ensuring that even we cannot determine the file type or content. Supported features include:
                        </p>
                        <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                            <li>Any file format (documents, images, videos, archives, etc.)</li>
                            <li>Maximum file size: 160MB</li>
                            <li>Client-side encryption for all files</li>
                            <li>Password protection and view limits</li>
                            <li>Private files (visible only to you when logged in)</li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">How do view limits work?</h3>
                        <p class="text-yt-text leading-relaxed">
                            View limits allow you to set how many times your content can be accessed before it's automatically deleted. 
                            This provides additional privacy protection by ensuring content doesn't remain accessible indefinitely. 
                            Once the limit is reached, the content is permanently deleted from our servers.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">What happens if I forget my password?</h3>
                        <p class="text-yt-text leading-relaxed mb-4">
                            For account passwords, you have multiple recovery options:
                        </p>
                        <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                            <li><strong>Backup Code:</strong> Use your 16-character backup code to log in (changes after each use)</li>
                            <li><strong>Email Recovery:</strong> If you've added an email, use email-based recovery</li>
                            <li><strong>PIN Recovery:</strong> Use your PIN code for account recovery</li>
                        </ul>
                        <p class="text-yt-text leading-relaxed mb-4">
                            However, for content passwords:
                        </p>
                        <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                            <li>We cannot recover content passwords (by design)</li>
                            <li>Content passwords are not stored on our servers</li>
                            <li>This ensures that only you can access your content</li>
                            <li>We recommend using a password manager for content passwords</li>
                        </ul>
                        <p class="text-yt-text leading-relaxed">
                            This limitation is intentional and protects your privacy by ensuring we cannot access your content.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">What are backup codes and how do they work?</h3>
                        <p class="text-yt-text leading-relaxed mb-4">
                            Backup codes are 16-character alphanumeric codes that allow you to log in without your password. 
                            They provide an additional layer of security and convenience:
                        </p>
                        <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                            <li><strong>Automatic Generation:</strong> A new backup code is created when you register</li>
                            <li><strong>Smart Login:</strong> Our login system automatically detects if you're entering a password or backup code</li>
                            <li><strong>Rotation:</strong> Backup codes change every time you log in using one</li>
                            <li><strong>Encrypted Storage:</strong> Backup codes are encrypted using Laravel's encryption system</li>
                            <li><strong>Secure Access:</strong> You can use backup codes to access your account from any device</li>
                        </ul>
                        <p class="text-yt-text leading-relaxed">
                            Store your backup codes safely as they change with each login. They provide a secure alternative to password-based authentication.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">Is DailyForever open source?</h3>
                        <p class="text-yt-text leading-relaxed">
                            While we are not currently open source, we are committed to transparency and security. 
                            We regularly publish security audits, use well-established open source cryptographic libraries, 
                            and are working toward making our codebase open source to allow community verification of our privacy claims.
                        </p>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">Support and Legal</h2>
                
                <div class="space-y-8">
                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">How can I get help or report issues?</h3>
                        <p class="text-yt-text leading-relaxed mb-4">
                            We provide comprehensive support through our Support Center:
                        </p>
                        <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                            <li><strong>General Support:</strong> Technical questions and account issues</li>
                            <li><strong>Security Reports:</strong> Vulnerability disclosures and security concerns</li>
                            <li><strong>DMCA Notices:</strong> Copyright infringement reports</li>
                            <li><strong>Abuse Reports:</strong> Policy violations and inappropriate content</li>
                            <li><strong>Policy Appeals:</strong> Content removal appeals</li>
                        </ul>
                        <p class="text-yt-text leading-relaxed">
                            Visit our <a href="{{ route('support.index') }}" class="text-link">Support Center</a> for assistance.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">What is your response time for support?</h3>
                        <p class="text-yt-text leading-relaxed mb-4">
                            We are committed to timely responses based on the nature of your inquiry:
                        </p>
                        <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                            <li><strong>Security Issues:</strong> 12-24 hours</li>
                            <li><strong>DMCA Notices:</strong> 24-48 hours</li>
                            <li><strong>Abuse Reports:</strong> 24-48 hours</li>
                            <li><strong>General Support:</strong> 48-72 hours</li>
                            <li><strong>Policy Appeals:</strong> 5-7 business days</li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">How do you handle legal requests?</h3>
                        <p class="text-yt-text leading-relaxed">
                            We handle legal requests in accordance with applicable law while protecting user privacy. 
                            Due to our zero-knowledge architecture, we cannot provide unencrypted content even with legal requests. 
                            We will work with law enforcement within the bounds of the law while maintaining our commitment to user privacy.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">Can I use DailyForever for commercial purposes?</h3>
                        <p class="text-yt-text leading-relaxed">
                            Yes, you can use DailyForever for commercial purposes, including business communications, 
                            confidential document sharing, and other legitimate business needs. We believe that privacy 
                            tools should be available for all legitimate uses, including commercial activities.
                        </p>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">Advertising and Analytics</h2>
                
                <div class="space-y-8">
                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">Does DailyForever show advertisements?</h3>
                        <p class="text-yt-text leading-relaxed mb-4">
                            To support our free service, we may display advertisements through Google AdSense. 
                            These advertisements are:
                        </p>
                        <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                            <li>Contextual and non-personalized</li>
                            <li>Configured to minimize data collection</li>
                            <li>Separate from our core encryption services</li>
                            <li>Subject to your cookie preferences</li>
                        </ul>
                        <p class="text-yt-text leading-relaxed">
                            Advertisements do not affect the privacy or security of your encrypted content.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">Does DailyForever use Google Analytics?</h3>
                        <p class="text-yt-text leading-relaxed mb-4">
                            We may use Google Analytics to understand how our service is used and improve user experience. 
                            Our implementation is designed to protect privacy:
                        </p>
                        <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                            <li>Data is anonymized and aggregated</li>
                            <li>No personal information is collected</li>
                            <li>IP addresses are anonymized</li>
                            <li>You can opt out through cookie settings</li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">How do advertisements and analytics affect my privacy?</h3>
                        <p class="text-yt-text leading-relaxed">
                            Advertisements and analytics do not compromise your core privacy protections:
                        </p>
                        <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                            <li>Your encrypted content remains private and secure</li>
                            <li>Zero-knowledge architecture is not affected</li>
                            <li>Third-party services cannot access your encrypted data</li>
                            <li>You maintain control over your data and preferences</li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">Can I opt out of advertisements and analytics?</h3>
                        <p class="text-yt-text leading-relaxed">
                            Yes, you can control your privacy preferences:
                        </p>
                        <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                            <li>Disable non-essential cookies in your browser</li>
                            <li>Use ad blockers or privacy-focused browsers</li>
                            <li>Opt out through Google's ad settings</li>
                            <li>Use private/incognito browsing mode</li>
                        </ul>
                        <p class="text-yt-text leading-relaxed">
                            Note that disabling essential cookies may affect service functionality.
                        </p>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">Privacy Rights and Philosophy</h2>
                
                <div class="space-y-8">
                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">Why do you believe privacy is a human right?</h3>
                        <p class="text-yt-text leading-relaxed mb-4">
                            We believe privacy is a fundamental human right because:
                        </p>
                        <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                            <li>It enables freedom of expression and thought</li>
                            <li>It protects individuals from surveillance and oppression</li>
                            <li>It allows for personal development and autonomy</li>
                            <li>It is essential for democratic societies</li>
                            <li>It protects vulnerable populations from harm</li>
                        </ul>
                        <p class="text-yt-text leading-relaxed">
                            Privacy is not a luxury or privilege, but a fundamental right that must be protected for all people.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">How do you balance privacy with security?</h3>
                        <p class="text-yt-text leading-relaxed">
                            We believe that privacy and security are complementary, not opposing forces. Strong encryption and 
                            privacy-preserving technologies actually enhance security by protecting against unauthorized access. 
                            Our approach is to implement security measures that protect privacy rather than compromise it.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-medium text-yt-accent mb-3">What makes DailyForever different from other services?</h3>
                        <p class="text-yt-text leading-relaxed mb-4">
                            DailyForever is different because:
                        </p>
                        <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                            <li>We treat privacy as a fundamental human right, not a feature</li>
                            <li>We implement zero-knowledge architecture by design</li>
                            <li>We are post-quantum ready for future security</li>
                            <li>We provide comprehensive support for privacy rights</li>
                            <li>We are committed to transparency and user empowerment</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section class="bg-yt-accent/5 border border-yt-accent rounded-lg p-6">
                <h2 class="text-2xl font-semibold text-yt-accent mb-4" data-i18n="legal.faq.still.title">Still Have Questions?</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    If you have questions not covered in this FAQ, please don't hesitate to contact us through our 
                    <a href="{{ route('support.index') }}" class="text-link" data-i18n="legal.faq.still.support_center">Support Center</a>. We are committed to 
                    helping you understand and exercise your privacy rights.
                </p>
                <p class="text-yt-text leading-relaxed">
                    You can also review our comprehensive legal documentation:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mt-4">
                    <li><a href="{{ route('legal.privacy') }}" class="text-link" data-i18n="support.info.quick.privacy">Privacy Policy</a></li>
                    <li><a href="{{ route('legal.terms') }}" class="text-link" data-i18n="support.info.quick.terms">Terms of Service</a></li>
                    <li><a href="{{ route('legal.philosophy') }}" class="text-link" data-i18n="support.info.quick.philosophy">Our Philosophy</a></li>
                    <li><a href="{{ route('legal.no-logs') }}" class="text-link" data-i18n="support.info.quick.no_logs">No Logs Policy</a></li>
                </ul>
            </section>
        </div>
    </div>
</div>
</div>
@endsection
