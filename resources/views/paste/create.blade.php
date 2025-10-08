@extends('layouts.app')

@section('title', 'Create Encrypted Paste - DailyForever')
@section('meta_description', 'Create an encrypted paste with zero-knowledge encryption. No data collection and complete privacy protection. Share sensitive text securely with DailyForever.')
@section('keywords', 'encrypted pastebin, create encrypted paste, zero-knowledge encryption, private paste, secure notes, end-to-end encryption, no data collection')
@section('og_title', 'Create Encrypted Paste - DailyForever')
@section('og_description', 'Create an encrypted paste with zero-knowledge encryption. No data collection and complete privacy protection.')
@section('twitter_title', 'Create Encrypted Paste - DailyForever')
@section('twitter_description', 'Create an encrypted paste with zero-knowledge encryption. No data collection and complete privacy protection.')

@section('content')
 <div id="pasteCreateRoot" class="w-full" 
      data-paste-store-url="{{ route('paste.store') }}"
      data-auth="{{ auth()->check() ? '1' : '0' }}">
    <div class="content-card">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-yt-text mb-3" data-i18n="paste.create.section_title">Share Text Securely</h1>
            <p class="text-yt-text-secondary text-base" data-i18n="paste.create.section_desc">Paste or type your text below — we'll encrypt it instantly before anyone (including us) can see it.</p>
        </div>
        
        <!-- AdSense Banner Ad -->
        <div class="mb-6">
            <x-adsense 
                slot="{{ env('ADSENSE_BANNER_SLOT') }}" 
                format="auto"
                class="w-full"
            />
        </div>
        
        @guest
        <div class="bg-blue-100 dark:bg-blue-900/20 border border-blue-300 dark:border-blue-800 rounded-lg p-4 mb-6">
            <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 text-blue-900 dark:text-blue-300 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                   <h2 class="text-sm font-bold dark:font-medium text-theme-switch-important" data-i18n="paste.create.guest.title">Guest Mode</h2>
                   <p class="text-sm font-bold dark:font-normal text-theme-switch-important mt-1" data-i18n="paste.create.guest.message">You're creating a paste as a guest.</p>
                    <a href="{{ route('auth.login.show') }}" class="text-blue-900 dark:text-blue-300 hover:text-blue-950 dark:hover:text-blue-200 underline font-bold" data-i18n="paste.create.guest.link">Register or login to access more features</a>
                </div>
            </div>
        </div>
        @endguest

        <form id="pasteForm" class="space-y-6">
            @csrf
            <!-- Quick Mode (Default) -->
            <div id="quickMode" class="space-y-6">
                <div class="relative">
                    <textarea 
                        id="content" 
                        name="content" 
                        rows="12" 
                        class="w-full px-6 py-5 text-base rounded-xl border-2 border-yt-border bg-yt-elevated/40 focus:border-emerald-500/50 focus:bg-yt-elevated/60 transition-all resize-none"
                        data-i18n-attr="placeholder" data-i18n-placeholder="paste.create.placeholder_simple"
                        placeholder="Type or paste your text here...\n\nYour text will be encrypted instantly and only people with your link can read it."
                        required
                    ></textarea>
                    <div class="absolute bottom-3 right-3 text-xs text-yt-text-secondary">
                        <span id="charCount">0</span> <span data-i18n="common.characters">characters</span>
                    </div>
                </div>
                
                <!-- Simple Options -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-yt-text mb-2" data-i18n="common.expires_simple">How long should we keep it?</label>
                        <select 
                            id="expires_in" 
                            name="expires_in" 
                            class="input-field w-full px-4 py-3 text-base"
                        >
                            <option value="1hour" data-i18n="common.options.1hour_simple">1 Hour</option>
                            <option value="1day" selected data-i18n="common.options.1day_simple">24 Hours (recommended)</option>
                            <option value="1week" data-i18n="common.options.1week_simple">1 Week</option>
                            <option value="never" data-i18n="common.options.never_simple">Keep forever</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-yt-text mb-2" data-i18n="common.password_simple">Add password protection?</label>
                        <input 
                            id="password" 
                            type="password" 
                            class="input-field w-full px-4 py-3 text-base" 
                            data-i18n-attr="placeholder" 
                            data-i18n-placeholder="common.password.placeholder_simple" 
                            placeholder="Optional password..."
                        />
                    </div>
                </div>
            </div>
            
            <!-- Advanced Mode (Hidden by default) -->
            <div id="advancedMode" class="hidden">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
                    <div class="lg:col-span-2">
                        <div class="editor-card rounded-lg border border-yt-border bg-yt-elevated/40 overflow-hidden">
                            <div class="px-2 sm:px-3 py-2 border-b border-yt-border bg-yt-elevated/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-medium text-yt-text" data-i18n="editor.editor">Editor</span>
                                </div>
                                <div class="flex flex-wrap items-center gap-1 sm:gap-2">
                                    <select id="syntax_select" class="px-2 py-1 text-[11px] bg-yt-border/40 border border-yt-border rounded">
                                        <option value="auto" selected>Auto</option>
                                        <option>plaintext</option>
                                        <option>markdown</option>
                                        <option>json</option>
                                        <option>javascript</option>
                                        <option>typescript</option>
                                        <option>python</option>
                                        <option>php</option>
                                        <option>go</option>
                                        <option>rust</option>
                                        <option>java</option>
                                        <option>c</option>
                                        <option>cpp</option>
                                        <option>bash</option>
                                        <option>sql</option>
                                        <option>html</option>
                                        <option>css</option>
                                        <option>yaml</option>
                                    </select>
                                    <button type="button" id="wrapToggle" class="text-[11px] px-2 py-1 bg-yt-border text-yt-text rounded hover:bg-yt-elevated transition-colors" data-i18n="editor.no_wrap">No Wrap</button>
                                    <select id="tabSize" class="px-2 py-1 text-[11px] bg-yt-border/40 border border-yt-border rounded">
                                        <option value="2" selected>Tab: 2</option>
                                        <option value="4">Tab: 4</option>
                                    </select>
                                </div>
                            </div>
                            <div class="editor-grid">
                                <pre id="lineGutter" class="line-gutter">1\n</pre>
                                <textarea 
                                    id="contentAdvanced" 
                                    name="contentAdvanced" 
                                    rows="20" 
                                    class="w-full font-mono text-sm bg-transparent outline-none"
                                    data-i18n-attr="placeholder" data-i18n-placeholder="paste.create.placeholder"
                                    placeholder="Start typing or paste text… It will be encrypted before sending."
                                ></textarea>
                            </div>
                            <div class="px-2 sm:px-3 py-2 border-t border-yt-border bg-yt-elevated/60 text-[10px] sm:text-[11px] text-yt-text-secondary flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                                <span data-i18n="paste.create.tip_shortcut">Tip: Press Ctrl/⌘ + Enter to create the paste</span>
                                <span id="charCountAdvanced">0 chars</span>
                            </div>
                        </div>
                    </div>
                    <div class="lg:col-span-1">
                        <div class="rounded-lg border border-yt-border bg-yt-elevated/40 p-3 sm:p-4">
                            <h3 class="text-sm font-medium text-yt-text mb-3" data-i18n="common.options.title">Options</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="expires_in_advanced" class="block text-sm font-medium text-yt-text mb-2" data-i18n="common.options.expiration">Expiration</label>
                                    <select 
                                        id="expires_in_advanced" 
                                        name="expires_in_advanced" 
                                        class="input-field w-full px-3 py-2 text-sm"
                                    >
                                        <option value="never" data-i18n="common.options.never">Never</option>
                                        <option value="1hour" data-i18n="common.options.1hour">1 Hour</option>
                                        <option value="1day" selected data-i18n="common.options.1day">1 Day</option>
                                        <option value="1week" data-i18n="common.options.1week">1 Week</option>
                                        <option value="1month" data-i18n="common.options.1month">1 Month</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-yt-text mb-2" data-i18n="common.password.label_optional">Password (optional)</label>
                                    <input id="password_advanced" type="password" class="input-field w-full px-3 py-2 text-sm" data-i18n-attr="placeholder" data-i18n-placeholder="common.password.placeholder_optional" placeholder="Protect with a password (optional)" />
                                    <input id="password_hint" class="input-field w-full px-3 py-2 text-sm mt-2" data-i18n-attr="placeholder" data-i18n-placeholder="common.password.hint_optional" placeholder="Password hint (optional)" />
                                    <p class="text-xs text-yt-text-secondary mt-1" data-i18n="paste.create.password_note">If set, password is verified server-side (Argon2id). Content remains end‑to‑end encrypted.</p>
                                </div>
                                <div>
                                    <label for="view_once" class="block text-sm font-medium text-yt-text mb-2" data-i18n="common.view_policy.label">View policy</label>
                                    <select id="view_once" name="view_once" class="input-field w-full px-3 py-2 text-sm">
                                        <option value="no" selected data-i18n="common.view_policy.multiple">Allow multiple views</option>
                                        <option value="yes" data-i18n="common.view_policy.once">View once (delete after first view)</option>
                                    </select>
                                </div>
                                @auth
                                <div>
                                    <div class="flex items-center gap-3">
                                        <button type="button" id="is_private_toggle" role="switch" aria-checked="false" class="private-toggle">
                                            <span id="is_private_knob" class="private-knob"></span>
                                        </button>
                                        <input type="checkbox" id="is_private" name="is_private" class="hidden" />
                                        <span class="text-sm text-yt-text private-label" data-i18n="common.private_label">Private (visible only to you when logged in)</span>
                                    </div>
                                </div>
                                @endauth
                                @guest
                                <div class="text-xs text-yt-text-secondary" data-i18n="paste.create.login_private">Login to create private pastes.</div>
                                @endguest
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Toggle Advanced Mode -->
            <div class="flex justify-center">
                <button 
                    type="button" 
                    id="toggleMode"
                    class="text-sm text-yt-text-secondary hover:text-yt-text transition-colors flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span id="modeToggleText" data-i18n="common.advanced_settings">Advanced Settings</span>
                </button>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center pt-4 gap-4">
                <button 
                    type="submit" 
                    id="submitBtn"
                    class="btn-primary px-8 py-4 text-lg font-medium w-full sm:w-auto rounded-xl shadow-lg hover:shadow-xl transition-all"
                >
                    <span class="flex items-center justify-center gap-3">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM9 6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9V6zm9 14H6V10h12v10zm-6-3c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"/>
                        </svg>
                        <span data-i18n="paste.create.cta_simple">Encrypt & Create Link</span>
                    </span>
                </button>
            </div>
            <div class="text-center text-sm text-yt-text-secondary">
                <div data-i18n="common.security_simple">🔒 Your text is encrypted before leaving your device</div>
            </div>
        </form>

        <div id="result" class="mt-6 hidden">
            <div class="rounded-xl border border-yt-success/60 bg-yt-elevated/60 p-5 sm:p-6">
                <div class="flex items-start gap-3">
                    <div class="shrink-0 mt-0.5">
                        <div class="w-7 h-7 rounded-full grid place-items-center bg-emerald-500/15 border border-emerald-400/30">
                            <svg viewBox="0 0 24 24" class="w-4 h-4 text-emerald-400"><path fill="currentColor" d="M9 16.2l-3.5-3.5l1.4-1.4L9 13.4l7.1-7.1l1.4 1.4z"/></svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base sm:text-lg font-medium text-yt-success" data-i18n="paste.create.result.title">Paste Created Successfully</h3>
                        <p class="text-xs text-yt-text-secondary mt-1" data-i18n="common.share_link_never_leaves">Your share link contains the decryption key after the # and never leaves your browser.</p>
                        <div class="mt-4 space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-yt-text mb-2" data-i18n="common.share_url">Share URL</label>
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <input 
                                        type="text" 
                                        id="shareUrl" 
                                        readonly 
                                        class="flex-1 px-3 py-2 bg-yt-bg border border-yt-border rounded text-yt-text font-mono text-xs sm:text-sm focus:outline-none"
                                    >
                                    <button 
                                        type="button" 
                                        data-action="copyToClipboard"
                                        class="btn-primary px-4 py-2 rounded text-xs sm:text-sm font-medium"
                                    data-i18n="common.buttons.copy">Copy</button>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 pt-1">
                                <button type="button" class="btn-secondary px-3 py-2 text-xs sm:text-sm" data-action="shareLink" data-i18n="common.buttons.share">Share</button>
                                <button type="button" class="btn-secondary px-3 py-2 text-xs sm:text-sm" data-action="showQr" data-i18n="common.buttons.show_qr">Show QR</button>
                            </div>
                            <div id="identifierWrapper" class="pt-2 hidden space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-yt-text-secondary" data-i18n="common.identifier">Identifier:</span>
                                    <a id="createdIdentifier" href="#" target="_blank" class="text-link font-mono text-xs sm:text-sm"></a>
                                    <input type="hidden" id="identifierValue" />
                                    <button type="button" class="btn-secondary px-2 py-1 text-xs" data-action="copyIdentifier" data-i18n="common.buttons.copy_id">Copy ID</button>
                                </div>
                                <p class="text-xs text-yt-text-secondary" data-i18n="paste.create.result.identifier_note">Share this short ID with support/admins for moderation. Never share the key if you want to keep the paste private.</p>
                            </div>
                        </div>
                        @auth
                        <div class="md:col-span-2 mt-4">
                            <label for="recipient_username" class="block text-sm font-medium text-yt-text mb-2" data-i18n="paste.create.recipient_label">Recipient (username, optional)</label>
                            <input id="recipient_username" name="recipient_username" class="input-field w-full px-3 py-2 text-sm" data-i18n-attr="placeholder" data-i18n-placeholder="paste.create.recipient_placeholder" placeholder="Leave empty to use link-only mode" />
                            <p class="text-xs text-yt-text-secondary mt-1" data-i18n="paste.create.recipient_note">If set, the paste will be encrypted specifically for this user. The server never sees your content.</p>
                        </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Modal -->
        <div id="qrModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60 p-2 sm:p-4">
            <div class="relative content-card p-0 max-w-md w-full mx-auto overflow-hidden">
                <div class="px-5 py-4 border-b border-yt-border flex items-center justify-between bg-yt-elevated/60">
                    <div>
                        <h4 class="text-base font-medium" data-i18n="common.qr.scan_to_open">Scan to open</h4>
                        <p class="text-xs text-yt-text-secondary" data-i18n="common.qr.use_camera">Use your phone camera to open the link</p>
                    </div>
                    <button class="btn-secondary px-2 py-1 text-xs" data-action="hideQr" data-i18n="common.buttons.close">Close</button>
                </div>
                <div class="p-6">
                    <div class="mx-auto rounded-xl p-4 bg-yt-bg border border-yt-border w-fit">
                        <canvas id="qrCanvas" class="block"></canvas>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <input id="qrUrl" class="flex-1 px-3 py-2 bg-yt-bg border border-yt-border rounded text-yt-text font-mono text-xs" readonly />
                        <button type="button" class="btn-primary px-3 py-2 text-xs" data-action="copyQrUrl" data-i18n="common.buttons.copy">Copy</button>
                    </div>
                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                        <button type="button" class="btn-secondary px-3 py-2" data-action="downloadQrPng" data-i18n="common.buttons.download_png">Download PNG</button>
                        <button type="button" class="btn-secondary px-3 py-2" data-action="openInNewTab" data-i18n="common.buttons.open_link">Open Link</button>
                    </div>
                </div>
            </div>
                
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- No inline scripts. Page logic bundled in resources/js/pages/paste-create.js --}}
@endsection
