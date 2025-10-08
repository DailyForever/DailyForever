@extends('layouts.app')

@section('title', 'No Logs Policy - DailyForever')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-slate-50/50 to-slate-100/30 dark:from-slate-900/50 dark:to-slate-800/30">
    <div class="max-w-4xl w-full px-4">
        <div class="content-card p-8">
        <h1 class="text-3xl font-bold text-yt-text mb-4" data-i18n="legal.no_logs.title">No Logs Policy</h1>
        <p class="text-yt-text-secondary mb-8 text-sm"><span data-i18n="legal.common.last_updated">Last updated:</span> September 23, 2025</p>

        <div class="bg-yt-accent/10 border border-yt-accent rounded-lg p-6 mb-8">
            <h2 class="text-lg font-semibold text-yt-accent mb-3" data-i18n="legal.no_logs.banner_title">Technical Implementation of Privacy Rights</h2>
            <p class="text-yt-text leading-relaxed">
                DailyForever's No Logs Policy represents our technical commitment to protecting fundamental privacy rights. 
                We believe that the right to privacy can only be meaningfully protected through technical measures that 
                prevent the collection and storage of personal data. This policy details the specific technical and 
                organizational measures we implement to ensure that user privacy is protected by design, not just by policy.
            </p>
        </div>

        <div class="prose prose-invert max-w-none space-y-10">
            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">1. Our Commitment to Privacy</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    DailyForever is committed to protecting user privacy through minimal data collection and a strict no-logs policy. This document provides detailed information about exactly what information we do and do not log, store, or monitor, and the technical measures we implement to ensure privacy protection.
                </p>
                <p class="text-yt-text leading-relaxed">
                    Our no-logs policy is fundamental to our zero-knowledge architecture and represents our commitment to providing the highest level of privacy protection possible while maintaining service functionality.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">2. What We Do NOT Log</h2>
                <p class="text-yt-text leading-relaxed mb-4">We do not log or store any of the following information:</p>
                
                <h3 class="text-xl font-medium text-yt-accent mb-4">2.1 User Identification Data</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>IP Addresses:</strong> We do not store user IP addresses in persistent logs</li>
                    <li><strong>User Identities:</strong> We do not track or log user identities or personal information</li>
                    <li><strong>Email Addresses:</strong> We do not require or collect email addresses</li>
                    <li><strong>Real Names:</strong> We do not collect or store real names or personal identifiers</li>
                    <li><strong>Location Data:</strong> We do not log or track user geographic information</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">2.2 Usage and Behavior Data</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Content Access Patterns:</strong> We do not log which pastes users view or access</li>
                    <li><strong>User Behavior:</strong> We do not track user interactions, clicks, or usage patterns</li>
                    <li><strong>Navigation Data:</strong> We do not log where users came from or where they go</li>
                    <li><strong>Session Data:</strong> We do not maintain user sessions or tracking across visits</li>
                    <li><strong>Search Queries:</strong> We do not provide search functionality and do not log content searches</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">2.3 Technical Identification Data</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Browser Information:</strong> We do not log user agents, browser types, or device fingerprints</li>
                    <li><strong>Device Identifiers:</strong> We do not collect device IDs, MAC addresses, or hardware identifiers</li>
                    <li><strong>Referrer Information:</strong> We do not log where users came from or navigation patterns</li>
                    <li><strong>Cookie Data:</strong> We do not use tracking cookies or store persistent identifiers</li>
                    <li><strong>Fingerprinting Data:</strong> We do not collect browser fingerprints or device characteristics</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">2.4 Content and Communication Data</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6">
                    <li><strong>Unencrypted Content:</strong> We only store encrypted data that we cannot access</li>
                    <li><strong>Communication Metadata:</strong> We do not log communication patterns or relationships</li>
                    <li><strong>File Access Patterns:</strong> We do not track which files users download or access</li>
                    <li><strong>Sharing Patterns:</strong> We do not log who shares content with whom</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">3. Minimal Technical Logs</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    We maintain only the minimal technical logs absolutely necessary for system operation and security. These logs are designed to contain no personally identifiable information:
                </p>
                
                <h3 class="text-xl font-medium text-yt-accent mb-4">3.1 System Operation Logs</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Error Logs:</strong> System errors and exceptions (without user-identifying information)</li>
                    <li><strong>Performance Metrics:</strong> Basic system performance data for optimization</li>
                    <li><strong>Resource Usage:</strong> Server resource utilization for capacity planning</li>
                    <li><strong>Service Health:</strong> System availability and uptime monitoring</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">3.2 Security Logs</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Attack Attempts:</strong> Automated security monitoring for attack prevention</li>
                    <li><strong>Failed Authentication:</strong> Account security events (without user identification)</li>
                    <li><strong>System Intrusions:</strong> Security breach attempts and responses</li>
                    <li><strong>Abuse Patterns:</strong> Automated detection of policy violations</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">3.3 Log Retention and Purging</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Automatic Purging:</strong> All logs are automatically purged after 7 days</li>
                    <li><strong>No PII:</strong> Logs contain no personally identifiable information</li>
                    <li><strong>Aggregated Data:</strong> Only aggregated, anonymized data is retained longer</li>
                    <li><strong>Secure Deletion:</strong> Deleted logs are securely overwritten and cannot be recovered</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">4. Encrypted Content Storage</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    The only user-related data we store is encrypted and cannot be used to identify users or reveal content:
                </p>
                
                <h3 class="text-xl font-medium text-yt-accent mb-4">4.1 Encrypted Data</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Encrypted Ciphertext:</strong> Your content encrypted with client-side keys we cannot access</li>
                    <li><strong>Initialization Vectors:</strong> Technical data required for decryption (not sensitive)</li>
                    <li><strong>Encryption Metadata:</strong> Algorithm identifiers and key lengths (not sensitive)</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">4.2 Content Metadata</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Paste Identifiers:</strong> Random 6-character alphanumeric identifiers (not linked to users)</li>
                    <li><strong>File Metadata:</strong> File sizes, MIME types, and original filenames (for files only)</li>
                    <li><strong>Expiration Settings:</strong> User-chosen expiration times and view limits</li>
                    <li><strong>Creation Timestamps:</strong> Rounded timestamps with jitter for metadata protection</li>
                    <li><strong>View Counts:</strong> Number of times content has been accessed</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">4.3 Privacy Protection Measures</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6">
                    <li><strong>Metadata Minimization:</strong> We collect only the minimum metadata necessary</li>
                    <li><strong>Time Rounding:</strong> Timestamps are rounded to reduce precision</li>
                    <li><strong>Constant-Time Responses:</strong> Response times are normalized to prevent timing attacks</li>
                    <li><strong>Padding:</strong> Content is padded to prevent size-based analysis</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">5. No Analytics or Tracking</h2>
                <p class="text-yt-text leading-relaxed mb-4">We do not use any of the following tracking mechanisms:</p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Third-party Cookies:</strong> No tracking cookies or persistent identifiers</li>
                    <li><strong>Analytics Services:</strong> No Google Analytics, Mixpanel, or similar services</li>
                    <li><strong>Fingerprinting:</strong> No browser or device fingerprinting techniques</li>
                    <li><strong>Social Media Tracking:</strong> No Facebook Pixel, Twitter tracking, or similar</li>
                    <li><strong>Advertising Networks:</strong> No ad networks or tracking pixels</li>
                    <li><strong>User Profiling:</strong> No creation of user profiles or behavioral analysis</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    Our Service is designed to work without any tracking, ensuring complete privacy for our users.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">6. Legal Compliance and Limitations</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    In the rare event of legal requests for information, our no-logs policy means:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>No User Identification:</strong> We cannot provide user identification as we do not collect it</li>
                    <li><strong>No Content Access:</strong> We cannot provide content as it is encrypted with client-side keys</li>
                    <li><strong>Limited Metadata:</strong> We can only provide basic metadata about paste existence and expiration</li>
                    <li><strong>Legal Challenges:</strong> We will challenge overbroad or inappropriate requests</li>
                    <li><strong>Transparency:</strong> We will notify users of legal requests when legally permitted</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    Our no-logs policy is designed to protect user privacy while maintaining compliance with applicable laws.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">7. Technical Implementation</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    Our no-logs policy is implemented through several technical measures:
                </p>
                
                <h3 class="text-xl font-medium text-yt-accent mb-4">7.1 Server Configuration</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Minimal Logging:</strong> Server configured to log only essential system events</li>
                    <li><strong>Log Rotation:</strong> Automatic log rotation and deletion after 7 days</li>
                    <li><strong>Secure Deletion:</strong> Deleted logs are securely overwritten multiple times</li>
                    <li><strong>Access Controls:</strong> Strict access controls on any remaining logs</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">7.2 Network Architecture</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>No IP Logging:</strong> IP addresses are not stored in persistent logs</li>
                    <li><strong>Request Processing:</strong> Request data is processed in memory only</li>
                    <li><strong>No Session Tracking:</strong> No persistent session data is maintained</li>
                    <li><strong>Stateless Design:</strong> Service designed to be stateless where possible</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">7.3 Data Processing</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6">
                    <li><strong>Client-Side Encryption:</strong> All content encrypted before transmission</li>
                    <li><strong>Zero-Knowledge Architecture:</strong> We cannot access encrypted content</li>
                    <li><strong>Minimal Metadata:</strong> Only essential metadata is collected</li>
                    <li><strong>Automatic Deletion:</strong> Expired content is automatically and permanently deleted</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">8. Temporary Processing Data</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    During normal operation, some data exists temporarily in system memory for request processing but is not persisted to logs or storage:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Request Headers:</strong> Processed in memory only, not logged</li>
                    <li><strong>Processing Metadata:</strong> Temporary data for request handling</li>
                    <li><strong>Encryption Operations:</strong> Cryptographic operations performed in memory</li>
                    <li><strong>Response Generation:</strong> Response data generated without logging</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    This temporary data is immediately discarded after response completion and is never written to persistent storage.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">9. Third-Party Services</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    We minimize the use of third-party services to reduce potential data exposure:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Careful Selection:</strong> Only services necessary for operation are used</li>
                    <li><strong>Privacy Evaluation:</strong> All third-party services are evaluated for privacy compliance</li>
                    <li><strong>Data Minimization:</strong> Third-party services receive minimal data</li>
                    <li><strong>Regular Audits:</strong> Third-party services are regularly audited for compliance</li>
                    <li><strong>Contractual Protection:</strong> Strict data protection agreements with all providers</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">10. Data Retention and Deletion</h2>
                <p class="text-yt-text leading-relaxed mb-4">Our data retention policies ensure minimal data storage:</p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Content Expiration:</strong> All user content is subject to expiration settings</li>
                    <li><strong>Automatic Deletion:</strong> Expired content is automatically and permanently deleted</li>
                    <li><strong>No Backups:</strong> We do not maintain backups of expired content</li>
                    <li><strong>Secure Deletion:</strong> Deleted data is securely overwritten and cannot be recovered</li>
                    <li><strong>Account Deletion:</strong> User accounts can be deleted, removing all associated data</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">11. Transparency and Verification</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    We are committed to transparency about our data practices:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Open Documentation:</strong> This policy provides detailed information about our practices</li>
                    <li><strong>Technical Verification:</strong> Our zero-knowledge architecture can be independently verified</li>
                    <li><strong>Security Audits:</strong> We encourage security researchers to audit our practices</li>
                    <li><strong>Regular Updates:</strong> This policy is updated to reflect any changes in our practices</li>
                    <li><strong>User Education:</strong> We provide information to help users understand our privacy protections</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">12. Policy Changes and Notifications</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    If our logging practices change in any way, we will:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Update This Policy:</strong> Clear explanations of any changes</li>
                    <li><strong>Provide Notice:</strong> Advance notice to users when possible</li>
                    <li><strong>Maintain Standards:</strong> Continue to maintain the highest possible privacy standards</li>
                    <li><strong>Transparent Communication:</strong> Clear communication about any changes</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    We are committed to maintaining our no-logs policy and will only make changes that enhance privacy protection.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">13. Independent Verification</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    Our no-logs policy and zero-knowledge architecture can be independently verified:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Open Source Code:</strong> Client-side encryption code is available for review</li>
                    <li><strong>Technical Documentation:</strong> Detailed technical documentation of our architecture</li>
                    <li><strong>Security Audits:</strong> Regular security audits by independent researchers</li>
                    <li><strong>Network Analysis:</strong> Users can analyze network traffic to verify our claims</li>
                    <li><strong>Transparency Reports:</strong> Regular reports on our privacy practices</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">14. Contact and Questions</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    If you have questions about our no-logs policy or want to report concerns about data collection, please contact us:
                </p>
                <div class="bg-yt-bg border border-yt-border rounded-lg p-6">
                    <p class="text-yt-text">
                        <strong>Privacy Team</strong><br>
                        DailyForever Privacy Department<br>
                        Email: dailyforever@proton.me<br>
                        Subject: No Logs Policy Question<br>
                        <br>
                        <strong>Security Researchers</strong><br>
                        Email: dailyforever@proton.me<br>
                        Subject: Security Audit Request<br>
                        <br>
                        <strong>Response Time:</strong> We will respond to all inquiries within 48 hours<br>
                        <strong>Confidentiality:</strong> We will keep your inquiries confidential
                    </p>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">15. Legal Disclaimer</h2>
                <p class="text-yt-text leading-relaxed">
                    This No Logs Policy is provided for informational purposes only and does not constitute legal advice. While we are committed to maintaining our no-logs policy, we may be required to comply with applicable laws and regulations. If you have questions about privacy law or your rights, please consult with a qualified attorney.
                </p>
            </section>
        </div>
    </div>
</div>
</div>
@endsection