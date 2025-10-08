@extends('layouts.app')

@section('title', 'Terms of Service - DailyForever')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-slate-50/50 to-slate-100/30 dark:from-slate-900/50 dark:to-slate-800/30">
    <div class="max-w-4xl w-full px-4">
        <div class="content-card p-8">
        <h1 class="text-3xl font-bold text-yt-text mb-4" data-i18n="legal.terms.title">Terms of Service</h1>
        <p class="text-yt-text-secondary mb-8 text-sm"><span data-i18n="legal.common.last_updated">Last updated:</span> September 23, 2025</p>

        <div class="bg-yt-accent/10 border border-yt-accent rounded-lg p-6 mb-8">
            <h2 class="text-lg font-semibold text-yt-accent mb-3" data-i18n="legal.terms.banner_title">Fundamental Privacy Rights</h2>
            <p class="text-yt-text leading-relaxed">
                DailyForever operates on the fundamental principle that privacy and encryption are universal human rights. 
                We believe that every individual, regardless of their background, location, or circumstances, has the 
                inherent right to communicate privately and securely. Our service is designed to protect these rights 
                through zero-knowledge architecture and end-to-end encryption. We are committed to defending these 
                rights against any form of surveillance, censorship, or unauthorized access.
            </p>
        </div>

        <div class="prose prose-invert max-w-none space-y-10">
            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">1. Acceptance of Terms</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    Welcome to DailyForever ("we," "our," or "us"). These Terms of Service ("Terms") govern your use of our encrypted paste and file sharing service ("Service") operated by DailyForever. By accessing or using our Service, you agree to be bound by these Terms and our Privacy Policy.
                </p>
                <p class="text-yt-text leading-relaxed">
                    If you do not agree to these Terms, you may not access or use our Service. These Terms apply to all visitors, users, and others who access or use the Service.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">2. Description of Service</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    DailyForever is a zero-knowledge encrypted platform that provides secure sharing of text content and files. Our Service includes but is not limited to:
                </p>
                
                <h3 class="text-xl font-medium text-yt-accent mb-4">2.1 Core Features</h3>
                <ul class="list-disc list-inside text-yt-text space-y-3 ml-6 mb-6">
                    <li><strong>Encrypted Pastes:</strong> Password-protected text sharing with client-side AES-GCM encryption (256-bit)</li>
                    <li><strong>Encrypted File Uploads:</strong> Secure file sharing up to 160MB with client-side encryption</li>
                    <li><strong>User Accounts:</strong> Optional registration with username/password authentication using Argon2id hashing</li>
                    <li><strong>Two-Factor Authentication:</strong> TOTP-based 2FA for enhanced account security</li>
                    <li><strong>Backup Code Authentication:</strong> Encrypted backup codes for secure login without passwords</li>
                    <li><strong>Post-Quantum Prekeys:</strong> ML-KEM 512 prekey management for future quantum-resistant encryption</li>
                    <li><strong>Private Content:</strong> User-only visibility for authenticated content</li>
                    <li><strong>Password Protection:</strong> Server-side password verification with Argon2id hashing</li>
                    <li><strong>View Limits:</strong> Automatic content deletion after specified view counts</li>
                    <li><strong>Expiration Controls:</strong> Time-based automatic content deletion</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">2.2 Zero-Knowledge Architecture</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    All content is encrypted on your device before transmission using AES-GCM encryption. We implement a zero-knowledge architecture, which means:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                    <li>We do not have access to your unencrypted content</li>
                    <li>We cannot decrypt your pastes or files without the encryption key</li>
                    <li>Encryption keys are generated client-side and only transmitted to our servers when owners choose to store them for convenient access</li>
                    <li>We store only encrypted ciphertext and necessary metadata</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">2.3 Advertising and Analytics</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    To support the continued operation and development of our free service, DailyForever may display advertisements 
                    and use analytics services. We are committed to implementing these services in a privacy-preserving manner:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                    <li><strong>Google AdSense:</strong> We may display contextual advertisements that do not require personal data</li>
                    <li><strong>Google Analytics:</strong> We may use analytics to understand service usage while protecting user privacy</li>
                    <li><strong>Privacy Protection:</strong> All advertising and analytics are configured to minimize data collection</li>
                    <li><strong>User Control:</strong> Users can opt out of non-essential cookies and tracking</li>
                    <li><strong>Content Isolation:</strong> Advertisements and analytics do not affect the privacy of your encrypted content</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    Any advertising or analytics implementation will be clearly disclosed and will not compromise the 
                    zero-knowledge architecture of our core encryption services.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">3. User Accounts and Registration</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    While our Service can be used without registration, creating an account provides additional features:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                    <li>Private paste and file creation</li>
                    <li>Two-factor authentication for enhanced security</li>
                    <li>Backup code authentication for secure login</li>
                    <li>Post-quantum prekey management</li>
                    <li>Multiple account recovery options (PIN, email, backup codes)</li>
                </ul>
                <p class="text-yt-text leading-relaxed mb-4">
                    You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account. You agree to:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6">
                    <li>Provide accurate and complete information during registration</li>
                    <li>Keep your password, backup codes, and 2FA credentials secure</li>
                    <li>Store backup codes safely as they change with each login</li>
                    <li>Notify us immediately of any unauthorized use of your account</li>
                    <li>Be responsible for all content posted under your account</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">4. User Responsibilities and Prohibited Uses</h2>
                <p class="text-yt-text leading-relaxed mb-4">You agree that you will not use the Service to:</p>
                
                <h3 class="text-xl font-medium text-yt-accent mb-4">4.1 Illegal Activities</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Upload, post, or transmit any content that is illegal, harmful, threatening, abusive, harassing, defamatory, vulgar, obscene, or otherwise objectionable</li>
                    <li>Violate any local, state, national, or international law or regulation</li>
                    <li>Infringe upon the intellectual property rights of others</li>
                    <li>Distribute malware, viruses, or any other malicious code</li>
                    <li>Facilitate illegal activities or provide instructions for illegal acts</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">4.2 Security Violations</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Attempt to gain unauthorized access to our systems or other users' content</li>
                    <li>Use the service for spam, phishing, or other fraudulent activities</li>
                    <li>Attempt to decrypt other users' content without authorization</li>
                    <li>Interfere with the proper functioning of the Service</li>
                    <li>Use automated systems to abuse or overload our servers</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">4.3 Content Restrictions</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6">
                    <li>Content involving minors in inappropriate contexts</li>
                    <li>Graphic violence or disturbing imagery descriptions</li>
                    <li>Content that promotes hate speech or discrimination</li>
                    <li>Personal information of others without consent</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">5. Content and Intellectual Property</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    Your content (both pastes and files) is encrypted client-side using AES-GCM before being transmitted to our servers. We store only encrypted data and associated metadata such as:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Expiration times and view limits</li>
                    <li>File sizes and MIME types (for files)</li>
                    <li>Password hashes (Argon2id) for password-protected content</li>
                    <li>User associations for private content</li>
                    <li>Two-factor authentication settings (for accounts)</li>
                    <li>Post-quantum prekey data (for future encryption)</li>
                </ul>
                
                <h3 class="text-xl font-medium text-yt-accent mb-4">5.1 Your Rights</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    You retain all rights to your content. By using our Service, you grant us a limited, non-exclusive, royalty-free license to store and serve your encrypted content solely for the purpose of providing the Service.
                </p>

                <h3 class="text-xl font-medium text-yt-accent mb-4">5.2 Our Rights</h3>
                <p class="text-yt-text leading-relaxed">
                    We reserve the right to remove or disable access to content that violates these Terms or applicable laws. Due to our zero-knowledge architecture, we cannot review content before it is posted, but we will respond to valid reports of violations.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">6. Content Expiration and Deletion</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    Pastes and files may be set to expire automatically or have view/download limits. Our deletion policies include:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                    <li>Expired content is permanently deleted from our servers</li>
                    <li>Content with view limits is automatically deleted after the specified number of views</li>
                    <li>We reserve the right to delete content that violates these Terms or applicable laws</li>
                    <li>Deleted content cannot be recovered due to our zero-knowledge architecture</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    We implement automated deletion processes to ensure expired content is permanently removed and cannot be accessed by anyone, including us.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">7. Privacy and Data Protection</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    Your privacy is fundamental to our Service. Please review our Privacy Policy for detailed information about how we collect, use, and protect your information. Key points include:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                    <li>We do not log IP addresses or user identities</li>
                    <li>All content is encrypted client-side before transmission</li>
                    <li>We cannot access or decrypt your content</li>
                    <li>We maintain minimal technical logs for system operation only</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    Our Service is designed to comply with applicable privacy laws, including GDPR and CCPA, while maintaining our zero-knowledge architecture.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">8. Data Retention and Deletion</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    We maintain strict data retention policies to minimize data collection and protect your privacy. Our retention schedules are designed to balance operational needs with privacy protection:
                </p>
                
                <h3 class="text-xl font-medium text-yt-accent mb-4">8.1 Content Data</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Encrypted Pastes:</strong> Retained until expiration or view limit reached, then permanently deleted</li>
                    <li><strong>Encrypted Files:</strong> Retained until expiration or download limit reached, then permanently deleted</li>
                    <li><strong>Expired Content:</strong> Automatically deleted within 24 hours of expiration</li>
                    <li><strong>Deleted Content:</strong> Cannot be recovered due to zero-knowledge architecture</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">8.2 Account Data</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Active Accounts:</strong> Data retained while account is active</li>
                    <li><strong>Inactive Accounts:</strong> Account data deleted after 2 years of inactivity</li>
                    <li><strong>Deleted Accounts:</strong> All associated data permanently deleted within 30 days</li>
                    <li><strong>Backup Codes:</strong> Rotated and old codes deleted immediately upon generation of new codes</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">8.3 Technical Logs</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>System Logs:</strong> Retained for 30 days for operational purposes</li>
                    <li><strong>Security Logs:</strong> Retained for 90 days for security monitoring</li>
                    <li><strong>Error Logs:</strong> Retained for 14 days for debugging purposes</li>
                    <li><strong>Access Logs:</strong> Retained for 7 days (no personal data included)</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">8.4 Legal Holds</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    In the event of legal proceedings, we may be required to preserve certain data beyond our normal retention periods. However, due to our zero-knowledge architecture:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                    <li>We can only preserve encrypted content (which we cannot decrypt)</li>
                    <li>We cannot provide decrypted content even under legal order</li>
                    <li>Legal holds do not affect our inability to access user content</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">8.5 Data Deletion Verification</h3>
                <p class="text-yt-text leading-relaxed">
                    We implement secure deletion practices including cryptographic erasure and multiple overwrites to ensure data cannot be recovered. All deletion processes are logged and audited to verify complete removal.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">9. Security Incident Response</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    We maintain comprehensive security incident response procedures to protect our users and service. Our response framework prioritizes user safety, transparency, and rapid resolution.
                </p>
                
                <h3 class="text-xl font-medium text-yt-accent mb-4">9.1 Incident Classification</h3>
                <p class="text-yt-text leading-relaxed mb-4">Security incidents are classified by severity and potential impact:</p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Critical:</strong> Potential compromise of encryption systems or user data</li>
                    <li><strong>High:</strong> Service disruption or unauthorized access attempts</li>
                    <li><strong>Medium:</strong> Security vulnerabilities or suspicious activity</li>
                    <li><strong>Low:</strong> Minor security events or false alarms</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">9.2 Response Timeline</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Critical Incidents:</strong> Response initiated within 1 hour, resolution within 24 hours</li>
                    <li><strong>High Incidents:</strong> Response initiated within 4 hours, resolution within 72 hours</li>
                    <li><strong>Medium Incidents:</strong> Response initiated within 24 hours, resolution within 7 days</li>
                    <li><strong>Low Incidents:</strong> Response initiated within 48 hours, resolution within 14 days</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">9.3 User Notification</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    We are committed to transparent communication about security incidents that may affect users:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Critical/High Incidents:</strong> Users notified within 24 hours via email and service notifications</li>
                    <li><strong>Medium Incidents:</strong> Users notified within 72 hours via service notifications</li>
                    <li><strong>Low Incidents:</strong> Information posted in security updates or changelog</li>
                    <li><strong>False Alarms:</strong> No user notification required</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">9.4 Incident Response Process</h3>
                <ol class="list-decimal list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Detection:</strong> Automated monitoring and manual reporting identify potential incidents</li>
                    <li><strong>Assessment:</strong> Security team evaluates severity and potential impact</li>
                    <li><strong>Containment:</strong> Immediate steps to prevent further damage or data exposure</li>
                    <li><strong>Investigation:</strong> Detailed analysis to understand scope and root cause</li>
                    <li><strong>Eradication:</strong> Removal of threats and vulnerabilities</li>
                    <li><strong>Recovery:</strong> Restoration of normal service operations</li>
                    <li><strong>Lessons Learned:</strong> Post-incident review and process improvement</li>
                </ol>

                <h3 class="text-xl font-medium text-yt-accent mb-4">9.5 Zero-Knowledge Protection</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    Even in the event of a security incident, our zero-knowledge architecture provides additional protection:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Encrypted content remains protected even if our systems are compromised</li>
                    <li>We cannot decrypt user content even under duress or system compromise</li>
                    <li>Encryption keys are never stored on our servers in recoverable form</li>
                    <li>Client-side encryption ensures data is protected before transmission</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">9.6 Reporting Security Issues</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    We encourage responsible disclosure of security vulnerabilities:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                    <li><strong>Email:</strong> dailyforever@proton.me for security reports</li>
                    <li><strong>Response Time:</strong> Acknowledgment within 24 hours, initial assessment within 72 hours</li>
                    <li><strong>Bug Bounty:</strong> We may offer rewards for valid security vulnerabilities</li>
                    <li><strong>Coordination:</strong> We work with researchers to responsibly disclose issues</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">9.7 Regulatory Compliance</h3>
                <p class="text-yt-text leading-relaxed">
                    Our incident response procedures comply with applicable regulations including GDPR Article 33 (breach notification), CCPA, and other relevant data protection laws. We maintain detailed incident logs and documentation for regulatory reporting when required.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">10. Service Availability and Modifications</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    We strive to maintain high availability but do not guarantee uninterrupted service. We reserve the right to:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                    <li>Modify, suspend, or discontinue the Service at any time with or without notice</li>
                    <li>Perform maintenance that may temporarily affect Service availability</li>
                    <li>Update features and functionality to improve the Service</li>
                    <li>Implement security measures that may affect certain features</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    We will provide reasonable notice of significant changes when possible, but emergency security updates may be implemented immediately.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">11. Limitation of Liability</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    TO THE MAXIMUM EXTENT PERMITTED BY LAW, DAILYFOREVER IS PROVIDED "AS IS" WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED. WE SHALL NOT BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL, OR PUNITIVE DAMAGES, INCLUDING BUT NOT LIMITED TO:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                    <li>Loss of data or content</li>
                    <li>Loss of profits or business opportunities</li>
                    <li>Service interruptions or downtime</li>
                    <li>Security breaches or unauthorized access</li>
                    <li>Any other damages arising from your use of the Service</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    Our total liability to you for any claims arising from these Terms or your use of the Service shall not exceed the amount you paid us for the Service in the 12 months preceding the claim, or $100, whichever is greater.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">12. Indemnification</h2>
                <p class="text-yt-text leading-relaxed">
                    You agree to indemnify, defend, and hold harmless DailyForever and its officers, directors, employees, and agents from any claims, damages, losses, or expenses (including reasonable attorneys' fees) arising from your use of the Service, violation of these Terms, or infringement of any third-party rights.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">13. Support and Communication</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    DailyForever provides comprehensive support services to ensure the protection of your privacy rights and the proper functioning of our platform. Our support system is designed to handle various types of inquiries and reports while maintaining the highest standards of privacy and security.
                </p>
                
                <h3 class="text-xl font-medium text-yt-accent mb-4">13.1 Support Services</h3>
                <p class="text-yt-text leading-relaxed mb-4">We provide support for the following matters:</p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>General Support:</strong> Technical assistance, account management, and service-related questions</li>
                    <li><strong>Security Reports:</strong> Vulnerability disclosures and security-related concerns</li>
                    <li><strong>DMCA Notices:</strong> Copyright infringement reports and takedown requests</li>
                    <li><strong>Abuse Reports:</strong> Policy violations, malicious content, and inappropriate behavior</li>
                    <li><strong>Policy Appeals:</strong> Content removal appeals and account restriction challenges</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">13.2 Support Response Times</h3>
                <p class="text-yt-text leading-relaxed mb-4">We are committed to timely responses based on the nature of your inquiry:</p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Security Issues:</strong> 12-24 hours</li>
                    <li><strong>DMCA Notices:</strong> 24-48 hours</li>
                    <li><strong>Abuse Reports:</strong> 24-48 hours</li>
                    <li><strong>General Support:</strong> 48-72 hours</li>
                    <li><strong>Policy Appeals:</strong> 5-7 business days</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">13.3 Privacy in Support Communications</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    All support communications are handled with the same privacy standards as our core service. 
                    We do not log support conversations beyond what is necessary for resolution, and we do not 
                    share your personal information with third parties without your explicit consent, except 
                    as required by law.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">14. Dispute Resolution</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    Any disputes arising from these Terms or your use of the Service will be resolved through binding arbitration in accordance with the rules of the American Arbitration Association. The arbitration will be conducted in English and will take place in a mutually agreed location.
                </p>
                <p class="text-yt-text leading-relaxed">
                    You waive any right to participate in class action lawsuits or class-wide arbitration against us.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">15. Changes to Terms</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    We reserve the right to modify these Terms at any time. Changes will be effective immediately upon posting on this page. We will notify users of material changes through:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                    <li>Posting a notice on our Service</li>
                    <li>Sending email notifications to registered users (when possible)</li>
                    <li>Updating the "Last updated" date at the top of these Terms</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    Your continued use of the Service after changes constitutes acceptance of the new Terms. If you do not agree to the changes, you must stop using the Service.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">16. Termination</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    We may terminate or suspend your access to the Service immediately, without prior notice or liability, for any reason, including if you breach these Terms. Upon termination:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-4">
                    <li>Your right to use the Service will cease immediately</li>
                    <li>Your account and associated data may be deleted</li>
                    <li>Your content will be subject to our deletion policies</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    You may terminate your account at any time by contacting us or using account deletion features if available.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">17. Governing Law</h2>
                <p class="text-yt-text leading-relaxed">
                    These Terms shall be governed by and construed in accordance with the laws of the jurisdiction in which DailyForever operates, without regard to conflict of law principles. Any legal action or proceeding arising under these Terms will be brought exclusively in the courts of that jurisdiction.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">18. Severability</h2>
                <p class="text-yt-text leading-relaxed">
                    If any provision of these Terms is held to be invalid or unenforceable, the remaining provisions will remain in full force and effect. We will replace any invalid provision with a valid provision that most closely approximates the intent of the original provision.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">19. Contact Information</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    If you have questions about these Terms of Service, please contact us:
                </p>
                <div class="bg-yt-bg border border-yt-border rounded-lg p-6">
                    <p class="text-yt-text">
                        <strong>DailyForever Legal Department</strong><br>
                        Email: dailyforever@proton.me<br>
                        Website: <a href="{{ route('legal.philosophy') }}" class="text-link">Our Philosophy</a><br>
                        <br>
                        <strong>For DMCA notices:</strong><br>
                        Email: dailyforever@proton.me<br>
                        Subject: DMCA Takedown Notice
                    </p>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">20. Entire Agreement</h2>
                <p class="text-yt-text leading-relaxed">
                    These Terms, together with our Privacy Policy and other legal documents referenced herein, constitute the entire agreement between you and DailyForever regarding the use of our Service and supersede all prior agreements and understandings.
                </p>
            </section>
        </div>
    </div>
</div>
</div>
@endsection