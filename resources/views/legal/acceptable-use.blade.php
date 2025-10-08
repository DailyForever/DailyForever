@extends('layouts.app')

@section('title', 'Acceptable Use Policy - DailyForever')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-slate-50/50 to-slate-100/30 dark:from-slate-900/50 dark:to-slate-800/30">
    <div class="max-w-4xl w-full px-4">
        <div class="content-card p-8">
        <h1 class="text-3xl font-bold text-yt-text mb-4" data-i18n="legal.acceptable_use.title">Acceptable Use Policy</h1>
        <p class="text-yt-text-secondary mb-8 text-sm"><span data-i18n="legal.common.last_updated">Last updated:</span> September 23, 2025</p>

        <div class="bg-yt-accent/10 border border-yt-accent rounded-lg p-6 mb-8">
            <h2 class="text-lg font-semibold text-yt-accent mb-3" data-i18n="legal.acceptable_use.banner_title">Responsible Use of Privacy Tools</h2>
            <p class="text-yt-text leading-relaxed">
                DailyForever provides powerful privacy tools that must be used responsibly and ethically. While we strongly believe in 
                the fundamental right to privacy and encryption, we also recognize that these tools carry responsibilities. This policy 
                outlines the acceptable uses of our platform while maintaining our commitment to protecting legitimate privacy rights 
                and preventing abuse that could harm individuals or society.
            </p>
        </div>

        <div class="prose prose-invert max-w-none space-y-10">
            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">1. Purpose and Scope</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    This Acceptable Use Policy ("AUP") outlines the permitted and prohibited uses of DailyForever's encrypted paste and file sharing service. By using our Service, you agree to comply with this policy and understand that violations may result in removal of content, restriction of access, or other appropriate actions.
                </p>
                <p class="text-yt-text leading-relaxed">
                    This policy applies to all users of our Service, whether using guest access or registered accounts. It is designed to protect our users, maintain service integrity, and ensure compliance with applicable laws and regulations.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">2. Permitted Uses</h2>
                <p class="text-yt-text leading-relaxed mb-4">DailyForever may be used for legitimate purposes including:</p>
                
                <h3 class="text-xl font-medium text-yt-accent mb-4">2.1 Professional and Educational</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Sharing code snippets, documentation, and technical content</li>
                    <li>Collaborative development and programming projects</li>
                    <li>Educational materials and research data</li>
                    <li>Secure communication of sensitive business information</li>
                    <li>Legal and compliance document sharing</li>
                    <li>Journalistic and investigative work</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">2.2 Personal and Creative</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Personal notes, journals, and creative writing</li>
                    <li>Private communication and messaging</li>
                    <li>File sharing with friends and family</li>
                    <li>Backup and archival of personal data</li>
                    <li>Any lawful purpose that complies with this policy</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">3. Prohibited Content</h2>
                <p class="text-yt-text leading-relaxed mb-4">You may not use DailyForever to store, share, or transmit:</p>
                
                <h3 class="text-xl font-medium text-yt-accent mb-4">3.1 Illegal Content</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Content that violates any applicable local, state, national, or international laws</li>
                    <li>Content that facilitates illegal activities or provides instructions for illegal acts</li>
                    <li>Stolen or pirated intellectual property, including copyrighted material without permission</li>
                    <li>Content that violates export control laws or trade restrictions</li>
                    <li>Content related to illegal gambling, drug trafficking, or money laundering</li>
                    <li>Content that promotes or incites violence or terrorism</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">3.2 Harmful and Malicious Content</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Malware, viruses, trojans, or any other malicious code</li>
                    <li>Content designed to harm computer systems, networks, or devices</li>
                    <li>Instructions for creating weapons, explosives, or other dangerous devices</li>
                    <li>Content that promotes self-harm, suicide, or dangerous activities</li>
                    <li>Phishing attempts, scams, or fraudulent schemes</li>
                    <li>Content designed to exploit security vulnerabilities</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">3.3 Abusive and Harassment Content</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Harassment, threats, intimidation, or stalking content</li>
                    <li>Content that promotes hate speech, discrimination, or prejudice</li>
                    <li>Defamatory, libelous, or slanderous statements</li>
                    <li>Content that violates privacy rights or contains personal information without consent</li>
                    <li>Content that targets individuals or groups based on protected characteristics</li>
                    <li>Cyberbullying or online harassment of any kind</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">3.4 Inappropriate and Offensive Content</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Obscene, pornographic, or sexually explicit material</li>
                    <li>Content involving minors in inappropriate contexts</li>
                    <li>Graphic violence, gore, or disturbing imagery descriptions</li>
                    <li>Content that glorifies or promotes violence</li>
                    <li>Extreme or graphic content that may be disturbing to others</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">3.5 Spam and Commercial Abuse</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6">
                    <li>Spam, unsolicited commercial communications, or bulk messaging</li>
                    <li>Pyramid schemes, multi-level marketing, or other deceptive business practices</li>
                    <li>Content designed to artificially inflate traffic or engagement</li>
                    <li>Excessive or repetitive posting of similar content</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">4. Prohibited Activities</h2>
                <p class="text-yt-text leading-relaxed mb-4">You may not engage in the following activities:</p>
                
                <h3 class="text-xl font-medium text-yt-accent mb-4">4.1 Security Violations</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Attempt to circumvent security measures or access controls</li>
                    <li>Attempt to gain unauthorized access to our systems or other users' content</li>
                    <li>Attempt to decrypt other users' content without authorization</li>
                    <li>Use automated systems to abuse or overload our servers</li>
                    <li>Interfere with the proper functioning of the Service</li>
                    <li>Exploit vulnerabilities or attempt to compromise system security</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">4.2 Abuse and Misuse</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Use the service to distribute spam or unsolicited communications</li>
                    <li>Engage in activities that could damage or impair service functionality</li>
                    <li>Misrepresent your identity or affiliation</li>
                    <li>Interfere with other users' ability to use the service</li>
                    <li>Use the service for any purpose that violates applicable laws</li>
                    <li>Create multiple accounts to circumvent restrictions or policies</li>
                </ul>

                <h3 class="text-xl font-medium text-yt-accent mb-4">4.3 Privacy Violations</h3>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6">
                    <li>Share personal information of others without their consent</li>
                    <li>Collect or harvest user information from our Service</li>
                    <li>Use the service to stalk, harass, or intimidate others</li>
                    <li>Violate others' privacy rights or expectations</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">5. Intellectual Property Rights</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    You must respect intellectual property rights and only share content that you own or have proper authorization to share. This includes:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Copyrighted material (text, code, images, music, videos, etc.)</li>
                    <li>Trademarks and service marks</li>
                    <li>Patents and trade secrets</li>
                    <li>Right of publicity and privacy rights</li>
                    <li>Any other intellectual property rights</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    If you believe your intellectual property rights have been violated, please refer to our DMCA Policy for information on how to file a takedown notice.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">6. Commercial Use Guidelines</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    DailyForever may be used for legitimate business purposes, but with the following restrictions:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Use must comply with all applicable laws and regulations</li>
                    <li>No pyramid schemes, multi-level marketing, or other deceptive practices</li>
                    <li>No excessive commercial use that impacts service availability for other users</li>
                    <li>Respect intellectual property rights of others</li>
                    <li>Comply with all terms of this Acceptable Use Policy</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    If you plan to use our Service for commercial purposes, please ensure your use is appropriate and does not violate any of our policies.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">7. Privacy and Consent</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    When sharing content that contains personal information about others, you must:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Obtain proper consent before sharing personal information</li>
                    <li>Ensure you have legal authority to share such information</li>
                    <li>Respect others' privacy rights and expectations</li>
                    <li>Comply with applicable privacy laws and regulations</li>
                    <li>Consider the potential impact on others' privacy</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    Remember that our zero-knowledge architecture means we cannot help you recover content once it's shared, so ensure you have proper authorization before sharing sensitive information.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">8. Enforcement and Consequences</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    While our zero-knowledge architecture prevents us from proactively monitoring content, we will investigate reports of policy violations and take appropriate action, which may include:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li><strong>Content Removal:</strong> Immediate removal of violating content</li>
                    <li><strong>Access Restrictions:</strong> Temporary or permanent restriction of service access</li>
                    <li><strong>Account Suspension:</strong> Suspension or termination of user accounts</li>
                    <li><strong>IP Blocking:</strong> Blocking access from specific IP addresses</li>
                    <li><strong>Legal Action:</strong> Cooperation with law enforcement for serious violations</li>
                    <li><strong>Rate Limiting:</strong> Implementation of rate limits to prevent abuse</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    The severity of consequences will depend on the nature and severity of the violation, as well as any previous violations.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">9. Reporting Violations</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    DailyForever provides a comprehensive reporting system to address policy violations while maintaining user privacy. 
                    We encourage responsible reporting of content that violates this policy through our Support system.
                </p>
                
                <h3 class="text-xl font-medium text-yt-accent mb-4">9.1 How to Report</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    To report policy violations, please use our Support system:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Visit our <a href="{{ route('support.index') }}" class="text-link">Support Center</a></li>
                    <li>Select "Abuse Report (Policy Violation)" as the report type</li>
                    <li>Specify the type of violation from the provided categories</li>
                    <li>Provide the paste or file identifier if known</li>
                    <li>Describe the violation in detail</li>
                    <li>Submit your report for review</li>
                </ul>
                
                <h3 class="text-xl font-medium text-yt-accent mb-4">9.2 Information to Include</h3>
                <p class="text-yt-text leading-relaxed mb-4">
                    When reporting violations, please provide:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>The specific URL or identifier of the violating content</li>
                    <li>A detailed description of the violation</li>
                    <li>Any relevant supporting information</li>
                    <li>Your contact information (optional but helpful for follow-up)</li>
                </ul>
                <div class="bg-yt-bg border border-yt-border rounded-lg p-6 mb-6">
                    <p class="text-yt-text">
                        <strong>Report Violations To:</strong><br>
                        Email: dailyforever@proton.me<br>
                        Subject: Policy Violation Report<br>
                        <br>
                        <strong>Response Time:</strong> We will investigate reports within 24-48 hours<br>
                        <strong>Confidentiality:</strong> We will keep your report confidential unless required by law
                    </p>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">10. Appeals Process</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    If you believe your content was removed in error or you were restricted unfairly, you may appeal by:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Providing a detailed explanation of why you believe the action was incorrect</li>
                    <li>Including any relevant evidence or context</li>
                    <li>Demonstrating that your content complies with this policy</li>
                    <li>Providing your contact information for follow-up</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    Send appeals to: dailyforever@proton.me with "Policy Appeal" in the subject line. We will review appeals within 5-7 business days.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">11. International Compliance</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    Users are responsible for ensuring their use of our Service complies with applicable laws in their jurisdiction. This includes:
                </p>
                <ul class="list-disc list-inside text-yt-text space-y-2 ml-6 mb-6">
                    <li>Local content restrictions and censorship laws</li>
                    <li>Data protection and privacy regulations</li>
                    <li>Intellectual property laws</li>
                    <li>Export control and trade restrictions</li>
                    <li>Any other applicable local laws</li>
                </ul>
                <p class="text-yt-text leading-relaxed">
                    We may restrict access to our Service in certain jurisdictions if required by applicable law.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">12. Policy Updates</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    We may update this Acceptable Use Policy as needed to address new concerns, legal requirements, or improve our guidelines. Changes will be posted on this page with an updated revision date.
                </p>
                <p class="text-yt-text leading-relaxed">
                    We will notify users of material changes through our Service and, when possible, via email to registered users. Your continued use of the Service after changes constitutes acceptance of the updated policy.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">13. Questions and Clarifications</h2>
                <p class="text-yt-text leading-relaxed mb-4">
                    If you have questions about this Acceptable Use Policy or need clarification about specific use cases, please contact us:
                </p>
                <div class="bg-yt-bg border border-yt-border rounded-lg p-6">
                    <p class="text-yt-text">
                        <strong>General Questions:</strong><br>
                        Email: dailyforever@proton.me<br>
                        Subject: Acceptable Use Policy Question<br>
                        <br>
                        <strong>Report Violations:</strong><br>
                        Email: dailyforever@proton.me<br>
                        Subject: Policy Violation Report<br>
                        <br>
                        <strong>Appeals:</strong><br>
                        Email: dailyforever@proton.me<br>
                        Subject: Policy Appeal<br>
                        <br>
                        <strong>Response Time:</strong> 24-48 hours for most inquiries
                    </p>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-yt-text mb-6">14. Legal Disclaimer</h2>
                <p class="text-yt-text leading-relaxed">
                    This Acceptable Use Policy is provided for informational purposes only and does not constitute legal advice. If you have questions about applicable laws or your rights, please consult with a qualified attorney. DailyForever reserves the right to modify this policy at any time and to take appropriate action to enforce compliance.
                </p>
            </section>
        </div>
    </div>
</div>
</div>
@endsection