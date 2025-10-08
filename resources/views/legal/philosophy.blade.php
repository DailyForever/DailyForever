@extends('layouts.app')

@section('title', 'Philosophy - DailyForever')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-slate-50/50 to-slate-100/30 dark:from-slate-900/50 dark:to-slate-800/30">
    <div class="max-w-4xl w-full px-4">
        <div class="content-card p-8">
        <h1 class="text-3xl font-bold text-yt-text mb-6" data-i18n="legal.philosophy.title">Our Philosophy</h1>
        <p class="text-yt-text-secondary mb-8 text-lg">Why DailyForever exists and the principles that guide our mission to protect digital privacy and security.</p>

        <div class="bg-yt-accent/10 border border-yt-accent rounded-lg p-6 mb-8">
            <h2 class="text-lg font-semibold text-yt-accent mb-3" data-i18n="legal.philosophy.banner_title">Privacy and Encryption as Universal Human Rights</h2>
            <p class="text-yt-text leading-relaxed">
                DailyForever is founded on the unshakeable belief that privacy and encryption are fundamental human rights that must be 
                protected and defended for all people, regardless of their nationality, political beliefs, economic status, or any other 
                characteristic. We recognize that these rights are essential for human dignity, freedom of expression, and the functioning 
                of democratic societies. Our mission is to provide tools that enable individuals to exercise these rights in the digital age.
            </p>
        </div>

        <div class="prose prose-invert max-w-none space-y-10">
            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">Our Mission</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    DailyForever exists to provide a secure, private, and accessible platform for sharing sensitive information in an increasingly surveilled digital world. We believe that privacy is a fundamental human right, not a luxury, and that everyone deserves access to tools that protect their digital communications.
                </p>
                <p class="text-yt-text leading-relaxed">
                    Our mission is to make end-to-end encryption accessible to everyone, regardless of their technical expertise, while maintaining the highest standards of security and privacy protection.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">Who This Is For</h2>
                <p class="text-yt-text leading-relaxed mb-4">DailyForever serves a diverse community of users who value privacy and security:</p>
                
                <h3 class="text-xl font-medium text-yt-accent mb-4">Journalists and Activists</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Investigative journalists protecting sources and sensitive information</li>
                    <li>Human rights activists working in dangerous environments</li>
                    <li>Whistleblowers exposing corruption and wrongdoing</li>
                    <li>Political dissidents and freedom fighters</li>
                    <li>Anyone who needs safe, deniable sharing of sensitive documents</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">Security and Legal Professionals</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Security researchers sharing vulnerability information</li>
                    <li>Legal teams exchanging confidential case materials</li>
                    <li>Compliance officers handling sensitive regulatory data</li>
                    <li>IT professionals sharing configuration files and logs</li>
                    <li>Forensic analysts collaborating on investigations</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">Developers and Researchers</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Software developers sharing code snippets and documentation</li>
                    <li>Academic researchers collaborating on sensitive projects</li>
                    <li>Open source contributors sharing development materials</li>
                    <li>Students and educators working on privacy-focused projects</li>
                    <li>Anyone who needs to share technical information securely</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">Everyday Users</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6">
                    <li>Individuals who prefer privacy without the complexity of accounts</li>
                    <li>People sharing personal documents and sensitive information</li>
                    <li>Users who want to avoid surveillance and data collection</li>
                    <li>Anyone who values their digital privacy and security</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">Core Principles</h2>
                <p class="text-yt-text leading-relaxed mb-4">Our philosophy is built on several fundamental principles:</p>
                
                <h3 class="text-xl font-medium text-yt-accent mb-4">Privacy by Design</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    Privacy is not an afterthought—it's built into every aspect of our service. We implement privacy by design principles, ensuring that user data is protected by default and that privacy is maintained throughout the entire data lifecycle.
                </p>

                <h3 class="text-xl font-medium text-yt-accent mb-4">Zero Knowledge Architecture</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    We cannot access your data because we designed our system to make it impossible. All encryption and decryption happens on your device using the Web Crypto API. We store only encrypted data that we cannot decrypt, ensuring that your privacy is protected even from us.
                </p>

                <h3 class="text-xl font-medium text-yt-accent mb-4">Minimal Data Collection</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    We collect only the minimum data necessary to operate the service. We do not persist IP addresses, user agents, or identities with your content. We do not maintain server-side application logs or server-side tracking. Optional third‑party analytics/ads may be enabled by the site operator; when enabled, they are consent‑based and configured with IP anonymization. We never create user profiles.
                </p>

                <h3 class="text-xl font-medium text-yt-accent mb-4">User Control</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    You control your data. You choose when it expires, how many times it can be viewed, and whether it's private or public. You can delete your content at any time, and we provide tools to help you manage your data effectively.
                </p>

                <h3 class="text-xl font-medium text-yt-accent mb-4">Transparency</h3>
                <p class="text-yt-text leading-relaxed">
                    We are transparent about our practices and open about our limitations. We provide detailed documentation about our security measures, privacy protections, and data handling practices. We believe that transparency builds trust and helps users make informed decisions.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">Anonymous by Design</h2>
                <p class="text-yt-text leading-relaxed mb-4">Our service is designed to protect user anonymity:</p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>No Required Registration:</strong> You can use our service without creating an account or providing any personal information</li>
                    <li><strong>No Email Required:</strong> We don't collect email addresses or require verification</li>
                    <li><strong>No IP Logging:</strong> We don't log or store IP addresses in persistent logs</li>
                    <li><strong>No Server‑Side Tracking:</strong> We do not run server-side trackers or build behavioral profiles</li>
                    <li><strong>Consent‑Based Analytics (optional):</strong> If enabled by the site operator, analytics/ads are consent‑based and configured with IP anonymization. You can control this via the cookies banner.</li>
                    <li><strong>Tor Compatible:</strong> Our service works with Tor and other privacy tools</li>
                    <li><strong>Strict Referrer Policy:</strong> We don't leak information through referrer headers</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">Technical Excellence</h2>
                <p class="text-yt-text leading-relaxed mb-4">We use the best available cryptographic tools and techniques:</p>
                
                <h3 class="text-xl font-medium text-yt-accent mb-4">Modern Cryptography</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>AES-GCM 256-bit:</strong> Industry-standard encryption for content protection</li>
                    <li><strong>Argon2id:</strong> State-of-the-art password hashing for account security</li>
                    <li><strong>Web Crypto API:</strong> Browser-native cryptographic functions for maximum security</li>
                    <li><strong>Client-Side Encryption:</strong> All encryption happens on your device, not our servers</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">Post-Quantum Ready</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>ML-KEM 512:</strong> Post-quantum key encapsulation for future security</li>
                    <li><strong>One-Time Prekeys:</strong> Forward secrecy for addressed communications</li>
                    <li><strong>Quantum-Resistant Design:</strong> Architecture designed to resist quantum attacks</li>
                    <li><strong>Future-Proof:</strong> Ready for the post-quantum computing era</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">Security Best Practices</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6">
                    <li><strong>Constant-Time Operations:</strong> Prevent timing attacks on our systems</li>
                    <li><strong>Metadata Protection:</strong> Padding and time rounding to prevent analysis</li>
                    <li><strong>Secure Deletion:</strong> Expired content is permanently and securely deleted</li>
                    <li><strong>Regular Audits:</strong> Regular security audits and vulnerability assessments</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">No Logs Commitment</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    We maintain a strict no server‑side logs policy. We do not log IP addresses, user agents, identities, or access patterns. For abuse prevention, IP addresses may be used ephemerally for in‑memory throttling and are never persisted. If optional analytics/ads are enabled, they are consent‑based and configured with IP anonymization and do not change our server‑side no‑logs posture.
                </p>
                <p class="text-yt-text leading-relaxed">
                    <a href="{{ route('legal.no-logs') }}" class="text-link">Read our detailed No Logs Policy</a> to understand exactly what we do and don't log.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">Accessibility and Usability</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    We believe that privacy tools should be accessible to everyone, not just technical experts. Our service is designed to be:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Easy to Use:</strong> Simple, intuitive interface that anyone can understand</li>
                    <li><strong>Fast and Reliable:</strong> Quick sharing without compromising security</li>
                    <li><strong>Cross-Platform:</strong> Works on any device with a modern web browser</li>
                    <li><strong>No Installation:</strong> No software to download or install</li>
                    <li><strong>Mobile Friendly:</strong> Optimized for mobile devices and touch interfaces</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">Accountability Without Surveillance</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    We believe it's possible to prevent abuse and maintain accountability without compromising user privacy. Our approach includes:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Content Expiration:</strong> Automatic deletion of content after specified time or views</li>
                    <li><strong>View Limits:</strong> Users can set limits on how many times content can be accessed</li>
                    <li><strong>Password Protection:</strong> Optional password protection for sensitive content</li>
                    <li><strong>Private Mode:</strong> Content visible only to authenticated users</li>
                    <li><strong>Abuse Reporting:</strong> Users can report violations without compromising privacy</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">Open Verification</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    We believe in open verification and transparency. You can verify our claims by:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Inspecting Network Traffic:</strong> See exactly what data is sent to our servers</li>
                    <li><strong>Reviewing Source Code:</strong> Client-side code is available for inspection</li>
                    <li><strong>License &amp; Source:</strong> The project is open source under the GNU AGPL v3; you can obtain and redistribute the source under its terms</li>
                    <li><strong>Analyzing Encryption:</strong> Verify that encryption happens client-side</li>
                    <li><strong>Testing Privacy Claims:</strong> Verify that we don't log identifying information</li>
                    <li><strong>Security Audits:</strong> Independent security researchers can audit our practices</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">The Future of Privacy</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    We believe that privacy is essential for a free and open society. As technology becomes more powerful and surveillance becomes more pervasive, tools like DailyForever become increasingly important for protecting fundamental human rights.
                </p>
                <p class="text-yt-text leading-relaxed mb-4">
                    We are committed to staying at the forefront of privacy technology, implementing new security measures as they become available, and adapting to new threats as they emerge. Our goal is to provide a service that remains secure and private for years to come.
                </p>
                <p class="text-yt-text leading-relaxed">
                    The future of privacy depends on tools that are both secure and accessible. DailyForever is our contribution to that future.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">Our Commitment to You</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    We make the following commitments to our users:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Privacy First:</strong> We will always prioritize your privacy over our convenience</li>
                    <li><strong>Transparency:</strong> We will be honest about our capabilities and limitations</li>
                    <li><strong>Security:</strong> We will use the best available security measures</li>
                    <li><strong>Accessibility:</strong> We will make privacy tools accessible to everyone</li>
                    <li><strong>Independence:</strong> We will resist pressure to compromise user privacy</li>
                    <li><strong>Innovation:</strong> We will continuously improve our security and privacy protections</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">Join Our Mission</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    If you share our commitment to privacy and security, we invite you to join our community. Use DailyForever for your sensitive communications, share it with others who need privacy protection, and help us build a more secure and private digital world.
                </p>
                <p class="text-yt-text leading-relaxed">
                    Together, we can ensure that privacy remains a fundamental right in the digital age.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">12. Technical Architecture & Security Philosophy</h2>
                <p class="text-yt-text leading-relaxed mb-6">
                    This section provides detailed technical information about our security architecture, encryption methods, and privacy protection mechanisms. This information is provided for transparency and for users who want to understand the technical implementation of our privacy protections.
                </p>

                <h3 class="text-xl font-medium text-yt-accent mb-4">12.1 Security-First Design Principles</h3>
                <div class="bg-gray-900 border border-gray-700 rounded-lg p-6 mb-6">
                    <h4 class="text-lg font-semibold text-yt-text mb-3">Zero-Trust Architecture</h4>
                    <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                        <li><strong>Client-Side Encryption:</strong> All content encrypted before leaving user's device</li>
                        <li><strong>Server-Side Blindness:</strong> Server cannot access unencrypted content</li>
                        <li><strong>Key Separation:</strong> Encryption keys only transmitted to server when owners choose to store them</li>
                        <li><strong>Minimal Metadata:</strong> Only essential metadata collected and stored</li>
                        <li><strong>Automatic Deletion:</strong> Content automatically purged after expiration</li>
                    </ul>
                    <p class="text-yt-text-secondary text-sm">
                        Our architecture ensures that even with full server access, your content remains protected.
                    </p>
                </div>

                <h3 class="text-xl font-medium text-yt-accent mb-4">12.2 Encryption Implementation</h3>
                <div class="bg-gray-900 border border-gray-700 rounded-lg p-6 mb-6">
                    <h4 class="text-lg font-semibold text-yt-text mb-3">AES-GCM 256-bit Encryption</h4>
                    <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                        <li><strong>Algorithm:</strong> Advanced Encryption Standard - Galois/Counter Mode</li>
                        <li><strong>Key Strength:</strong> 256-bit encryption keys (military-grade)</li>
                        <li><strong>Authentication:</strong> Built-in message authentication prevents tampering</li>
                        <li><strong>Randomization:</strong> Unique initialization vector for each encryption</li>
                        <li><strong>Implementation:</strong> Web Crypto API for maximum browser compatibility</li>
                    </ul>
                    <p class="text-yt-text-secondary text-sm">
                        This is the same encryption standard used by banks, governments, and military organizations worldwide.
                    </p>
                </div>

                <h3 class="text-xl font-medium text-yt-accent mb-4">12.3 Post-Quantum Security</h3>
                <div class="bg-gray-900 border border-gray-700 rounded-lg p-6 mb-6">
                    <h4 class="text-lg font-semibold text-yt-text mb-3">ML-KEM 512 Key Exchange</h4>
                    <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                        <li><strong>Algorithm:</strong> Module-Lattice-based Key Encapsulation Mechanism</li>
                        <li><strong>NIST Standard:</strong> Approved by National Institute of Standards and Technology</li>
                        <li><strong>Quantum Resistance:</strong> Secure against quantum computer attacks</li>
                        <li><strong>Future-Proof:</strong> Designed to remain secure for decades</li>
                        <li><strong>Implementation:</strong> Client-side JavaScript for maximum security</li>
                    </ul>
                    <p class="text-yt-text-secondary text-sm">
                        We're preparing for the quantum computing era to ensure your data remains secure.
                    </p>
                </div>

                <h3 class="text-xl font-medium text-yt-accent mb-4">12.4 Privacy Protection Mechanisms</h3>
                <div class="bg-gray-900 border border-gray-700 rounded-lg p-6 mb-6">
                    <h4 class="text-lg font-semibold text-yt-text mb-3">Multi-Layer Privacy Defense</h4>
                    <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                        <li><strong>No Content Analysis:</strong> Server never analyzes or processes content</li>
                        <li><strong>No Logging:</strong> No access logs or user activity tracking</li>
                        <li><strong>No Data Mining:</strong> No collection of usage patterns or metadata</li>
                        <li><strong>No Third-Party Access:</strong> No sharing with advertisers or data brokers</li>
                        <li><strong>No Government Backdoors:</strong> No secret access mechanisms</li>
                    </ul>
                    <p class="text-yt-text-secondary text-sm">
                        Multiple layers of protection ensure your privacy is never compromised.
                    </p>
                </div>

                <h3 class="text-xl font-medium text-yt-accent mb-4">12.5 Security Standards & Compliance</h3>
                <div class="bg-gray-900 border border-gray-700 rounded-lg p-6 mb-6">
                    <h4 class="text-lg font-semibold text-yt-text mb-3">Industry-Leading Security</h4>
                    <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                        <li><strong>HTTPS/TLS 1.3:</strong> All communications encrypted in transit</li>
                        <li><strong>Content Security Policy:</strong> We are progressively deploying strict CSP headers to reduce XSS risk</li>
                        <li><strong>CSRF Protection:</strong> Laravel CSRF tokens for all forms</li>
                        <li><strong>Input Validation:</strong> Server-side validation of all inputs</li>
                        <li><strong>Rate Limiting:</strong> Protection against abuse and DoS attacks</li>
                        <li><strong>Secure Headers:</strong> HSTS, X-Frame-Options, and other security headers</li>
                    </ul>
                    <p class="text-yt-text-secondary text-sm">
                        We implement the same security standards used by major tech companies and government agencies.
                    </p>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">13. Contact Us</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    If you have questions about our philosophy or want to learn more about our mission, please contact us:
                </p>
                <div class="bg-yt-bg border border-yt-border rounded-lg p-6">
                    <p class="text-yt-text">
                        <strong>General Inquiries</strong><br>
                        Email: dailyforever@proton.me<br>
                        <br>
                        <strong>Response Time:</strong> We will respond to all inquiries within 48 hours
                    </p>
                </div>
            </section>
        </div>
    </div>
</div>
</div>
@endsection