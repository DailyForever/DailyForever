@extends('layouts.app')

@section('title', 'Support - DailyForever')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="content-card p-8">
        <h1 class="text-3xl font-bold text-yt-text mb-6" data-i18n="support.title">Support Center</h1>
        <p class="text-yt-text-secondary mb-8 text-lg" data-i18n="support.subtitle">Get help, report issues, or contact our team. We're here to assist you with any questions or concerns.</p>

        @if(session('success'))
            <div class="bg-yt-success/20 border border-yt-success rounded-lg p-4 mb-6">
                <p class="text-yt-success font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-yt-error/20 border border-yt-error rounded-lg p-4 mb-6">
                <p class="text-yt-error font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Support Form -->
            <div class="space-y-6">
                <h2 class="text-2xl font-semibold text-yt-text mb-4" data-i18n="support.form.title">Submit a Report</h2>
                
                <form method="POST" action="{{ route('support.submit') }}" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="type" class="block text-sm font-medium text-yt-text mb-2" data-i18n="support.form.report_type">Report Type</label>
                        <select id="type" name="type" class="input-field w-full px-3 py-2 text-sm" required>
                            <option value="" data-i18n="support.form.type.select">Select a report type</option>
                            <option value="dmca" data-i18n="support.form.type.dmca">DMCA Takedown Notice</option>
                            <option value="abuse" data-i18n="support.form.type.abuse">Abuse Report</option>
                            <option value="general" data-i18n="support.form.type.general">General Support</option>
                            <option value="security" data-i18n="support.form.type.security">Security Issue</option>
                            <option value="appeal" data-i18n="support.form.type.appeal">Policy Appeal</option>
                        </select>
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-medium text-yt-text mb-2" data-i18n="support.form.subject_label">Subject</label>
                        <input type="text" id="subject" name="subject" class="input-field w-full px-3 py-2 text-sm" data-i18n-attr="placeholder" data-i18n-placeholder="support.form.subject_placeholder" placeholder="Brief description of your issue" required>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-yt-text mb-2" data-i18n="support.form.description_label">Description</label>
                        <textarea id="description" name="description" rows="6" class="input-field w-full px-3 py-2 text-sm" data-i18n-attr="placeholder" data-i18n-placeholder="support.form.description_placeholder" placeholder="Please provide detailed information about your issue..." required></textarea>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-yt-text mb-2" data-i18n="support.form.email_label">Email (Optional)</label>
                        <input type="email" id="email" name="email" class="input-field w-full px-3 py-2 text-sm" data-i18n-attr="placeholder" data-i18n-placeholder="support.form.email_placeholder" placeholder="your@email.com">
                        <p class="text-xs text-yt-text-secondary mt-1" data-i18n="support.form.email_help">We'll use this to respond to your report. If not provided, we'll respond through this page.</p>
                    </div>

                    <!-- DMCA Specific Fields -->
                    <div id="dmca-fields" class="hidden space-y-4">
                        <h3 class="text-lg font-medium text-yt-accent" data-i18n="support.dmca.title">DMCA Information</h3>
                        
                        <div>
                            <label for="paste_identifier" class="block text-sm font-medium text-yt-text mb-2" data-i18n="support.dmca.identifier_label">Paste/File Identifier</label>
                            <input type="text" id="paste_identifier" name="paste_identifier" class="input-field w-full px-3 py-2 text-sm" data-i18n-attr="placeholder" data-i18n-placeholder="support.dmca.identifier_placeholder" placeholder="e.g., aB3xK9">
                            <p class="text-xs text-yt-text-secondary mt-1" data-i18n="support.dmca.identifier_help">The 6-character identifier of the content you want to report</p>
                        </div>

                        <div>
                            <label for="copyright_work" class="block text-sm font-medium text-yt-text mb-2" data-i18n="support.dmca.work_label">Copyrighted Work</label>
                            <textarea id="copyright_work" name="copyright_work" rows="3" class="input-field w-full px-3 py-2 text-sm" data-i18n-attr="placeholder" data-i18n-placeholder="support.dmca.work_placeholder" placeholder="Describe the copyrighted work that has been infringed"></textarea>
                        </div>

                        <div>
                            <label for="authorization" class="block text-sm font-medium text-yt-text mb-2" data-i18n="support.dmca.auth_label">Authorization Statement</label>
                            <textarea id="authorization" name="authorization" rows="3" class="input-field w-full px-3 py-2 text-sm" data-i18n-attr="placeholder" data-i18n-placeholder="support.dmca.auth_placeholder" placeholder="I have a good faith belief that the use is not authorized by the copyright owner..."></textarea>
                        </div>

                        <div>
                            <label for="contact_info" class="block text-sm font-medium text-yt-text mb-2" data-i18n="support.dmca.contact_label">Contact Information</label>
                            <textarea id="contact_info" name="contact_info" rows="2" class="input-field w-full px-3 py-2 text-sm" data-i18n-attr="placeholder" data-i18n-placeholder="support.dmca.contact_placeholder" placeholder="Your full name, address, phone number, and email"></textarea>
                        </div>
                    </div>

                    <!-- Abuse Report Specific Fields -->
                    <div id="abuse-fields" class="hidden space-y-4">
                        <h3 class="text-lg font-medium text-yt-accent" data-i18n="support.abuse.title">Abuse Report Information</h3>
                        
                        <div>
                            <label for="paste_identifier" class="block text-sm font-medium text-yt-text mb-2" data-i18n="support.abuse.identifier_label">Paste/File Identifier</label>
                            <input type="text" id="abuse_paste_identifier" name="paste_identifier" class="input-field w-full px-3 py-2 text-sm" data-i18n-attr="placeholder" data-i18n-placeholder="support.abuse.identifier_placeholder" placeholder="e.g., aB3xK9">
                            <p class="text-xs text-yt-text-secondary mt-1" data-i18n="support.abuse.identifier_help">The 6-character identifier of the content you want to report</p>
                        </div>

                        <div>
                            <label for="violation_type" class="block text-sm font-medium text-yt-text mb-2" data-i18n="support.abuse.type_label">Type of Violation</label>
                            <select id="violation_type" name="violation_type" class="input-field w-full px-3 py-2 text-sm">
                                <option value="" data-i18n="support.abuse.type_select">Select violation type</option>
                                <option value="illegal_content" data-i18n="support.abuse.types.illegal">Illegal Content</option>
                                <option value="harmful_content" data-i18n="support.abuse.types.harmful">Harmful Content</option>
                                <option value="harassment" data-i18n="support.abuse.types.harassment">Harassment</option>
                                <option value="spam" data-i18n="support.abuse.types.spam">Spam</option>
                                <option value="malware" data-i18n="support.abuse.types.malware">Malware</option>
                                <option value="other" data-i18n="support.abuse.types.other">Other</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary px-6 py-3 text-sm font-medium w-full" data-i18n="support.form.submit">Submit Report</button>
                </form>
            </div>

            <!-- Support Information -->
            <div class="space-y-6">
                <h2 class="text-2xl font-semibold text-yt-text mb-4" data-i18n="support.info.title">Support Information</h2>
                
                <!-- Report Types -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-yt-accent" data-i18n="support.info.report_types">Report Types</h3>
                    
                    <div class="space-y-3">
                        <div class="bg-yt-bg border border-yt-border rounded-lg p-4">
                            <h4 class="font-medium text-yt-text mb-2" data-i18n="support.info.dmca.title">DMCA Takedown Notice</h4>
                            <p class="text-sm text-yt-text-secondary" data-i18n="support.info.dmca.desc">Report copyright infringement. Include the paste identifier and detailed information about the copyrighted work.</p>
                        </div>

                        <div class="bg-yt-bg border border-yt-border rounded-lg p-4">
                            <h4 class="font-medium text-yt-text mb-2" data-i18n="support.info.abuse.title">Abuse Report</h4>
                            <p class="text-sm text-yt-text-secondary" data-i18n="support.info.abuse.desc">Report content that violates our Acceptable Use Policy, including illegal, harmful, or inappropriate content.</p>
                        </div>

                        <div class="bg-yt-bg border border-yt-border rounded-lg p-4">
                            <h4 class="font-medium text-yt-text mb-2" data-i18n="support.info.general.title">General Support</h4>
                            <p class="text-sm text-yt-text-secondary" data-i18n="support.info.general.desc">Questions about using our service, technical issues, or general inquiries.</p>
                        </div>

                        <div class="bg-yt-bg border border-yt-border rounded-lg p-4">
                            <h4 class="font-medium text-yt-text mb-2" data-i18n="support.info.security.title">Security Issue</h4>
                            <p class="text-sm text-yt-text-secondary" data-i18n="support.info.security.desc">Report security vulnerabilities, suspicious activity, or potential security threats.</p>
                        </div>

                        <div class="bg-yt-bg border border-yt-border rounded-lg p-4">
                            <h4 class="font-medium text-yt-text mb-2" data-i18n="support.info.appeal.title">Policy Appeal</h4>
                            <p class="text-sm text-yt-text-secondary" data-i18n="support.info.appeal.desc">Appeal content removal or account restrictions that you believe were made in error.</p>
                        </div>
                    </div>
                </div>

                <!-- Response Times -->
                <div class="bg-yt-elevated border border-yt-border rounded-lg p-4">
                    <h3 class="text-lg font-medium text-yt-accent mb-3" data-i18n="support.info.response.title">Response Times</h3>
                    <ul class="space-y-2 text-sm text-yt-text-secondary">
                        <li data-i18n="support.info.response.dmca"><strong>DMCA Notices:</strong> 24-48 hours</li>
                        <li data-i18n="support.info.response.abuse"><strong>Abuse Reports:</strong> 24-48 hours</li>
                        <li data-i18n="support.info.response.security"><strong>Security Issues:</strong> 12-24 hours</li>
                        <li data-i18n="support.info.response.general"><strong>General Support:</strong> 48-72 hours</li>
                        <li data-i18n="support.info.response.appeal"><strong>Policy Appeals:</strong> 5-7 business days</li>
                    </ul>
                </div>

                <!-- Quick Links -->
                <div class="bg-yt-elevated border border-yt-border rounded-lg p-4">
                    <h3 class="text-lg font-medium text-yt-accent mb-3" data-i18n="support.info.quick.title">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('legal.terms') }}" class="text-link" data-i18n="support.info.quick.terms">Terms of Service</a></li>
                        <li><a href="{{ route('legal.privacy') }}" class="text-link" data-i18n="support.info.quick.privacy">Privacy Policy</a></li>
                        <li><a href="{{ route('legal.dmca') }}" class="text-link" data-i18n="support.info.quick.dmca">DMCA Policy</a></li>
                        <li><a href="{{ route('legal.acceptable-use') }}" class="text-link" data-i18n="support.info.quick.acceptable_use">Acceptable Use Policy</a></li>
                        <li><a href="{{ route('legal.no-logs') }}" class="text-link" data-i18n="support.info.quick.no_logs">No Logs Policy</a></li>
                        <li><a href="{{ route('legal.philosophy') }}" class="text-link" data-i18n="support.info.quick.philosophy">Our Philosophy</a></li>
                    </ul>
                </div>

                <!-- Contact Information -->
                <div class="bg-yt-elevated border border-yt-border rounded-lg p-4">
                    <h3 class="text-lg font-medium text-yt-accent mb-3" data-i18n="support.info.contact.title">Direct Contact</h3>
                    <div class="space-y-2 text-sm text-yt-text-secondary">
                        <p><strong data-i18n="support.info.contact.general_label">General Support:</strong> general@dailyforever.com</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const dmcaFields = document.getElementById('dmca-fields');
    const abuseFields = document.getElementById('abuse-fields');

    typeSelect.addEventListener('change', function() {
        // Hide all conditional fields
        dmcaFields.classList.add('hidden');
        abuseFields.classList.add('hidden');

        // Show relevant fields based on selection
        if (this.value === 'dmca') {
            dmcaFields.classList.remove('hidden');
        } else if (this.value === 'abuse') {
            abuseFields.classList.remove('hidden');
        }
    });
});
</script>
@endsection
