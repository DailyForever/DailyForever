@extends('layouts.app')

@section('title', 'How It Works - DailyForever')
@section('meta_description', 'Learn how DailyForever uses client-side encryption to protect your data. Detailed technical explanations of our zero-knowledge architecture and security measures.')
@section('keywords', 'encryption, zero-knowledge, client-side encryption, data protection, security, privacy, how it works')
@section('og_type', 'article')
@section('og_title', 'How DailyForever Encryption Works - Technical Overview')
@section('og_description', 'Comprehensive guide to DailyForever\'s zero-knowledge encryption system and data protection mechanisms.')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-slate-50/50 to-slate-100/30 dark:from-slate-900/50 dark:to-slate-800/30">
    <div class="max-w-6xl w-full px-4">
        <div class="content-card p-8">
        <h1 class="text-4xl font-bold text-yt-text mb-6" data-i18n="legal.how.title" data-i18n-doc-title="legal.how.doc_title">How DailyForever Works</h1>
        <p class="text-yt-text-secondary mb-4 text-lg">
            We wrote this page as the engineering team behind DailyForever. It is long on purpose: when we forget
            why we made a security decision, this is where we come back to fact-check ourselves. Read it straight
            through if you want the full story, or jump to the bits that matter to you. Expect occasional rough edges;
            we would rather be direct than pitch-perfect.
        </p>
        <p class="text-yt-text-secondary mb-8 text-sm">
            Quick note: DailyForever is still evolving. Whenever something meaningful changes (a new KEM parameter,
            a deprecation, a privacy footgun we discovered the hard way) we annotate it below. If you see a mismatch
            between this page and reality, please nudge us.
        </p>

        <!-- Quick Navigation -->
        <div class="bg-yt-bg border border-yt-border rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-yt-text mb-3" data-i18n="legal.how.toc.title">Skip Ahead</h2>
            <p class="text-sm text-yt-text-secondary mb-4">Pick a section below. We grouped them the way we think about the system on-call.</p>
            <div class="grid md:grid-cols-3 gap-4 text-sm">
                <div>
                    <h3 class="font-medium text-yt-text mb-2">Foundations</h3>
                    <ul class="space-y-1">
                        <li><a href="#threat-model" class="text-link hover:underline">Threat model & guardrails</a></li>
                        <li><a href="#overview" class="text-link hover:underline">System overview</a></li>
                        <li><a href="#encryption-process" class="text-link hover:underline">Encryption workflow</a></li>
                        <li><a href="#key-management" class="text-link hover:underline">Key management</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-medium text-yt-text mb-2">Daily operations</h3>
                    <ul class="space-y-1">
                        <li><a href="#lifecycle" class="text-link hover:underline">Paste & file lifecycle</a></li>
                        <li><a href="#data-flow" class="text-link hover:underline">Data flow diagrams</a></li>
                        <li><a href="#password-gate" class="text-link hover:underline">Password gate & rate limits</a></li>
                        <li><a href="#retention" class="text-link hover:underline">Storage & expiry</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-medium text-yt-text mb-2">Deep dives</h3>
                    <ul class="space-y-1">
                        <li><a href="#security-features" class="text-link hover:underline">Security features checklist</a></li>
                        <li><a href="#zk-proofs" class="text-link hover:underline">Zero-knowledge proofs</a></li>
                        <li><a href="#srp-authentication" class="text-link hover:underline">Authentication (SRP-6a)</a></li>
                        <li><a href="#technical-references" class="text-link hover:underline">Technical references</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="prose prose-invert max-w-none space-y-12">
            <!-- Threat Model & Assumptions -->
            <section id="threat-model">
                <h2 class="text-3xl font-semibold text-yt-text mb-6" data-i18n="legal.how.toc.threat_model">1. Threat Model & Assumptions</h2>
                <div class="grid md:grid-cols-2 gap-8 mb-6">
                    <div class="bg-yt-bg border border-yt-border rounded-lg p-6">
                        <h3 class="text-xl font-medium mb-3">What we protect against</h3>
                        <ul class="text-sm text-yt-text-secondary space-y-2">
                            <li>• Server compromise: stored blobs are unreadable without your key (which we never have).</li>
                            <li>• Network adversaries: TLS + client-side encryption prevent reading or tampering.</li>
                            <li>• Database leaks: only ciphertext, IV, and non-sensitive metadata are stored.</li>
                            <li>• Replay of links without key: URL fragment is never sent; attacker needs full link.</li>
                        </ul>
                    </div>
                    <div class="bg-yt-bg border border-yt-border rounded-lg p-6">
                        <h3 class="text-xl font-medium mb-3">Out of scope (examples)</h3>
                        <ul class="text-sm text-yt-text-secondary space-y-2">
                            <li>• Compromised end-user devices (malware, keyloggers, exfiltration).</li>
                            <li>• Users sharing the key publicly or poor password hygiene.</li>
                            <li>• Targeted phishing/social engineering outside the app’s control.</li>
                        </ul>
                    </div>
                </div>

                <div class="bg-yt-bg border border-yt-border rounded-lg p-6">
                    <h3 class="text-xl font-medium text-yt-text mb-3">Known Risks & Mitigations</h3>
                    <div class="grid md:grid-cols-2 gap-6 text-sm text-yt-text-secondary">
                        <div>
                            <h4 class="font-medium text-yt-text mb-2">URL Fragment (link‑secret) exposure</h4>
                            <ul class="space-y-1">
                                <li>• The decryption key lives in the URL fragment (<code>#</code>), which is not sent in HTTP requests or Referer by default.</li>
                                <li>• Risk comes from sharing full links, screenshots, browser sync/history, or link shorteners/redirectors.</li>
                                <li>• Mitigations: share carefully; prefer addressed shares (recipient KEM); re‑encrypt to rotate if a link is exposed.</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium text-yt-text mb-2">AES‑GCM IV uniqueness</h4>
                            <ul class="space-y-1">
                                <li>• Reusing a 96‑bit IV under the same key catastrophically breaks GCM.</li>
                                <li>• Client must generate a fresh 12‑byte IV via WebCrypto per encryption. The server validates IV shape/range but cannot detect reuse (zero‑knowledge).</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium text-yt-text mb-2">Client randomness & key strength</h4>
                            <ul class="space-y-1">
                                <li>• Keys must be generated with WebCrypto; never derived from low‑entropy sources.</li>
                                <li>• Password gates are access controls only and do not replace the encryption key.</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium text-yt-text mb-2">Transport security</h4>
                            <ul class="space-y-1">
                                <li>• All traffic must use HTTPS. Production sets HSTS and forces HTTPS at the edge.</li>
                                <li>• Mitigation: <code>ForceHttps</code> + security headers; deploy behind TLS‑terminating proxy/CDN.</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium text-yt-text mb-2">Addressed recipient (KEM) assumptions</h4>
                            <ul class="space-y-1">
                                <li>• The browser encapsulates the AES key to the recipient’s ML‑KEM public key. Server validates algorithm/lengths and marks prekeys used.</li>
                                <li>• Risk: misaddressing (wrong recipient key) yields undecryptable shares. Mitigation: UI confirmation and server validation; recipients store secrets locally.</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium text-yt-text mb-2">DoS/abuse vs. confidentiality</h4>
                            <ul class="space-y-1">
                                <li>• Abuse can affect availability, not confidentiality.</li>
                                <li>• Mitigation: throttling on creation and downloads, constant‑time style existence blur on APIs.</li>
                            </ul>
                        </div>
                        <div class="md:col-span-2">
                            <h4 class="font-medium text-yt-text mb-2">Endpoint compromise (out of scope)</h4>
                            <ul class="space-y-1">
                                <li>• Malware/browser extensions can exfiltrate keys or plaintext on the user’s device. No web app can fully prevent this.</li>
                                <li>• Mitigation: keep devices clean, avoid untrusted extensions, and rotate content by re‑encrypting if compromise is suspected.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Paste & File Lifecycle -->
            <section id="lifecycle">
                <h2 class="text-3xl font-semibold text-yt-text mb-6" data-i18n="legal.how.toc.lifecycle">2. Paste & File Lifecycle</h2>
                <div class="bg-yt-bg border border-yt-border rounded-lg p-4 mb-6">
                    <div class="mermaid">
                        flowchart TD
                          A["User input or file"] --> B["Generate 256-bit AES-GCM key (browser)"]
                          B --> C["Encrypt in browser (AES-GCM)"]
                          C --> D["Upload ciphertext + IV over TLS"]
                          D --> E["Store encrypted blob + metadata"]
                          B --> F["Key placed in URL fragment (hash keyHex)"]
                          E --> G["Recipient opens link"]
                          G --> H["Browser reads key from fragment (hash)"]
                          H --> I["Decrypt locally and render"]
                          I --> J["Optional: verify ZK proof and commitment"]
                    </div>
                </div>
                <p class="text-sm text-yt-text-secondary">The server never receives the decryption key. Optional password gates and rate limits apply at the access layer only.</p>
            </section>

            <!-- System Overview -->
            <section id="overview">
                <h2 class="text-3xl font-semibold text-yt-text mb-6" data-i18n="legal.how.toc.overview">3. System Overview</h2>
                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <h3 class="text-xl font-medium text-yt-text mb-4">Core Principle</h3>
                        <p class="text-yt-text leading-relaxed mb-4">
                            DailyForever implements a zero-knowledge architecture where all encryption and decryption 
                            operations occur entirely within your browser. Our servers never have access to your 
                            unencrypted content or decryption keys.
                        </p>
                        <div class="bg-green-900/20 border border-green-500/30 rounded-lg p-4">
                            <h4 class="font-medium text-green-400 mb-2">✓ What We Can See</h4>
                            <ul class="text-sm space-y-1 text-green-300">
                                <li>• Encrypted data (unreadable)</li>
                                <li>• Timestamps and view counts</li>
                                <li>• File sizes and types</li>
                                <li>• Access patterns (anonymized)</li>
                            </ul>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xl font-medium text-yt-text mb-4">What We Cannot See</h3>
                        <div class="bg-red-900/20 border border-red-500/30 rounded-lg p-4">
                            <h4 class="font-medium text-red-400 mb-2">✗ What We Cannot See</h4>
                            <ul class="text-sm space-y-1 text-red-300">
                                <li>• Your actual content</li>
                                <li>• Decryption keys</li>
                                <li>• Passwords</li>
                                <li>• Personal information</li>
                                <li>• File contents</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="bg-yt-bg border border-yt-border rounded-lg p-6 mt-4">
                    <h3 class="text-xl font-medium text-yt-text mb-3">Implementation & Libraries</h3>
                    <div class="grid md:grid-cols-2 gap-6 text-sm text-yt-text-secondary">
                        <div>
                            <ul class="space-y-1">
                                <li>• Laravel Framework: <code>^12.0</code></li>
                                <li>• artisansdk/srp: <code>dev-master</code> <span class="opacity-75">(SRP-6a implementation)</span></li>
                                <li>• paragonie/sodium_compat: <code>^2.2</code></li>
                                <li>• pragmarx/google2fa: <code>*</code></li>
                                <li>• simplesoftwareio/simple-qrcode: <code>^4.2</code></li>
                                <li>• otplib: <code>^12.0.1</code></li>
                            </ul>
                        </div>
                        <div>
                            <ul class="space-y-1">
                                <li>• mlkem: <code>^2.5.0</code></li>
                                <li>• snarkjs: <code>^0.7.5</code></li>
                                <li>• circom2: <code>^0.2.22</code></li>
                                <li>• secure-remote-password: <code>^0.3.1</code></li>
                                <li>• fast-srp-hap: <code>^1.0.0</code></li>
                                <li>• qrcode (frontend): <code>^1.5.4</code></li>
                            </ul>
                        </div>
                    </div>
                    <p class="text-xs text-yt-text-secondary mt-3">Notes: Versions reflect current constraints. For production, pin exact versions/commits (composer.lock/package-lock.json) and perform supply‑chain monitoring.</p>
                </div>
            </section>

            <!-- Interactive API Demonstrations -->
            <section id="api-demos" class="mb-12">
                <h2 class="text-3xl font-semibold text-yt-text mb-6">🔬 Live API Demonstrations</h2>
                <div class="bg-amber-900/20 border border-amber-500/30 rounded-lg p-6 mb-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <div class="flex-1">
                            <h4 class="font-semibold text-amber-300 mb-2">Try It Yourself!</h4>
                            <p class="text-sm text-amber-200/80 leading-relaxed">
                                Click any demo button below to make real API calls. Open your browser's Developer Tools 
                                (press F12) and navigate to the Network tab to see the actual requests and responses. 
                                This proves our zero-knowledge architecture - you can verify that sensitive data never reaches our servers!
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <!-- Demo 1: Encryption -->
                    <div class="bg-yt-bg border border-yt-border rounded-lg p-6">
                        <h3 class="text-xl font-medium text-yt-text mb-3">1. Client-Side Encryption</h3>
                        <p class="text-sm text-yt-text-secondary mb-4">
                            Watch as we generate a 256-bit AES key, encrypt data in your browser, 
                            and send only the encrypted blob to our server.
                        </p>
                        <button 
                            data-demo="encryption" 
                            data-original-text="Run Encryption Demo"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            Run Encryption Demo
                        </button>
                        <div id="result-encryption" class="mt-4"></div>
                    </div>

                    <!-- Demo 2: Keypair Generation -->
                    <div class="bg-yt-bg border border-yt-border rounded-lg p-6">
                        <h3 class="text-xl font-medium text-yt-text mb-3">2. Post-Quantum Keypair</h3>
                        <p class="text-sm text-yt-text-secondary mb-4">
                            Generate a quantum-resistant ML-KEM keypair. The private key stays 
                            in your browser, only the public key is sent.
                        </p>
                        <button 
                            data-demo="keypair" 
                            data-original-text="Generate Keypair"
                            class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            Generate Keypair
                        </button>
                        <div id="result-keypair" class="mt-4"></div>
                    </div>

                    <!-- Demo 3: ZK Proof -->
                    <div class="bg-yt-bg border border-yt-border rounded-lg p-6">
                        <h3 class="text-xl font-medium text-yt-text mb-3">3. Zero-Knowledge Proof</h3>
                        <p class="text-sm text-yt-text-secondary mb-4">
                            Submit a ZK proof that verifies encryption without revealing 
                            the plaintext or encryption key.
                        </p>
                        <button 
                            data-demo="zk-proof" 
                            data-original-text="Submit ZK Proof"
                            class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            Submit ZK Proof
                        </button>
                        <div id="result-zk-proof" class="mt-4"></div>
                    </div>

                    <!-- Demo 4: SRP Auth -->
                    <div class="bg-yt-bg border border-yt-border rounded-lg p-6">
                        <h3 class="text-xl font-medium text-yt-text mb-3">4. SRP Authentication</h3>
                        <p class="text-sm text-yt-text-secondary mb-4">
                            Check our SRP-6a authentication support - passwords are never 
                            sent to the server, even when hashed.
                        </p>
                        <button 
                            data-demo="srp-auth" 
                            data-original-text="Check SRP Support"
                            class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            Check SRP Support
                        </button>
                        <div id="result-srp-auth" class="mt-4"></div>
                    </div>
                </div>

                <div class="bg-gray-800 border border-gray-600 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-300 mb-3">📊 How to Verify in DevTools</h3>
                    <div class="grid md:grid-cols-2 gap-6 text-sm text-gray-400">
                        <div>
                            <h4 class="font-medium text-gray-200 mb-2">1. Open Developer Tools</h4>
                            <ul class="space-y-1">
                                <li>• Press <kbd class="px-2 py-1 bg-gray-700 rounded text-xs">F12</kbd> or</li>
                                <li>• Right-click → "Inspect" or</li>
                                <li>• <kbd class="px-2 py-1 bg-gray-700 rounded text-xs">Ctrl+Shift+I</kbd> (Win/Linux)</li>
                                <li>• <kbd class="px-2 py-1 bg-gray-700 rounded text-xs">Cmd+Option+I</kbd> (Mac)</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-200 mb-2">2. Navigate to Network Tab</h4>
                            <ul class="space-y-1">
                                <li>• Click the "Network" tab</li>
                                <li>• Click any demo button above</li>
                                <li>• Watch the API calls appear</li>
                                <li>• Click on a request to inspect headers & payload</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-200 mb-2">3. What to Look For</h4>
                            <ul class="space-y-1">
                                <li>✓ Request payload contains only encrypted data</li>
                                <li>✓ No plaintext or keys in requests</li>
                                <li>✓ Authentication tokens are temporary</li>
                                <li>✓ Response contains no sensitive data</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-200 mb-2">4. Security Headers</h4>
                            <ul class="space-y-1">
                                <li>• <code>X-Content-Type-Options: nosniff</code></li>
                                <li>• <code>X-Frame-Options: DENY</code></li>
                                <li>• <code>Strict-Transport-Security</code></li>
                                <li>• <code>Content-Security-Policy</code></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Threat Model & Assumptions -->
            <section id="threat-model">

            <!-- Paste & File Lifecycle -->
            <section id="lifecycle">
            <!-- SRP sections moved below to position #9 -->

            <!-- Encryption Process -->
            <section id="encryption-process">
                <h2 class="text-3xl font-semibold text-yt-text mb-6" data-i18n="legal.how.toc.encryption_process">4. Encryption Process</h2>
                
                <div class="bg-yt-bg border border-yt-border rounded-lg p-6 mb-6">
                    <h3 class="text-xl font-medium text-yt-text mb-4">Step-by-Step Process</h3>
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                            <div>
                                <h4 class="font-medium text-yt-text mb-2">Key Generation</h4>
                                <p class="text-yt-text-secondary text-sm">A cryptographically secure 256-bit key is generated in your browser using the Web Crypto API.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">2</div>
                            <div>
                                <h4 class="font-medium text-yt-text mb-2">Content Encryption</h4>
                                <p class="text-yt-text-secondary text-sm">Your content is encrypted using end-to-end encryption (AES-GCM) with the generated key before leaving your device.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">3</div>
                            <div>
                                <h4 class="font-medium text-yt-text mb-2">Key Handling</h4>
                                <p class="text-yt-text-secondary text-sm">A 256‑bit key is generated in your browser and embedded in the URL fragment (<code>#</code>). It never leaves your device or reaches our servers. Owners may optionally store their key for convenient access; this does not change how viewer links work.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">4</div>
                            <div>
                                <h4 class="font-medium text-yt-text mb-2">Secure Transmission</h4>
                                <p class="text-yt-text-secondary text-sm">Only encrypted data is transmitted over HTTPS to our servers for storage.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Encryption & Upload Diagram -->
            <section id="enc-diagram">
                <h2 class="text-3xl font-semibold text-yt-text mb-6" data-i18n="legal.how.toc.enc_diagram">4.1 Encryption & Upload Diagram</h2>
                <div class="bg-yt-bg border border-yt-border rounded-lg p-4">
                    <div class="mermaid">
                        flowchart TD
                          U["User content or file"] --> KGEN["Generate key (256-bit)"]
                          KGEN --> ENC["AES-GCM encrypt in browser"]
                          ENC --> TLS["Send ciphertext + IV over TLS"]
                          TLS --> S["Server storage"]
                          KGEN --> KEYFRAG["Key in URL fragment (hash)"]
                    </div>
                </div>
            </section>

            <!-- Encrypted Data Format -->
            <section id="encrypted-data-format">
                <h2 class="text-3xl font-semibold text-yt-text mb-6" data-i18n="legal.how.toc.encrypted_data_format">5. Encrypted Data Format</h2>
                
                <div class="bg-yt-bg border border-yt-border rounded-lg p-6 mb-6">
                    <h3 class="text-xl font-medium text-yt-text mb-4">What You See vs. What We Store</h3>
                    <p class="text-yt-text-secondary mb-4">
                        When you view your encrypted content as an owner, you see the raw encrypted data that our servers store. 
                        This data is completely unreadable without the decryption key.
                    </p>
                    
                    <div class="bg-red-900/20 border border-red-500/30 rounded-lg p-4 mb-4">
                        <h4 class="font-medium text-red-400 mb-2">Example of Encrypted Data</h4>
                        <div class="bg-black/50 rounded p-3 font-mono text-xs text-gray-300 overflow-x-auto">
                            <div class="text-green-400 mb-1">// Encrypted Content (unreadable)</div>
                            <div>[81,16,122,212,164,122,172,236,246,82,20,78,14,245,163,239,234,80,163,248,116,130,0,112,202,56,28]</div>
                            <div class="text-green-400 mb-1 mt-2">// Initialization Vector (IV)</div>
                            <div>[175,123,13,116,164,238,146,174,223,23,54,31]</div>
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <h3 class="text-xl font-medium text-yt-text mb-4">Why This Data is Unbreakable</h3>
                        <div class="space-y-4">
                            <div class="bg-blue-900/20 border border-blue-500/30 rounded-lg p-4">
                                <h4 class="font-medium text-blue-400 mb-2">End-to-End Encryption (AES-GCM, 256‑bit key, 128‑bit tag)</h4>
                                <p class="text-sm text-blue-300">
                                    Your content is encrypted client‑side using AES‑GCM with a 256‑bit key and a 128‑bit authentication tag. 
                                    Security depends on secrecy of the key and strict prevention of IV reuse under the same key.
                                </p>
                            </div>
                            
                            <div class="bg-purple-900/20 border border-purple-500/30 rounded-lg p-4">
                                <h4 class="font-medium text-purple-400 mb-2">Unique Initialization Vector</h4>
                                <p class="text-sm text-purple-300">
                                    Each encryption uses a unique 96-bit IV, ensuring that identical content produces completely different encrypted data. 
                                    This prevents pattern analysis attacks.
                                </p>
                            </div>
                            
                            <div class="bg-orange-900/20 border border-orange-500/30 rounded-lg p-4">
                                <h4 class="font-medium text-orange-400 mb-2">Authenticated Encryption</h4>
                                <p class="text-sm text-orange-300">
                                    Our end-to-end encryption (AES-GCM) provides both confidentiality and authenticity, making it impossible to tamper with encrypted data 
                                    without detection.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-medium text-yt-text mb-4">End-to-End Encryption Benefits</h3>
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-6 h-6 bg-green-600 text-white rounded-full flex items-center justify-center text-xs font-bold">✓</div>
                                <div>
                                    <h4 class="font-medium text-yt-text">Server-Side Security</h4>
                                    <p class="text-sm text-yt-text-secondary">Even if our servers are compromised, attackers cannot read your data without the decryption key.</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-6 h-6 bg-green-600 text-white rounded-full flex items-center justify-center text-xs font-bold">✓</div>
                                <div>
                                    <h4 class="font-medium text-yt-text">Network Protection</h4>
                                    <p class="text-sm text-yt-text-secondary">Data remains encrypted during transmission, protecting against man-in-the-middle attacks.</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-6 h-6 bg-green-600 text-white rounded-full flex items-center justify-center text-xs font-bold">✓</div>
                                <div>
                                    <h4 class="font-medium text-yt-text">Zero-Knowledge Architecture</h4>
                                    <p class="text-sm text-yt-text-secondary">We cannot access your content even if legally compelled, as we don't possess the decryption keys.</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-6 h-6 bg-green-600 text-white rounded-full flex items-center justify-center text-xs font-bold">✓</div>
                                <div>
                                    <h4 class="font-medium text-yt-text">Forward Secrecy</h4>
                                    <p class="text-sm text-yt-text-secondary">Each paste uses a unique encryption key, so compromising one key doesn't affect others.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-900/20 border border-yellow-500/30 rounded-lg p-6">
                    <h3 class="text-xl font-medium text-yellow-400 mb-4">Computational Security</h3>
                    <p class="text-yellow-300 mb-4">
                        Breaking AES-256 encryption would require more computational power than currently exists on Earth. 
                        Even with the world's most powerful supercomputers, it would take longer than the age of the universe 
                        to crack a single 256-bit key through brute force.
                    </p>
                    <div class="grid md:grid-cols-3 gap-4 text-sm">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-400">2^256</div>
                            <div class="text-yellow-300">Possible Keys</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-400">10^77</div>
                            <div class="text-yellow-300">Years to Crack</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-400">∞</div>
                            <div class="text-yellow-300">Practical Security</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Key Management -->
            <section id="key-management">
                <h2 class="text-3xl font-semibold text-yt-text mb-6" data-i18n="legal.how.toc.key_management">6. Key Management</h2>
                
                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <h3 class="text-xl font-medium text-yt-text mb-4">Key Distribution</h3>
                        <p class="text-yt-text leading-relaxed mb-4">
                            The decryption key is not derived from a password and is typically not stored on our servers. Instead, it's handled as follows:
                        </p>
                        <ul class="space-y-2 text-sm">
                            <li class="flex items-center space-x-2"><span class="w-2 h-2 bg-blue-500 rounded-full"></span><span>Primary: A random 256‑bit AES‑GCM key is generated client‑side and placed in the URL fragment (<code>#</code>).</span></li>
                            <li class="flex items-center space-x-2"><span class="w-2 h-2 bg-yellow-500 rounded-full"></span><span>Default: Owners have their encryption key stored for convenient access when logged in (they can opt out in settings).</span></li>
                            <li class="flex items-center space-x-2"><span class="w-2 h-2 bg-green-500 rounded-full"></span><span>Optional: A password can gate access server‑side (Argon2id). This is separate from encryption and does not affect the key.</span></li>
                            <li class="flex items-center space-x-2"><span class="w-2 h-2 bg-purple-500 rounded-full"></span><span>Optional: Addressed shares (when enabled) encapsulate the AES key using a KEM for a specific recipient.</span></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-medium text-yt-text mb-4">Key Reconstruction</h3>
                        <p class="text-yt-text leading-relaxed mb-4">
                            When someone opens your link, their browser reads the hex key from the URL fragment and imports it locally. If you're the owner and chose to store keys, we may auto‑populate your key when viewing while logged in. Password gates (if set) only control access to the encrypted blob and do not change the encryption key.
                        </p>
                        <div class="bg-yt-bg border border-yt-border rounded-lg p-4">
                            <code class="text-sm text-yt-text-secondary">
                                // From viewer link<br>
                                keyHex = window.location.hash.slice(1)<br>
                                key = importKey('raw', hexToBytes(keyHex), 'AES-GCM')
                            </code>
                        </div>
                        <div class="mt-4 bg-yt-bg border border-yt-border rounded-lg p-4">
                            <h4 class="text-sm font-medium text-yt-text mb-2">URL Fragment Risks & Mitigations</h4>
                            <ul class="text-xs text-yt-text-secondary space-y-1">
                                <li>• Browser history and screenshots can capture full links; share carefully.</li>
                                <li>• Referer headers do not include fragments by standard, but avoid link shorteners/redirectors.</li>
                                <li>• Backups/sync tools may retain links; rotate keys by re‑encrypting if needed.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Post-Quantum Prekeys -->
            <section id="prekeys">
                <h2 class="text-3xl font-semibold text-yt-text mb-6" data-i18n="legal.how.toc.prekeys">6.1 Post-Quantum Prekeys (/prekeys) - Experimental Feature</h2>

                <!-- Experimental Notice -->
                <div class="bg-amber-900/20 border border-amber-500/30 rounded-lg p-6 mb-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="flex-1">
                            <h4 class="font-semibold text-amber-300 mb-2">Experimental Feature</h4>
                            <p class="text-sm text-amber-200/80 leading-relaxed">
                                The Post-Quantum Prekey system is currently in experimental phase. While the underlying ML-KEM (Kyber) cryptography is robust and NIST-standardized, 
                                our implementation is still being refined and tested. Features may change, and we recommend using traditional sharing methods for critical data 
                                until this feature reaches stable status. Feedback and bug reports are welcome to help us improve this cutting-edge security feature.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-yt-bg border border-yt-border rounded-lg p-6 mb-6">
                    <h3 class="text-xl font-medium text-yt-text mb-4">Purpose</h3>
                    <p class="text-yt-text leading-relaxed mb-4">
                        The <code>/prekeys</code> dashboard lets authenticated users generate one-time post-quantum key pairs (ML-KEM / Kyber) entirely in the browser.
                        Only the public portion is uploaded; the secret key stays in local storage unless you export it yourself. These prekeys power addressed shares where
                        a paste or file is encrypted specifically for a named recipient even if they are offline.
                    </p>
                    <ul class="text-sm text-yt-text-secondary space-y-2">
                        <li class="flex items-start gap-2">
                            <span class="w-2 h-2 bg-purple-500 rounded-full mt-1"></span>
                            <span>Client script <code>resources/js/pages/prekeys.js</code> calls <code>PostQuantumKEM.generateKeypair()</code> per key, producing ML-KEM-512/768/1024 public secrets.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-1"></span>
                            <span>Generated bundles are JSON arrays persisted locally in the textarea. You can copy, download, or import/export secrets before clearing browser storage.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="w-2 h-2 bg-green-500 rounded-full mt-1"></span>
                            <span>Server validation (see <code>PrekeyController::store()</code>) enforces unique <code>kid</code>, algorithm label, strict base64 decoding, and 16&nbsp;–&nbsp;8192 byte key sizes before upserting records.</span>
                        </li>
                    </ul>
                </div>

                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <div class="bg-yt-bg border border-yt-border rounded-lg p-6">
                        <h3 class="text-xl font-medium text-yt-text mb-3">How prekeys are used</h3>
                        <ol class="text-sm text-yt-text-secondary space-y-3 list-decimal list-inside">
                            <li>
                                While creating an addressed paste, <code>resources/js/pages/paste-create.js</code> requests <code>GET /api/users/{username}/prekey</code>.
                                <code>PrekeyController::fetch()</code> returns the oldest unused public key (base64) for that user.
                            </li>
                            <li>
                                The browser imports the Kyber public key, encapsulates the symmetric AES key with <code>PostQuantumKEM.encapsulate()</code>,
                                and sends the ciphertext + wrapped AES key alongside the encrypted paste payload.
                            </li>
                            <li>
                                On success, the sender asynchronously hits <code>POST /api/prekeys/mark-used</code>, which sets <code>used_at</code> on that prekey so it will not be handed out again.
                            </li>
                            <li>
                                When the recipient opens the link, <code>resources/js/pages/paste-show.js</code> tries to load the matching secret from <code>localStorage</code> (namespace <code>pq.prekeys.{kid}.sk</code>),
                                decapsulates the shared secret with <code>PostQuantumKEM.decapsulate()</code>, and decrypts the AES-GCM ciphertext.
                            </li>
                        </ol>
                        <p class="text-xs text-yt-text-disabled mt-4">If no prekey is available, the app falls back to the recipient’s long-term ML-KEM key pair when configured.</p>
                    </div>
                    <div class="bg-yt-bg border border-yt-border rounded-lg p-6">
                        <h3 class="text-xl font-medium text-yt-text mb-3">Bundle shape (upload example)</h3>
                        <pre class="bg-black/50 text-xs text-yt-text-secondary p-4 rounded overflow-x-auto">
[{"kid":"k-mlkem-01","alg":"ML-KEM-768","public_key":"BASE64..."}]
</pre>
                        <p class="text-xs text-yt-text-secondary">Secrets never leave your device. Export them from the /prekeys page before wiping your browser or migrating to a new machine.</p>
                    </div>
                </div>

                <div class="bg-yt-bg border border-yt-border rounded-lg p-6">
                    <h3 class="text-xl font-medium text-yt-text mb-4">Why this matters</h3>
                    <ul class="text-sm text-yt-text-secondary space-y-3">
                        <li>
                            <span class="font-medium text-yt-text">Forward secrecy for addressed shares.</span>
                            One paste ⇒ one ML-KEM encapsulation ⇒ compromise of a single secret key does not reveal other messages.
                        </li>
                        <li>
                            <span class="font-medium text-yt-text">Post-quantum preparedness.</span>
                            Kyber (ML-KEM) protects the envelope against future quantum adversaries while the payload remains AES-256-GCM.
                        </li>
                        <li>
                            <span class="font-medium text-yt-text">Recipient convenience.</span>
                            Recipients can pre-provision keys while online, then decrypt addressed shares later without additional prompts as long as their secret bundle is stored locally.
                        </li>
                        <li>
                            <span class="font-medium text-yt-text">Experimental Status.</span>
                            This feature is under active development. While cryptographically sound, the implementation may evolve based on user feedback and security audits.
                        </li>
                    </ul>
                </div>
            </section>

            <!-- Data Flow Architecture -->
            <section id="data-flow">
                <h2 class="text-3xl font-semibold text-yt-text mb-6" data-i18n="legal.how.toc.data_flow">7. Data Flow Architecture</h2>
                
                <div class="bg-yt-bg border border-yt-border rounded-lg p-6 mb-6">
                    <h3 class="text-xl font-medium text-yt-text mb-4">Upload Process</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-blue-900/20 border border-blue-500/30 rounded-lg">
                            <span class="text-sm font-medium">1. User Input</span>
                            <span class="text-xs text-blue-300">Plaintext content</span>
                        </div>
                        <div class="flex justify-center">
                            <svg class="w-6 h-6 text-yt-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-green-900/20 border border-green-500/30 rounded-lg">
                            <span class="text-sm font-medium">2. Client-Side Encryption</span>
                            <span class="text-xs text-green-300">E2E Encryption (AES-GCM 256-bit)</span>
                        </div>
                        <div class="flex justify-center">
                            <svg class="w-6 h-6 text-yt-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-purple-900/20 border border-purple-500/30 rounded-lg">
                            <span class="text-sm font-medium">3. Secure Transmission</span>
                            <span class="text-xs text-purple-300">HTTPS/TLS 1.3</span>
                        </div>
                        <div class="flex justify-center">
                            <svg class="w-6 h-6 text-yt-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-900/20 border border-gray-500/30 rounded-lg">
                            <span class="text-sm font-medium">4. Server Storage</span>
                            <span class="text-xs text-gray-300">Encrypted data only</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Decryption & View Diagram -->
            <section id="dec-diagram">
                <h2 class="text-3xl font-semibold text-yt-text mb-6">7.1 Decryption & View Diagram</h2>
                <div class="bg-yt-bg border border-yt-border rounded-lg p-4">
                    <div class="mermaid">
                        sequenceDiagram
                          participant B as Browser
                          participant S as Server
                          B->>S: GET content by id
                          S-->>B: ciphertext + iv plus metadata
                          B->>B: Read key from URL fragment (hash)
                          B->>B: Import key, AES-GCM decrypt
                          B->>B: Render plaintext
                    </div>
                </div>
            </section>

            <!-- Password Gate & Rate Limiting -->
            <section id="password-gate">
                <h2 class="text-3xl font-semibold text-yt-text mb-6">7.2 Password Gate & Rate Limiting</h2>
                <div class="bg-yt-bg border border-yt-border rounded-lg p-4 mb-4">
                    <div class="mermaid">
                        flowchart TD
                          Q["Request access"] --> PW{"Password set?"}
                          PW -->|no| OK["Serve encrypted blob"]
                          PW -->|yes| V["Verify Argon2id"]
                          V -->|valid| OK
                          V -->|invalid| DENY["401 password_required"]
                          V --> RL["RateLimiter (per file/paste + IP)"]
                    </div>
                </div>
                <p class="text-sm text-yt-text-secondary">Passwords gate access to the encrypted blob; they do not replace end‑to‑end encryption or control the AES key. Default throttling: 30 attempts per 60 seconds per resource+IP (HTTP 429 on excess).</p>
                <div class="mt-3 bg-yt-bg border border-yt-border rounded-lg p-4">
                    <h4 class="text-sm font-medium text-yt-text mb-2">Argon2id Parameters (recommended baseline)</h4>
                    <ul class="text-xs text-yt-text-secondary space-y-1">
                        <li>• Memory: 64–128 MB</li>
                        <li>• Time cost (iterations): 3</li>
                        <li>• Parallelism: 1</li>
                        <li>• Note: Actual server parameters may differ and are configured server‑side.</li>
                    </ul>
                </div>
            </section>

            <!-- Storage, Expiry & Deletion -->
            <section id="retention">
                <h2 class="text-3xl font-semibold text-yt-text mb-6">7.3 Storage, Expiry & Deletion</h2>
                <div class="bg-yt-bg border border-yt-border rounded-lg p-4">
                    <div class="mermaid">
                        graph TD
                          INC[Increment counter after successful view or download] --> VL{Reached view_limit?}
                          VL -->|yes| DEL[Schedule deletion]
                          VL -->|no| EXP{Expired by time?}
                          EXP -->|yes| DEL
                          EXP -->|no| KEEP[Retain encrypted blob]
                    </div>
                </div>
            </section>

            <!-- Security Features -->
            <section id="security-features">
                <h2 class="text-3xl font-semibold text-yt-text mb-6">8. Security Features</h2>
                
                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <h3 class="text-xl font-medium text-yt-text mb-4">Encryption Standards</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-yt-bg border border-yt-border rounded-lg">
                                <span class="text-sm font-medium">Content Encryption</span>
                                <span class="text-xs text-yt-text-secondary">E2E Encryption (AES-GCM 256-bit)</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-yt-bg border border-yt-border rounded-lg">
                                <span class="text-sm font-medium">Password Hashing</span>
                                <span class="text-xs text-yt-text-secondary">Argon2id (server-side gate)</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-yt-bg border border-yt-border rounded-lg">
                                <span class="text-sm font-medium">Transport Security</span>
                                <span class="text-xs text-yt-text-secondary">TLS 1.3</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-yt-bg border border-yt-border rounded-lg">
                                <span class="text-sm font-medium">Random Generation</span>
                                <span class="text-xs text-yt-text-secondary">Web Crypto API</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-medium text-yt-text mb-4">Additional Protections</h3>
                        <div class="space-y-3">
                            <div class="p-3 bg-yt-bg border border-yt-border rounded-lg">
                                <h4 class="text-sm font-medium text-yt-text mb-1">Password Protection</h4>
                                <p class="text-xs text-yt-text-secondary">Optional password gates viewing/downloading on the server using Argon2id. Encryption remains strictly end‑to‑end in your browser.</p>
                            </div>
                            <div class="p-3 bg-yt-bg border border-yt-border rounded-lg">
                                <h4 class="text-sm font-medium text-yt-text mb-1">View Limits</h4>
                                <p class="text-xs text-yt-text-secondary">Automatic deletion after specified number of views</p>
                            </div>
                            <div class="p-3 bg-yt-bg border border-yt-border rounded-lg">
                                <h4 class="text-sm font-medium text-yt-text mb-1">Time Expiry</h4>
                                <p class="text-xs text-yt-text-secondary">Automatic deletion after specified time period</p>
                            </div>
                            <div class="p-3 bg-yt-bg border border-yt-border rounded-lg">
                                <h4 class="text-sm font-medium text-yt-text mb-1">One-Time View</h4>
                                <p class="text-xs text-yt-text-secondary">Content deleted immediately after first view</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Zero‑Knowledge Encryption Proofs -->
            <section id="zk-proofs">
                <h2 class="text-3xl font-semibold text-yt-text mb-6">9. Zero‑Knowledge Encryption Proofs</h2>
                <div class="bg-yt-bg border border-yt-border rounded-lg p-6 mb-6">
                    <h3 class="text-xl font-medium text-yt-text mb-3">What we prove</h3>
                    <p class="text-yt-text-secondary text-sm">
                        When artifacts are available, the app can generate a succinct proof (Groth16) that the ciphertext
                        on a paste/file page is consistent with the encryption pipeline and a binding commitment.
                        The proof does not reveal plaintext or keys.
                    </p>
                </div>
                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <h3 class="text-xl font-medium text-yt-text mb-3">Verification & Distribution</h3>
                        <ul class="space-y-2 text-sm text-yt-text-secondary">
                            <li>• Verified in‑browser via snarkjs and a verifying key shipped with the site.</li>
                            <li>• Recommend pinning verifying key by hash/signature and versioning circuits.</li>
                            <li>• Publish artifact hashes to allow external auditors to cross‑check.</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-xl font-medium text-yt-text mb-3">Fallback & Performance</h3>
                        <ul class="space-y-2 text-sm text-yt-text-secondary">
                            <li>• If artifacts are missing or verification fails, UI degrades gracefully with a status note.</li>
                            <li>• Encryption remains fully client‑side regardless of proof availability.</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Automatic Key Rotation -->
            <section id="key-rotation">
                <h2 class="text-3xl font-semibold text-yt-text mb-6">10. Automatic Key Rotation</h2>
                
                <div class="bg-yt-bg border border-yt-border rounded-lg p-6 mb-6">
                    <h3 class="text-xl font-medium text-yt-text mb-4">Cryptographic Key Lifecycle Management</h3>
                    <p class="text-yt-text-secondary mb-4">
                        Our platform implements automatic key rotation to maintain cryptographic hygiene and prevent key exhaustion attacks. 
                        Keys are rotated based on multiple security triggers to ensure long-term security of encrypted content.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <h3 class="text-xl font-medium text-yt-text mb-4">Rotation Triggers</h3>
                        <div class="space-y-4">
                            <div class="bg-red-900/20 border border-red-500/30 rounded-lg p-4">
                                <h4 class="font-medium text-red-400 mb-2">Critical (Immediate)</h4>
                                <ul class="text-sm text-red-300 space-y-1">
                                    <li>• Key age exceeds 90 days</li>
                                    <li>• More than 10,000 encryptions</li>
                                    <li>• IV collision threshold exceeded</li>
                                    <li>• Suspected key compromise</li>
                                </ul>
                            </div>
                            
                            <div class="bg-yellow-900/20 border border-yellow-500/30 rounded-lg p-4">
                                <h4 class="font-medium text-yellow-400 mb-2">Urgent (24 hours)</h4>
                                <ul class="text-sm text-yellow-300 space-y-1">
                                    <li>• Key approaching maximum age (72+ days)</li>
                                    <li>• Usage above 8,000 operations</li>
                                    <li>• High IV collision rate detected</li>
                                </ul>
                            </div>
                            
                            <div class="bg-blue-900/20 border border-blue-500/30 rounded-lg p-4">
                                <h4 class="font-medium text-blue-400 mb-2">Recommended</h4>
                                <ul class="text-sm text-blue-300 space-y-1">
                                    <li>• Key age over 54 days</li>
                                    <li>• Manual rotation request</li>
                                    <li>• Algorithm deprecation</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-medium text-yt-text mb-4">Rotation Process</h3>
                        <div class="space-y-4">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                                <div>
                                    <h4 class="font-medium text-yt-text mb-1">New Key Generation</h4>
                                    <p class="text-yt-text-secondary text-sm">Generate fresh ML-KEM-512/768/1024 keypair with validated entropy</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center text-sm font-bold">2</div>
                                <div>
                                    <h4 class="font-medium text-yt-text mb-1">Grace Period</h4>
                                    <p class="text-yt-text-secondary text-sm">Old keys remain valid for 7 days to allow re-encryption</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center text-sm font-bold">3</div>
                                <div>
                                    <h4 class="font-medium text-yt-text mb-1">Content Re-encryption</h4>
                                    <p class="text-yt-text-secondary text-sm">Existing content automatically re-encrypted with new keys</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center text-sm font-bold">4</div>
                                <div>
                                    <h4 class="font-medium text-yt-text mb-1">Audit Logging</h4>
                                    <p class="text-yt-text-secondary text-sm">All rotations logged with timestamp and reason</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-900/20 border border-green-500/30 rounded-lg p-4 mt-4">
                            <h4 class="font-medium text-green-400 mb-2">Security Benefits</h4>
                            <ul class="text-sm text-green-300 space-y-1">
                                <li>• Limits exposure window if key compromised</li>
                                <li>• Prevents IV reuse and collision attacks</li>
                                <li>• Maintains forward secrecy</li>
                                <li>• Automated compliance with best practices</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Secure Random Validation -->
            <section id="random-validation">
                <h2 class="text-3xl font-semibold text-yt-text mb-6">11. Secure Random Validation</h2>
                
                <div class="bg-yt-bg border border-yt-border rounded-lg p-6 mb-6">
                    <h3 class="text-xl font-medium text-yt-text mb-4">Entropy Quality Assurance</h3>
                    <p class="text-yt-text-secondary mb-4">
                        All cryptographic operations require high-quality randomness. Our platform validates the entropy of random number generation 
                        to prevent weak keys and predictable IVs that could compromise encryption security.
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-yt-bg border border-yt-border rounded-lg p-6">
                        <h3 class="text-lg font-medium text-yt-text mb-3">Validation Checks</h3>
                        <ul class="text-sm text-yt-text-secondary space-y-2">
                            <li>• <strong>Entropy estimation:</strong> Minimum 128 bits</li>
                            <li>• <strong>Byte distribution:</strong> Chi-square test</li>
                            <li>• <strong>Pattern detection:</strong> Sequential/repetitive</li>
                            <li>• <strong>Statistical tests:</strong> Monobit, runs test</li>
                            <li>• <strong>Uniqueness ratio:</strong> >90% unique bytes</li>
                        </ul>
                    </div>
                    
                    <div class="bg-yt-bg border border-yt-border rounded-lg p-6">
                        <h3 class="text-lg font-medium text-yt-text mb-3">Implementation</h3>
                        <div class="space-y-3">
                            <div>
                                <h4 class="text-sm font-medium text-yt-text mb-1">Client-Side</h4>
                                <p class="text-xs text-yt-text-secondary">WebCrypto API with automatic validation and retry</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-yt-text mb-1">Server-Side</h4>
                                <p class="text-xs text-yt-text-secondary">PHP random_bytes() with SecureRandomValidator</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-yt-text mb-1">Monitoring</h4>
                                <p class="text-xs text-yt-text-secondary">Real-time metrics and failure alerts</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-yt-bg border border-yt-border rounded-lg p-6">
                        <h3 class="text-lg font-medium text-yt-text mb-3">Security Impact</h3>
                        <div class="space-y-2">
                            <div class="flex items-start space-x-2">
                                <span class="text-green-500 mt-1">✓</span>
                                <span class="text-sm">Prevents weak key generation</span>
                            </div>
                            <div class="flex items-start space-x-2">
                                <span class="text-green-500 mt-1">✓</span>
                                <span class="text-sm">Detects compromised RNG</span>
                            </div>
                            <div class="flex items-start space-x-2">
                                <span class="text-green-500 mt-1">✓</span>
                                <span class="text-sm">Ensures IV uniqueness</span>
                            </div>
                            <div class="flex items-start space-x-2">
                                <span class="text-green-500 mt-1">✓</span>
                                <span class="text-sm">Maintains cryptographic strength</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-900/20 border border-yellow-500/30 rounded-lg p-6">
                    <h3 class="text-xl font-medium text-yellow-400 mb-4">Validation Process Flow</h3>
                    <div class="bg-black/50 rounded p-4 font-mono text-xs text-gray-300 overflow-x-auto">
                        <pre>1. Generate random bytes (32-256 bytes)
2. Check for catastrophic failure (all same value)
3. Calculate Shannon entropy (must be ≥128 bits)
4. Analyze byte distribution (Chi-square test)
5. Detect patterns (sequential, keyboard walks)
6. Run statistical randomness tests
7. If validation fails → retry (max 3 attempts)
8. Log failures for monitoring
9. Use validated random for cryptographic operations</pre>
                    </div>
                </div>
            </section>

            <!-- Technical Specifications -->
            <section id="technical-specifications">
                <h2 class="text-3xl font-semibold text-yt-text mb-6">12. Technical Specifications</h2>
                
                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <h3 class="text-xl font-medium text-yt-text mb-4">Cryptographic Implementation</h3>
                        <div class="bg-yt-bg border border-yt-border rounded-lg p-6 space-y-4">
                            <div>
                                <h4 class="font-medium text-yt-text mb-2">Encryption Algorithm</h4>
                                <p class="text-sm text-yt-text-secondary">End-to-End Encryption using AES-GCM (Advanced Encryption Standard - Galois/Counter Mode)</p>
                                <ul class="text-xs text-yt-text-secondary mt-1 space-y-1">
                                    <li>• Key size: 256 bits</li>
                                    <li>• Block size: 128 bits</li>
                                    <li>• Authentication: Built-in</li>
                                    <li>• Mode: Galois/Counter Mode</li>
                                    <li>• Tag length: 128 bits</li>
                                    <li>• IV/nonce: 96 bits via CSPRNG; never reused under the same key</li>
                                </ul>
                            </div>
                            
                            <div>
                                <h4 class="font-medium text-yt-text mb-2">Password Hashing (access gate)</h4>
                                <p class="text-sm text-yt-text-secondary">Argon2id via PHP <code>password_hash</code> (separate from content encryption)</p>
                                <ul class="text-xs text-yt-text-secondary mt-1 space-y-1">
                                    <li>• Memory‑hard and salt‑based</li>
                                    <li>• Verified with <code>password_verify</code></li>
                                    <li>• Combined with per‑resource rate limiting to slow brute force</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-medium text-yt-text mb-4">Browser Requirements</h3>
                        <div class="bg-yt-bg border border-yt-border rounded-lg p-6 space-y-4">
                            <div>
                                <h4 class="font-medium text-yt-text mb-2">Required APIs</h4>
                                <ul class="text-sm text-yt-text-secondary space-y-1">
                                    <li>• Web Crypto API</li>
                                    <li>• SubtleCrypto interface</li>
                                    <li>• ArrayBuffer support</li>
                                    <li>• Uint8Array support</li>
                                </ul>
                            </div>
                            
                            <div>
                                <h4 class="font-medium text-yt-text mb-2">Supported Browsers</h4>
                                <ul class="text-sm text-yt-text-secondary space-y-1">
                                    <li>• Chrome 37+</li>
                                    <li>• Firefox 34+</li>
                                    <li>• Safari 7+</li>
                                    <li>• Edge 12+</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Privacy Guarantees -->
            <section id="privacy-guarantees">
                <h2 class="text-3xl font-semibold text-yt-text mb-6">13. Privacy Guarantees</h2>
                
                <div class="grid md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-green-900/20 border border-green-500/30 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-green-400 mb-3">Zero-Knowledge Architecture</h3>
                        <p class="text-sm text-green-300">
                            Our servers never have access to your unencrypted content or decryption keys. 
                            All encryption/decryption happens in your browser.
                        </p>
                    </div>
                    
                    <div class="bg-blue-900/20 border border-blue-500/30 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-blue-400 mb-3">No Data Collection</h3>
                        <p class="text-sm text-blue-300">
                            We don't collect personal information, track users, or store analytics data 
                            that could identify you or your content.
                        </p>
                    </div>
                    
                    <div class="bg-purple-900/20 border border-purple-500/30 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-purple-400 mb-3">Transparent Operations</h3>
                        <p class="text-sm text-purple-300">
                            All cryptographic operations are performed using standard, audited libraries 
                            and open-source implementations.
                        </p>
                    </div>
                </div>
                
                <div class="bg-yt-bg border border-yt-border rounded-lg p-6">
                    <h3 class="text-xl font-medium text-yt-text mb-4">What This Means for You</h3>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start space-x-3">
                            <span class="text-green-500 mt-1">✓</span>
                            <span>Your content is encrypted before it leaves your device</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <span class="text-green-500 mt-1">✓</span>
                            <span>We cannot read your content even if we wanted to</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <span class="text-green-500 mt-1">✓</span>
                            <span>Your data is protected even if our servers are compromised</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <span class="text-green-500 mt-1">✓</span>
                            <span>No third parties can access your content without the key</span>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- Zero‑Knowledge Authentication (SRP‑6a) -->
            <section id="srp-authentication">
                <h2 class="text-3xl font-semibold text-yt-text mb-6">14. Zero‑Knowledge Authentication (SRP‑6a)</h2>

                <div class="bg-yt-bg border border-yt-border rounded-lg p-6 mb-8">
                    <h3 class="text-xl font-medium text-yt-text mb-3">What is SRP?</h3>
                    <p class="text-yt-text-secondary">
                        SRP‑6a (Secure Remote Password) is a password‑authenticated key exchange (PAKE) that lets you prove
                        you know your password without ever sending it to the server. This is a <em>zero‑knowledge password proof</em>:
                        the server verifies correctness but learns nothing about the password itself.
                    </p>
                </div>
                <div class="bg-yt-bg border border-yt-border rounded-lg p-6 mb-8">
                    <h3 class="text-xl font-medium text-yt-text mb-3">Why SRP vs. OPAQUE/SPAKE2</h3>
                    <p class="text-yt-text-secondary text-sm">
                        SRP‑6a was chosen for mature libraries and ease of deployment with zero password transmission. 
                        Modern PAKEs such as OPAQUE or SPAKE2 are under evaluation; migration would require verifier re‑enrollment and client updates. 
                        Regardless of PAKE, TLS and server‑side rate limiting remain enforced.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <h3 class="text-xl font-medium text-yt-text mb-3">Registration (one‑time)</h3>
                        <ol class="list-decimal list-inside space-y-2 text-sm text-yt-text-secondary">
                            <li>Client generates a random salt <code>s</code> (hex).</li>
                            <li>Compute <code>h = H(I || ":" || P)</code> where <code>I</code> is username and <code>P</code> is password.</li>
                            <li>Compute <code>x = H( UPPER(s || h) )</code> per the library’s convention.</li>
                            <li>Compute verifier <code>v = g^x mod N</code> and send <code>{ s, v }</code> to the server.</li>
                            <li>Server stores only <code>s</code> and <code>v</code> (no passwords).</li>
                        </ol>
                    </div>
                    <div>
                        <h3 class="text-xl font-medium text-yt-text mb-3">Login (each session)</h3>
                        <ol class="list-decimal list-inside space-y-2 text-sm text-yt-text-secondary">
                            <li>Client picks random <code>a</code>, sends <code>A = g^a mod N</code>.</li>
                            <li>Server picks random <code>b</code>, returns <code>B = k·v + g^b mod N</code> and <code>s</code>.</li>
                            <li>Both compute <code>u = H(A || B)</code> (hex concatenation).</li>
                            <li>Client recomputes <code>x</code> and shared key:
                                <div class="bg-yt-bg border border-yt-border rounded p-3 mt-2 text-xs font-mono">
                                    <div><code>S_client = (B − k·g^x)^(a + u·x) mod N</code></div>
                                    <div class="mt-1"><code>M1 = H(A || B || S_client)</code></div>
                                </div>
                            </li>
                            <li>Server verifies <code>M1</code> by computing:
                                <div class="bg-yt-bg border border-yt-border rounded p-3 mt-2 text-xs font-mono">
                                    <div><code>S_server = (A · v^u)^b mod N</code></div>
                                    <div class="mt-1"><code>M = H(A || B || S_server)</code> and checks <code>M == M1</code></div>
                                </div>
                            </li>
                            <li>Both sides can derive session key <code>K = H(S)</code>. The server may compute <code>M2 = H(A || M || S)</code> for mutual proof.</li>
                        </ol>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <h3 class="text-xl font-medium text-yt-text mb-3">Why this is Zero‑Knowledge</h3>
                        <ul class="space-y-2 text-sm text-yt-text-secondary">
                            <li>• <strong>No passwords transmitted</strong>: Only proofs and public values (<code>A</code>, <code>B</code>, <code>M1</code>).</li>
                            <li>• <strong>Salted verifier</strong>: Server stores <code>v</code>, not a password hash; prevents reuse and resists offline attacks.</li>
                            <li>• <strong>Ephemeral secrets</strong>: New random <code>a</code> and <code>b</code> each login.</li>
                            <li>• <strong>Mutual key agreement</strong>: Both sides agree on <code>S</code> only if password is correct.</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-xl font-medium text-yt-text mb-3">Implementation Details</h3>
                        <ul class="space-y-2 text-sm text-yt-text-secondary">
                            <li>• Library: <code>artisansdk/srp</code> (SHA‑256, RFC 5054 safe prime group).</li>
                            <li>• Server: <code>app/Services/SRPService.php</code> implements challenge/verify and cookie‑backed handshake.</li>
                            <li>• Client: <code>resources/js/srp-auth.js</code> computes <code>A</code>, <code>M1</code> exactly as the server expects.</li>
                            <li>• Formats: values are lowercase hex; leading zeros are unpadded for hashing per library helpers.</li>
                            <li>• Handshake state: short‑lived HttpOnly cookie <code>srp_chal</code> + cache entry (5 minutes).</li>
                        </ul>
                    </div>
                </div>

                <div class="bg-blue-900/20 border border-blue-500/30 rounded-lg p-4">
                    <h4 class="font-medium text-blue-400 mb-2">Security Properties</h4>
                    <ul class="text-sm text-blue-300 space-y-1">
                        <li>• Resistant to passive eavesdropping and active MITM (with TLS).</li>
                        <li>• No server‑side password database; only salted verifier <code>v</code>.</li>
                        <li>• Zero‑knowledge password proof: server learns nothing about <code>P</code>.</li>
                    </ul>
                </div>

                <div class="bg-yt-bg border border-yt-border rounded-lg p-6 mt-8">
                    <h3 class="text-xl font-medium text-yt-text mb-3">How to verify you're using SRP</h3>
                    <ol class="list-decimal list-inside space-y-2 text-sm text-yt-text-secondary">
                        <li>
                            Open DevTools → Network. Perform login using the SRP option.
                            You should see <code>POST /api/srp/initiate</code> followed by <code>POST /api/srp/verify</code>.
                        </li>
                        <li>
                            Select <code>/api/srp/initiate</code>:
                            <ul class="list-disc list-inside ml-4 space-y-1">
                                <li>Response JSON contains <code>salt</code> and <code>B</code> (long hex strings), and <code>expires_at</code>.</li>
                                <li>Response Headers include <code>Set-Cookie: srp_chal=…; HttpOnly; SameSite=Lax</code> (Secure in production).</li>
                                <li>No password is present anywhere in the request or response.</li>
                            </ul>
                        </li>
                        <li>
                            Select <code>/api/srp/verify</code>:
                            <ul class="list-disc list-inside ml-4 space-y-1">
                                <li>Request payload includes only <code>A</code> and <code>M1</code> (hex). No password is sent.</li>
                                <li>Request Headers include the <code>srp_chal</code> cookie automatically.</li>
                                <li>Successful response returns <code>{ success: true, … }</code> and logs you in.</li>
                            </ul>
                        </li>
                        <li>
                            Optional: visit <code>/api/srp/support</code> in your browser. You should see JSON with:
                            <div class="bg-yt-bg border border-yt-border rounded p-3 mt-2 text-xs font-mono">
                                <div>{ "supported": true, "config": { "algorithm": "SRP-6a", "hash_function": "SHA-256", "prime_group": "RFC5054 2048-bit group" (for example), … } }</div>
                            </div>
                        </li>
                        <li>
                            Advanced: values you see are public/derived artifacts:
                            <span class="font-mono">A</span> (client public), <span class="font-mono">B</span> (server public),
                            <span class="font-mono">M1</span> (client proof). The password never leaves your device.
                        </li>
                    </ol>
                </div>

                <div class="bg-yt-bg border border-yt-border rounded-lg p-6 mt-8">
                    <h3 class="text-xl font-medium text-yt-text mb-3">Parameter Quality & Security Profile</h3>
                    <div class="grid md:grid-cols-2 gap-6 text-sm text-yt-text-secondary">
                        <div>
                            <ul class="space-y-2">
                                <li><strong class="text-yt-text">Modulus N (safe prime)</strong>: Current <code>RFC 5054 1024‑bit</code>. <span class="text-yt-text-secondary">2048‑bit is available via configuration and planned as the default after re‑enrollment.</span></li>
                                <li><strong class="text-yt-text">Generator g</strong>: <code>2</code>.</li>
                                <li><strong class="text-yt-text">Hash H</strong>: <code>SHA‑256</code> for <code>u</code>, <code>k</code>, and proof computation.</li>
                                <li><strong class="text-yt-text">Multiplier k</strong>: <code>k = H(N || PAD(g))</code> with SHA‑256.</li>
                            </ul>
                        </div>
                        <div>
                            <ul class="space-y-2">
                                <li><strong class="text-yt-text">Client KDF for x</strong>: Current <code>x = H(s || H(I":"P))</code> with SHA‑256 per library. Recommendation: <code>Argon2id</code> (memory‑hard) pre‑hashing before SRP to slow offline guesses (e.g., 64–128MB, t=3, p=1). Changing this requires re‑enrollment of SRP verifiers.</li>
                                <li><strong class="text-yt-text">Transport</strong>: Production requires <code>HTTPS/TLS</code>. Enable HSTS to reduce MITM risk; do not use SRP over plaintext HTTP outside local dev.</li>
                                <li><strong class="text-yt-text">Implementation</strong>: Uses <code>artisansdk/srp</code> (server) and a matching client implementation; avoid custom cryptography.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-yt-text-secondary">
                        <strong class="text-yt-text">How to verify parameters:</strong> Open <code>/api/srp/support</code> to see <code>algorithm</code>, <code>hash_function</code>, <code>prime_group</code>, and more. For 1024‑bit groups, public values like <code>B</code> are ~256 hex chars; for 2048‑bit, ~512 hex chars.
                    </div>
                </div>
            </section>

            <!-- SRP Login Sequence Diagram -->
            <section id="srp-sequence">
                <h2 class="text-3xl font-semibold text-yt-text mb-6">15. SRP Login Sequence Diagram</h2>
                <div class="bg-yt-bg border border-yt-border rounded-lg p-4">
                    <div class="mermaid">
                        sequenceDiagram
                          participant C as Client (Browser)
                          participant S as Server (Laravel)
                          C->>S: POST /api/srp/initiate { username }
                          S-->>C: { salt, B } + Set-Cookie: srp_chal
                          C->>S: POST /api/srp/verify { A, M1 } with srp_chal
                          S-->>C: { success, requires_2fa? }
                          Note right of S: Server computes and checks M internally and derives session key K
                    </div>
                </div>
            </section>

            <!-- Best Practices -->
            <section id="best-practices">
                <h2 class="text-3xl font-semibold text-yt-text mb-6">16. Security Best Practices</h2>
                
                <div class="grid md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-xl font-medium text-yt-text mb-4">For Content Creators</h3>
                        <ul class="space-y-3 text-sm">
                            <li class="flex items-start space-x-3">
                                <span class="text-blue-500 mt-1">•</span>
                                <span>Use strong, unique passwords for sensitive content</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <span class="text-blue-500 mt-1">•</span>
                                <span>Set appropriate expiry times for your content</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <span class="text-blue-500 mt-1">•</span>
                                <span>Share passwords through separate channels</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <span class="text-blue-500 mt-1">•</span>
                                <span>Keep your browser updated</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <span class="text-blue-500 mt-1">•</span>
                                <span>Use view limits for highly sensitive data</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-medium text-yt-text mb-4">For Content Recipients</h3>
                        <ul class="space-y-3 text-sm">
                            <li class="flex items-start space-x-3">
                                <span class="text-green-500 mt-1">•</span>
                                <span>Verify the sender before opening content</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <span class="text-green-500 mt-1">•</span>
                                <span>Use secure communication channels</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <span class="text-green-500 mt-1">•</span>
                                <span>Don't share links in unsecured locations</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <span class="text-green-500 mt-1">•</span>
                                <span>Access content from trusted devices only</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <span class="text-green-500 mt-1">•</span>
                                <span>Be aware of content expiry times</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Technical References -->
            <section id="technical-references">
                <h2 class="text-3xl font-semibold text-yt-text mb-6">17. Technical References</h2>
                
                <div class="bg-yt-bg border border-yt-border rounded-lg p-6">
                    <h3 class="text-xl font-medium text-yt-text mb-4">Standards and Specifications</h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-yt-text mb-2">Encryption Standards</h4>
                            <ul class="text-sm text-yt-text-secondary space-y-1">
                                <li>• <a href="https://nvlpubs.nist.gov/nistpubs/FIPS/NIST.FIPS.197.pdf" class="text-link hover:underline" target="_blank">FIPS 197 - AES Standard</a></li>
                                <li>• <a href="https://nvlpubs.nist.gov/nistpubs/SpecialPublications/NIST.SP.800-38D.pdf" class="text-link hover:underline" target="_blank">NIST SP 800-38D - GCM Mode</a></li>
                                <li>• <a href="https://tools.ietf.org/html/rfc2898" class="text-link hover:underline" target="_blank">RFC 2898 - PBKDF2</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium text-yt-text mb-2">Web Standards</h4>
                            <ul class="text-sm text-yt-text-secondary space-y-1">
                                <li>• <a href="https://www.w3.org/TR/WebCryptoAPI/" class="text-link hover:underline" target="_blank">Web Crypto API</a></li>
                                <li>• <a href="https://tools.ietf.org/html/rfc8446" class="text-link hover:underline" target="_blank">TLS 1.3 Specification</a></li>
                                <li>• <a href="https://tools.ietf.org/html/rfc7518" class="text-link hover:underline" target="_blank">JSON Web Algorithms</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <p class="text-yt-text-secondary text-sm mt-6">
                    For complete technical specifications and legal details, please refer to our 
                    <a href="{{ route('legal.privacy') }}" class="text-link hover:underline">Privacy Policy</a> and 
                    <a href="{{ route('legal.philosophy') }}" class="text-link hover:underline">Philosophy</a> pages.
                </p>
            </section>
        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
    <script>
        try {
            const theme = (document.documentElement.getAttribute('data-theme') || 'dark') === 'light' ? 'default' : 'dark';
            mermaid.initialize({ startOnLoad: true, theme });
        } catch (_) {}
    </script>
@endsection


