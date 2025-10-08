@extends('layouts.app')

@section('title', 'DMCA Policy - DailyForever')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-slate-50/50 to-slate-100/30 dark:from-slate-900/50 dark:to-slate-800/30">
    <div class="max-w-4xl w-full px-4">
        <div class="content-card p-8">
        <h1 class="text-3xl font-bold text-yt-text mb-4" data-i18n="legal.dmca.title">Digital Millennium Copyright Act (DMCA) Policy</h1>
        <p class="text-yt-text-secondary mb-8 text-sm"><span data-i18n="legal.common.last_updated">Last updated:</span> September 23, 2025</p>

        <div class="bg-yt-accent/10 border border-yt-accent rounded-lg p-6 mb-8">
            <h2 class="text-lg font-semibold text-yt-accent mb-3" data-i18n="legal.dmca.banner_title">Balancing Copyright Protection with Privacy Rights</h2>
            <p class="text-yt-text leading-relaxed">
                DailyForever recognizes the importance of intellectual property rights while maintaining our commitment to user privacy. 
                Our DMCA policy is designed to respect copyright holders' rights while preserving the fundamental privacy rights of our users. 
                We believe that copyright protection and privacy rights are not mutually exclusive, and we work to balance both concerns 
                through transparent, fair, and privacy-preserving processes.
            </p>
        </div>

        <div class="prose prose-invert max-w-none space-y-10">
            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">1. Our Commitment to Copyright Protection</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    DailyForever respects the intellectual property rights of others and expects our users to do the same. We respond to valid copyright infringement notices in accordance with the Digital Millennium Copyright Act (DMCA) and applicable copyright laws worldwide.
                </p>
                <p class="text-yt-text leading-relaxed">
                    This policy outlines our procedures for handling copyright infringement claims and our commitment to protecting intellectual property while maintaining our zero-knowledge architecture.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">2. Zero-Knowledge Architecture Considerations</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    Our service implements zero-knowledge encryption, which creates unique considerations for copyright enforcement:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>We cannot access or view the content of encrypted pastes and files</li>
                    <li>We cannot proactively monitor content for copyright infringement</li>
                    <li>We rely on valid DMCA notices to identify potentially infringing content</li>
                    <li>We can only remove entire pastes/files, not edit or modify content</li>
                    <li>Content removal is permanent and cannot be undone</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    These limitations are inherent to our privacy-focused architecture and are necessary to protect user privacy and security.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">3. Filing a DMCA Takedown Notice</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    If you believe your copyrighted work has been posted on DailyForever without authorization, you may submit a DMCA takedown notice. Your notice must include all of the following information:
                </p>

                <h3 class="text-xl font-medium text-yt-accent mb-4">3.1 Required Information</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Physical or Electronic Signature:</strong> A signature of the copyright owner or authorized agent</li>
                    <li><strong>Copyright Identification:</strong> Clear identification of the copyrighted work claimed to be infringed</li>
                    <li><strong>Infringing Material Location:</strong> The specific URL(s) of the allegedly infringing content on DailyForever</li>
                    <li><strong>Contact Information:</strong> Your complete contact information (address, telephone number, email address)</li>
                    <li><strong>Good Faith Statement:</strong> A statement that you have a good faith belief that the use is not authorized</li>
                    <li><strong>Accuracy Statement:</strong> A statement that the information is accurate and that you are authorized to act</li>
                    <li><strong>Signature:</strong> Your physical or electronic signature</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">3.2 Additional Requirements</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Include the paste/file identifier (6-character code) if known</li>
                    <li>Provide a description of the copyrighted work and how it is being infringed</li>
                    <li>Include any additional information that may help us locate the content</li>
                    <li>Ensure the notice is sent from an email address associated with the copyright owner</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">4. How to Submit a DMCA Notice</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    DailyForever provides multiple channels for submitting DMCA notices to ensure accessibility and proper handling. 
                    We recommend using our Support system for the fastest processing and tracking of your request.
                </p>
                
                <h3 class="text-xl font-medium text-yt-accent mb-4">4.1 Preferred Method: Support System</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    For the most efficient processing, please submit your DMCA notice through our Support system:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Visit our <a href="{{ route('support.index') }}" class="text-link">Support Center</a></li>
                    <li>Select "DMCA Takedown Notice (Copyright)" as the report type</li>
                    <li>Complete the required fields with your copyright information</li>
                    <li>Provide the paste or file identifier if known</li>
                    <li>Submit your notice for review</li>
                </ul>
                
                <h3 class="text-xl font-medium text-yt-accent mb-4">4.2 Alternative Method: Direct Contact</h3>
                <p class="text-yt-text leading-relaxed mb-4">You may also submit your DMCA notice directly to our designated agent:</p>
                <div class="bg-yt-bg border border-yt-border rounded-lg p-6 mb-6">
                    <p class="text-yt-text">
                        <strong>DMCA Agent</strong><br>
                        DailyForever Legal Department<br>
                        Email: dailyforever@proton.me<br>
                        Subject: DMCA Takedown Notice<br>
                        <br>
                        <strong>Response Time:</strong> We will respond to valid notices within 24-48 hours<br>
                        <strong>Processing Time:</strong> Content removal typically occurs within 24 hours of verification
                    </p>
                </div>
                <p class="text-yt-text leading-relaxed">
                    Please send your notice via email with "DMCA Takedown Notice" in the subject line. We do not accept DMCA notices through other channels.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">5. Our Response Process</h2>
                <p class="text-yt-text leading-relaxed mb-4">Upon receiving a DMCA notice, we will:</p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Review the notice for completeness and validity within 24 hours</li>
                    <li>Verify the copyright ownership and authorization of the claimant</li>
                    <li>Locate the allegedly infringing content using the provided information</li>
                    <li>Remove or disable access to the content if the notice is valid</li>
                    <li>Make reasonable efforts to notify the content uploader (if possible)</li>
                    <li>Document the takedown for our records and legal compliance</li>
                    <li>Respond to the claimant with confirmation of action taken</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    Due to our zero-knowledge architecture, we cannot provide copies of the removed content to the claimant.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">6. Counter-Notification Process</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    If you believe your content was removed in error, you may submit a counter-notification. Your counter-notification must include:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Your Signature:</strong> Physical or electronic signature</li>
                    <li><strong>Content Identification:</strong> Identification of the removed content and its location before removal</li>
                    <li><strong>Good Faith Statement:</strong> A statement under penalty of perjury that removal was due to mistake or misidentification</li>
                    <li><strong>Contact Information:</strong> Your name, address, and telephone number</li>
                    <li><strong>Consent to Jurisdiction:</strong> Consent to the jurisdiction of the federal court in your district</li>
                    <li><strong>Service of Process:</strong> Consent to accept service of process from the claimant</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    Send counter-notifications to: dailyforever@proton.me
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">7. Repeat Infringer Policy</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    While DailyForever does not require user accounts for basic usage, we may implement technical measures to prevent repeat infringement:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Blocking access from IP addresses associated with repeat infringement</li>
                    <li>Implementing rate limiting for content creation</li>
                    <li>Enhanced monitoring of reported content patterns</li>
                    <li>Cooperation with law enforcement for serious violations</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    These measures are implemented when technically feasible and necessary to prevent ongoing copyright infringement.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">8. Limitations and Considerations</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    Due to our zero-knowledge architecture, we have the following limitations:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>No Content Review:</strong> We cannot provide copies of encrypted content for review</li>
                    <li><strong>No Content Modification:</strong> We cannot edit or modify content, only remove entire pastes/files</li>
                    <li><strong>Permanent Removal:</strong> Content removal is permanent and cannot be undone</li>
                    <li><strong>No User Identification:</strong> We cannot identify users who posted content due to our no-logs policy</li>
                    <li><strong>Limited Metadata:</strong> We can only provide basic metadata about content existence</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">9. False Claims and Penalties</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    Submitting false or fraudulent DMCA notices may result in serious legal consequences:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Legal liability for damages caused by false claims</li>
                    <li>Potential criminal penalties for perjury</li>
                    <li>Liability for attorney's fees and costs</li>
                    <li>Permanent blocking of future DMCA notices from the sender</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    We reserve the right to ignore notices that appear to be false, fraudulent, or submitted in bad faith. We may also report such notices to appropriate authorities.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">10. International Copyright Protection</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    While this policy is based on U.S. DMCA law, we respect international copyright laws and will respond to valid copyright infringement notices from any jurisdiction. International notices should include:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Clear identification of the copyrighted work</li>
                    <li>Evidence of copyright ownership in the relevant jurisdiction</li>
                    <li>Specific location of the infringing content</li>
                    <li>Statement of good faith belief that the use is unauthorized</li>
                    <li>Contact information for the rights holder or authorized agent</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">11. Fair Use Considerations</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    We recognize that some uses of copyrighted material may be protected under fair use or similar doctrines. However, due to our zero-knowledge architecture, we cannot evaluate fair use claims and must rely on the copyright holder's representation that the use is unauthorized.
                </p>
                <p class="text-yt-text leading-relaxed">
                    If you believe your use of copyrighted material constitutes fair use, you may submit a counter-notification as described above.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">12. Policy Updates</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    We may update this DMCA policy as needed to address new legal requirements or improve our procedures. Changes will be posted on this page with an updated revision date.
                </p>
                <p class="text-yt-text leading-relaxed">
                    We will notify users of material changes through our Service and, when possible, via email to registered users.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">13. Contact Information</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    For questions about this DMCA policy or to submit notices, contact our designated agent:
                </p>
                <div class="bg-yt-bg border border-yt-border rounded-lg p-6">
                    <p class="text-yt-text">
                        <strong>DMCA Agent</strong><br>
                        DailyForever Legal Department<br>
                        Email: dailyforever@proton.me<br>
                        Subject: DMCA Takedown Notice<br>
                        <br>
                        <strong>For Counter-Notifications:</strong><br>
                        Email: dailyforever@proton.me<br>
                        Subject: DMCA Counter-Notification<br>
                        <br>
                        <strong>General Legal Inquiries:</strong><br>
                        Email: dailyforever@proton.me<br>
                        <br>
                        <strong>Response Time:</strong> 24-48 hours for valid notices<br>
                        <strong>Business Hours:</strong> Monday-Friday, 9 AM - 5 PM EST
                    </p>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">14. Legal Disclaimer</h2>
                <p class="text-yt-text leading-relaxed">
                    This DMCA policy is provided for informational purposes only and does not constitute legal advice. If you have questions about copyright law or your rights, please consult with a qualified attorney. DailyForever is not responsible for any legal consequences arising from the use of this policy or our Service.
                </p>
            </section>
        </div>
    </div>
</div>
</div>
@endsection