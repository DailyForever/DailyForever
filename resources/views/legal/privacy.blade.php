@extends('layouts.app')

@section('title', 'Privacy Policy - DailyForever')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-slate-50/50 to-slate-100/30 dark:from-slate-900/50 dark:to-slate-800/30">
    <div class="max-w-4xl w-full px-4">
        <div class="content-card p-8">
        <h1 class="text-3xl font-bold text-yt-text mb-4" data-i18n="legal.privacy.title">Privacy Policy</h1>
        <p class="text-yt-text-secondary mb-8 text-sm"><span data-i18n="legal.common.last_updated">Last updated:</span> September 23, 2025</p>

        <div class="bg-yt-accent/10 border border-yt-accent rounded-lg p-6 mb-8">
            <h2 class="text-lg font-semibold text-yt-accent mb-3" data-i18n="legal.privacy.banner_title">Privacy as a Fundamental Human Right</h2>
            <p class="text-yt-text leading-relaxed">
                DailyForever recognizes privacy as a fundamental human right, as enshrined in the Universal Declaration of Human Rights 
                and various international conventions. We believe that every individual has the inherent right to communicate privately, 
                express themselves freely, and maintain control over their personal information. Our privacy policy is designed not merely 
                to comply with regulations, but to actively protect and defend these fundamental rights through technological and 
                organizational measures.
            </p>
        </div>

        <div class="prose prose-invert max-w-none space-y-10">
            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">1. Introduction</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    DailyForever ("we," "our," or "us") is committed to protecting your privacy and implementing the highest standards of data protection. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our encrypted paste and file sharing service.
                </p>
                <p class="text-yt-text leading-relaxed mb-4">
                    Our Service is designed with privacy as a fundamental principle, implementing zero-knowledge encryption to ensure that we cannot access your content. This policy complies with applicable privacy laws, including the General Data Protection Regulation (GDPR), California Consumer Privacy Act (CCPA), and other relevant data protection regulations.
                </p>
                <p class="text-yt-text leading-relaxed">
                    By using our Service, you consent to the data practices described in this Privacy Policy.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">2. Information We Collect</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    We collect minimal information necessary to provide our Service. Due to our zero-knowledge architecture, we cannot access your encrypted content. 
                    We also collect limited information through third-party services for advertising and analytics purposes. Here's what we collect:
                </p>

                <h3 class="text-xl font-medium text-yt-accent mb-4">2.1 Encrypted Content (Not Accessible to Us)</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Encrypted Pastes:</strong> Your text content encrypted with client-side AES-GCM (256-bit) before transmission</li>
                    <li><strong>Encrypted Files:</strong> Your files encrypted with client-side AES-GCM before upload</li>
                    <li><strong>Initialization Vectors:</strong> Technical data required for decryption (not sensitive)</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">2.2 Account Information (Optional)</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Username:</strong> Chosen username for account identification</li>
                    <li><strong>Password Hash:</strong> Securely hashed using Argon2id (never stored in plain text)</li>
                    <li><strong>Email Address:</strong> Optional email for account recovery (stored encrypted)</li>
                    <li><strong>PIN Code:</strong> For account recovery, hashed using Argon2id</li>
                    <li><strong>Backup Codes:</strong> Encrypted backup codes for secure authentication (rotated on each use)</li>
                    <li><strong>Two-Factor Authentication:</strong> TOTP secrets for 2FA-enabled accounts, stored encrypted</li>
                    <li><strong>Post-Quantum Prekeys:</strong> ML-KEM 512 public keys for future quantum-resistant encryption</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">2.3 Technical Metadata</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Paste Identifiers:</strong> Random 6-character alphanumeric identifiers for accessing pastes</li>
                    <li><strong>File Metadata:</strong> File sizes, MIME types, and original filenames (for files only)</li>
                    <li><strong>Expiration Settings:</strong> User-chosen expiration times and view limits</li>
                    <li><strong>Creation Timestamps:</strong> Rounded timestamps with jitter for metadata protection</li>
                    <li><strong>View Counts:</strong> Number of times content has been accessed</li>
                    <li><strong>Password Protection:</strong> Argon2id hashes for password-protected content</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">2.4 System Logs (Minimal)</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Error Logs:</strong> System errors and exceptions (without user-identifying information)</li>
                    <li><strong>Performance Metrics:</strong> Basic system performance data for optimization</li>
                    <li><strong>Security Events:</strong> Automated security monitoring for attack prevention</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">2.5 Third-Party Services Data</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    To support our free service, we may use third-party services for advertising and analytics. 
                    These services collect limited data in accordance with their privacy policies:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                    <li><strong>Google AdSense:</strong> May collect anonymous usage data for contextual advertising</li>
                    <li><strong>Google Analytics:</strong> May collect anonymized website usage statistics</li>
                    <li><strong>Privacy Protection:</strong> All third-party services are configured to minimize data collection</li>
                    <li><strong>User Control:</strong> You can opt out of non-essential cookies and tracking</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    <strong>Important:</strong> Third-party services do not have access to your encrypted content 
                    and cannot compromise the zero-knowledge architecture of our core services.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">3. Zero-Knowledge Architecture</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    Our Service implements a zero-knowledge architecture, which means:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>All encryption and decryption happens on your device using the Web Crypto API</li>
                    <li>Encryption keys are generated client-side and only transmitted to our servers when you choose to store them for convenient access to your own pastes</li>
                    <li>We store only encrypted ciphertext and initialization vectors</li>
                    <li>We cannot decrypt, read, or access your content in any way</li>
                    <li>Even if legally compelled, we cannot provide access to your unencrypted content</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    This architecture ensures that your privacy is protected even from us, providing the highest level of content security possible.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">4. Information We Do NOT Collect</h2>
                <p class="text-yt-text leading-relaxed mb-4">We do not collect:</p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Personal Identification:</strong> Real names, addresses, phone numbers, or other personal identifiers</li>
                    <li><strong>Email Addresses:</strong> We do not require or collect email addresses</li>
                    <li><strong>IP Addresses:</strong> We do not log or store user IP addresses in persistent logs</li>
                    <li><strong>Browser Fingerprints:</strong> We do not collect browser fingerprints or device identifiers</li>
                    <li><strong>Tracking Cookies:</strong> We do not use tracking cookies or analytics</li>
                    <li><strong>Location Data:</strong> We do not collect geographic location information</li>
                    <li><strong>Usage Analytics:</strong> We do not track user behavior or usage patterns</li>
                    <li><strong>Unencrypted Content:</strong> We only store encrypted data that we cannot access</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">5. How We Use Information</h2>
                <p class="text-yt-text leading-relaxed mb-4">The limited information we collect is used solely to:</p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Provide encrypted paste and file sharing functionality</li>
                    <li>Enable user account features and authentication</li>
                    <li>Support two-factor authentication and password protection</li>
                    <li>Manage post-quantum prekeys for future encryption</li>
                    <li>Enforce content expiration and view limit policies</li>
                    <li>Monitor system performance and security</li>
                    <li>Comply with legal obligations when required</li>
                    <li>Prevent abuse and ensure service availability</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    We do not use your information for marketing, advertising, or any purpose other than providing the Service.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">6. Legal Basis for Processing (GDPR)</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    Under the GDPR, we process personal data based on the following legal grounds:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Contract Performance:</strong> Processing necessary to provide the Service you requested</li>
                    <li><strong>Legitimate Interests:</strong> Processing necessary for system security and abuse prevention</li>
                    <li><strong>Consent:</strong> Where you have given explicit consent for specific processing activities</li>
                    <li><strong>Legal Obligation:</strong> Processing required to comply with applicable laws</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    You have the right to withdraw consent at any time, though this may affect your ability to use certain features of the Service.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">7. Data Sharing and Disclosure</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    We do not sell, trade, or rent your information to third parties. We may disclose information only in the following limited circumstances:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Legal Requirements:</strong> When required by law or valid legal process</li>
                    <li><strong>Safety and Security:</strong> To protect our rights, property, or safety, or that of our users</li>
                    <li><strong>Abuse Prevention:</strong> To prevent illegal activities or violations of our terms</li>
                    <li><strong>Service Providers:</strong> With trusted third-party service providers who assist in operating our Service (under strict confidentiality agreements)</li>
                </ul>
                <p class="text-yt-text leading-relaxed mb-4">
                    <strong>Important:</strong> Since we cannot decrypt your content, we cannot provide access to the actual text of your pastes or files even if legally required. We can only provide encrypted data and metadata.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">8. Data Retention</h2>
                <p class="text-yt-text leading-relaxed mb-4">We retain data for the following periods:</p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Encrypted Content:</strong> Until expiration time or view limit is reached, then permanently deleted</li>
                    <li><strong>User Account Data:</strong> Until account deletion or 3 years of inactivity</li>
                    <li><strong>Technical Logs:</strong> 7 days maximum, then automatically purged</li>
                    <li><strong>Security Logs:</strong> 30 days maximum, then automatically purged</li>
                    <li><strong>Metadata:</strong> Until content expiration or account deletion</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    We implement automated deletion processes to ensure expired content and data are permanently removed and cannot be recovered.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">9. Your Rights (GDPR/CCPA)</h2>
                <p class="text-yt-text leading-relaxed mb-4">You have the following rights regarding your personal data:</p>

                <h3 class="text-xl font-medium text-yt-accent mb-4">9.1 Access and Portability</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                    <li>Request access to your personal data</li>
                    <li>Receive a copy of your data in a structured, machine-readable format</li>
                    <li>Request correction of inaccurate or incomplete data</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">9.2 Deletion and Restriction</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                    <li>Request deletion of your personal data</li>
                    <li>Request restriction of processing in certain circumstances</li>
                    <li>Object to processing based on legitimate interests</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">9.3 Withdrawal and Complaint</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                    <li>Withdraw consent for processing based on consent</li>
                    <li>Lodge a complaint with a supervisory authority</li>
                    <li>Request information about automated decision-making</li>
                </ul>

                <p class="text-yt-text leading-relaxed">
                    To exercise these rights, contact us using the information provided in the Contact section. We will respond to your request within 30 days.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">10. Security Measures</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    We implement appropriate technical and organizational security measures to protect your data:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Encryption:</strong> All data is encrypted in transit and at rest</li>
                    <li><strong>Zero-Knowledge Architecture:</strong> We cannot access your encrypted content</li>
                    <li><strong>Secure Hashing:</strong> Passwords and sensitive data are hashed using Argon2id</li>
                    <li><strong>Access Controls:</strong> Strict access controls and authentication for our systems</li>
                    <li><strong>Regular Audits:</strong> Regular security audits and vulnerability assessments</li>
                    <li><strong>Incident Response:</strong> Comprehensive incident response procedures</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    Despite our security measures, no method of transmission over the internet is 100% secure. We cannot guarantee absolute security but strive to implement industry best practices.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">11. International Data Transfers</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    Our services may be provided from servers located in various countries. When we transfer data internationally, we ensure appropriate safeguards are in place:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Standard Contractual Clauses (SCCs) for EU data transfers</li>
                    <li>Adequacy decisions where applicable</li>
                    <li>Appropriate technical and organizational measures</li>
                    <li>Regular review of transfer mechanisms</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    Since all user content is encrypted client-side, the location of data processing does not compromise content privacy.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">12. Children's Privacy</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    Our Service is not intended for children under 13 years of age. We do not knowingly collect personal information from children under 13. If you are a parent or guardian and believe your child has provided us with personal information, please contact us immediately.
                </p>
                <p class="text-yt-text leading-relaxed">
                    If we discover that we have collected personal information from a child under 13, we will take steps to delete such information from our servers immediately.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">13. Third-Party Services</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    We minimize the use of third-party services to reduce potential data exposure. Any third-party services we do use are carefully evaluated for privacy compliance:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>We only use services that are necessary for Service operation</li>
                    <li>All third-party services are subject to strict data protection agreements</li>
                    <li>We regularly audit third-party service providers</li>
                    <li>We avoid services that collect unnecessary user data</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">14. Data Breach Notification</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    In the event of a data breach that may result in a risk to your rights and freedoms, we will:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Notify the relevant supervisory authority within 72 hours</li>
                    <li>Notify affected users without undue delay</li>
                    <li>Provide clear information about the breach and its consequences</li>
                    <li>Describe the measures taken to address the breach</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    Due to our zero-knowledge architecture, the risk of content exposure in a breach is minimal, as we cannot access encrypted content.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">15. Changes to Privacy Policy</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    We may update this Privacy Policy from time to time. We will notify users of any material changes by:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Posting the new policy on this page with an updated revision date</li>
                    <li>Sending email notifications to registered users (when possible)</li>
                    <li>Providing prominent notice on our Service</li>
                    <li>Obtaining consent for material changes when required by law</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    Your continued use of the Service after changes constitutes acceptance of the new Privacy Policy.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">16. GDPR Compliance & Data Protection</h2>
                <p class="text-yt-text leading-relaxed mb-6">
                    DailyForever is fully committed to GDPR compliance. This section provides specific information about our data protection measures, your rights, and our obligations under the General Data Protection Regulation.
                </p>

                <h3 class="text-xl font-medium text-yt-accent mb-4">16.1 Data Protection Officer (DPO)</h3>
                <div class="bg-gray-900 border border-gray-700 rounded-lg p-6 mb-6">
                    <p class="text-yt-text mb-4">
                        We have designated a Data Protection Officer to oversee our GDPR compliance and handle data protection matters:
                    </p>
                    <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                        <li><strong>Email:</strong> dailyforever@proton.me</li>
                        <li><strong>Response Time:</strong> Within 72 hours for urgent matters</li>
                        <li><strong>Languages:</strong> English, with translation available upon request</li>
                        <li><strong>Scope:</strong> All GDPR-related inquiries and data subject requests</li>
                    </ul>
                </div>

                <h3 class="text-xl font-medium text-yt-accent mb-4">16.2 Data Retention & Deletion</h3>
                <div class="bg-gray-900 border border-gray-700 rounded-lg p-6 mb-6">
                    <h4 class="text-lg font-semibold text-yt-text mb-3">Specific Retention Periods</h4>
                    <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                        <li><strong>Encrypted Pastes/Files:</strong> Automatically deleted after expiration (1 hour to 30 days)</li>
                        <li><strong>User Accounts:</strong> Retained until account deletion request</li>
                        <li><strong>Support Reports:</strong> 3 years for legal compliance</li>
                        <li><strong>Access Logs:</strong> 30 days maximum (minimal logging)</li>
                        <li><strong>Backup Data:</strong> 7 days maximum</li>
                    </ul>
                    <p class="text-yt-text-secondary text-sm">
                        All data is automatically purged according to these schedules. You can request immediate deletion at any time.
                    </p>
                </div>

                <h3 class="text-xl font-medium text-yt-accent mb-4">16.3 International Data Transfers</h3>
                <div class="bg-gray-900 border border-gray-700 rounded-lg p-6 mb-6">
                    <h4 class="text-lg font-semibold text-yt-text mb-3">Data Location & Transfers</h4>
                    <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                        <li><strong>Primary Storage:</strong> EU-based servers (encrypted data only)</li>
                        <li><strong>Backup Storage:</strong> EU-based backup systems</li>
                        <li><strong>No Third-Country Transfers:</strong> All data remains within EU</li>
                        <li><strong>Adequacy Decision:</strong> No transfers to non-adequate countries</li>
                        <li><strong>Standard Contractual Clauses:</strong> Not applicable (no transfers)</li>
                    </ul>
                    <p class="text-yt-text-secondary text-sm">
                        Due to our zero-knowledge architecture, we cannot access your content, making data transfer concerns minimal.
                    </p>
                </div>

                <h3 class="text-xl font-medium text-yt-accent mb-4">16.4 Children's Data Protection</h3>
                <div class="bg-gray-900 border border-gray-700 rounded-lg p-6 mb-6">
                    <h4 class="text-lg font-semibold text-yt-text mb-3">Age Verification & Protection</h4>
                    <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                        <li><strong>Minimum Age:</strong> 16 years (GDPR requirement)</li>
                        <li><strong>Age Verification:</strong> Self-declaration during registration</li>
                        <li><strong>Parental Consent:</strong> Required for users under 16</li>
                        <li><strong>Data Minimization:</strong> Minimal data collection for all users</li>
                        <li><strong>Special Protection:</strong> Enhanced privacy measures for minors</li>
                    </ul>
                    <p class="text-yt-text-secondary text-sm">
                        We do not knowingly collect data from children under 16 without parental consent.
                    </p>
                </div>

                <h3 class="text-xl font-medium text-yt-accent mb-4">16.5 Automated Decision-Making</h3>
                <div class="bg-gray-900 border border-gray-700 rounded-lg p-6 mb-6">
                    <h4 class="text-lg font-semibold text-yt-text mb-3">No Automated Profiling</h4>
                    <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                        <li><strong>No Profiling:</strong> We do not create user profiles</li>
                        <li><strong>No Automated Decisions:</strong> No algorithmic decision-making</li>
                        <li><strong>No Scoring:</strong> No risk scoring or behavioral analysis</li>
                        <li><strong>Manual Review:</strong> All moderation decisions are manual</li>
                        <li><strong>Transparency:</strong> All processing is transparent and explainable</li>
                    </ul>
                    <p class="text-yt-text-secondary text-sm">
                        Our zero-knowledge architecture prevents automated analysis of your content.
                    </p>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">17. Technical Architecture & Security Implementation</h2>
                <p class="text-yt-text leading-relaxed mb-6">
                    This section provides detailed technical information about our security architecture, encryption methods, and privacy protection mechanisms. This information is provided for transparency and for users who want to understand the technical implementation of our privacy protections.
                </p>

                <h3 class="text-xl font-medium text-yt-accent mb-4">17.1 Encryption Architecture</h3>
                <div class="bg-gray-900 border border-gray-700 rounded-lg p-6 mb-6">
                    <h4 class="text-lg font-semibold text-yt-text mb-3">Client-Side Encryption (AES-GCM 256-bit)</h4>
                    <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                        <li><strong>Algorithm:</strong> AES-GCM (Advanced Encryption Standard - Galois/Counter Mode)</li>
                        <li><strong>Key Length:</strong> 256-bit encryption keys</li>
                        <li><strong>Implementation:</strong> Web Crypto API (window.crypto.subtle)</li>
                        <li><strong>Key Generation:</strong> Cryptographically secure random key generation</li>
                        <li><strong>Initialization Vector (IV):</strong> 96-bit random IV for each encryption operation</li>
                        <li><strong>Authentication:</strong> Built-in authentication prevents tampering</li>
                    </ul>
                    <p class="text-yt-text-secondary text-sm">
                        All content is encrypted in your browser before transmission. The server never receives unencrypted data.
                    </p>
                </div>

                <h3 class="text-xl font-medium text-yt-accent mb-4">17.2 Post-Quantum Key Exchange (KEM)</h3>
                <div class="bg-gray-900 border border-gray-700 rounded-lg p-6 mb-6">
                    <h4 class="text-lg font-semibold text-yt-text mb-3">ML-KEM 512 Implementation</h4>
                    <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                        <li><strong>Algorithm:</strong> ML-KEM 512 (Module-Lattice-based Key Encapsulation Mechanism)</li>
                        <li><strong>Security Level:</strong> NIST Level 1 (128-bit security equivalent)</li>
                        <li><strong>Purpose:</strong> Future-proof key exchange resistant to quantum computers</li>
                        <li><strong>Implementation:</strong> Client-side JavaScript implementation</li>
                        <li><strong>Key Generation:</strong> Asymmetric key pairs for each user</li>
                        <li><strong>Encapsulation:</strong> One-time keys for secure communication</li>
                    </ul>
                    <p class="text-yt-text-secondary text-sm">
                        This provides protection against future quantum computing threats while maintaining current security standards.
                    </p>
                </div>

                <h3 class="text-xl font-medium text-yt-accent mb-4">17.3 Zero-Knowledge Architecture</h3>
                <div class="bg-gray-900 border border-gray-700 rounded-lg p-6 mb-6">
                    <h4 class="text-lg font-semibold text-yt-text mb-3">Server-Side Privacy Protection</h4>
                    <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                        <li><strong>No Plaintext Access:</strong> Server cannot decrypt user content</li>
                        <li><strong>Encrypted Storage:</strong> All content stored in encrypted form only</li>
                        <li><strong>Key Separation:</strong> Encryption keys only transmitted to server when owners choose to store them for convenient access</li>
                        <li><strong>Metadata Minimization:</strong> Minimal metadata collection and storage</li>
                        <li><strong>No Logging:</strong> No access logs or content analysis</li>
                        <li><strong>Automatic Deletion:</strong> Content automatically deleted after expiration</li>
                    </ul>
                    <p class="text-yt-text-secondary text-sm">
                        Our zero-knowledge architecture ensures that even we cannot access your private content.
                    </p>
                </div>

                <h3 class="text-xl font-medium text-yt-accent mb-4">17.4 Data Flow & Security</h3>
                <div class="bg-gray-900 border border-gray-700 rounded-lg p-6 mb-6">
                    <h4 class="text-lg font-semibold text-yt-text mb-3">End-to-End Security Process</h4>
                    <ol class="list-decimal list-inside text-yt-text space-y-2 ml-6 mb-4">
                        <li><strong>Key Generation:</strong> Random 256-bit AES key generated in browser</li>
                        <li><strong>Content Encryption:</strong> User content encrypted with AES-GCM</li>
                        <li><strong>Key Encapsulation:</strong> If recipient specified, key encrypted with their public key</li>
                        <li><strong>Secure Transmission:</strong> Only encrypted data sent to server over HTTPS</li>
                        <li><strong>Encrypted Storage:</strong> Data stored in encrypted form on server</li>
                        <li><strong>Secure Retrieval:</strong> Encrypted data retrieved and decrypted in browser</li>
                    </ol>
                    <p class="text-yt-text-secondary text-sm">
                        The entire process ensures that content is never accessible in unencrypted form on our servers.
                    </p>
                </div>

                <h3 class="text-xl font-medium text-yt-accent mb-4">17.5 Security Standards & Compliance</h3>
                <div class="bg-gray-900 border border-gray-700 rounded-lg p-6 mb-6">
                    <h4 class="text-lg font-semibold text-yt-text mb-3">Technical Security Measures</h4>
                    <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                        <li><strong>HTTPS/TLS 1.3:</strong> All communications encrypted in transit</li>
                        <li><strong>Content Security Policy:</strong> Strict CSP headers prevent XSS attacks</li>
                        <li><strong>CSRF Protection:</strong> Laravel CSRF tokens for all forms</li>
                        <li><strong>Input Validation:</strong> Server-side validation of all inputs</li>
                        <li><strong>Rate Limiting:</strong> Protection against abuse and DoS attacks</li>
                        <li><strong>Secure Headers:</strong> HSTS, X-Frame-Options, and other security headers</li>
                    </ul>
                    <p class="text-yt-text-secondary text-sm">
                        Multiple layers of security protect both your data and our infrastructure.
                    </p>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">18. Contact Information</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    If you have questions about this Privacy Policy or want to exercise your rights, please contact us:
                </p>
                <div class="bg-yt-bg border border-yt-border rounded-lg p-6">
                    <p class="text-yt-text">
                        <strong>Data Protection Officer</strong><br>
                        DailyForever Privacy Team<br>
                        Email: dailyforever@proton.me<br>
                        Website: <a href="{{ route('legal.philosophy') }}" class="text-link">Our Philosophy</a><br>
                        <br>
                        <strong>For GDPR/CCPA requests:</strong><br>
                        Email: dailyforever@proton.me<br>
                        Subject: Data Rights Request<br>
                        <br>
                        <strong>Response Time:</strong> We will respond to all requests within 30 days as required by law.
                    </p>
                </div>
            </section>

            <!-- <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">19. Supervisory Authority</h2>
                <p class="text-yt-text leading-relaxed">
                    If you are not satisfied with our response to your privacy concerns, you have the right to lodge a complaint with your local data protection supervisory authority. For EU residents, you can find your local authority at <a href="https://edpb.europa.eu/about-edpb/board/members_en" class="text-link" target="_blank">edpb.europa.eu</a>.
                </p>
            </section> -->
        </div>
    </div>
</div>
</div>
@endsection