/**
 * Interactive API Demo for How It Works Page
 * Demonstrates real API calls visible in DevTools Network tab
 */

class HowItWorksDemo {
    constructor() {
        this.init();
    }

    init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupEventListeners());
        } else {
            this.setupEventListeners();
        }
    }

    setupEventListeners() {
        document.querySelectorAll('[data-demo]').forEach(button => {
            button.addEventListener('click', (e) => this.handleDemo(e));
        });
    }

    async handleDemo(event) {
        const button = event.target;
        const demoType = button.dataset.demo;
        const resultDiv = document.getElementById(`result-${demoType}`);
        
        button.disabled = true;
        button.textContent = 'Running...';
        
        try {
            switch(demoType) {
                case 'encryption':
                    await this.demoEncryption(resultDiv);
                    break;
                case 'keypair':
                    await this.demoKeypairGeneration(resultDiv);
                    break;
                case 'zk-proof':
                    await this.demoZKProof(resultDiv);
                    break;
                case 'srp-auth':
                    await this.demoSRPAuth(resultDiv);
                    break;
            }
        } catch (error) {
            this.displayError(resultDiv, error);
        } finally {
            button.disabled = false;
            button.textContent = button.dataset.originalText || 'Run Demo';
        }
    }

    async demoEncryption(resultDiv) {
        this.displayStatus(resultDiv, 'Generating encryption key...');
        
        const key = await crypto.subtle.generateKey(
            { name: 'AES-GCM', length: 256 },
            true,
            ['encrypt', 'decrypt']
        );
        
        const exportedKey = await crypto.subtle.exportKey('raw', key);
        const keyHex = Array.from(new Uint8Array(exportedKey))
            .map(b => b.toString(16).padStart(2, '0'))
            .join('');
        
        const sampleText = 'Secret message encrypted in browser!';
        const encoder = new TextEncoder();
        const data = encoder.encode(sampleText);
        const iv = crypto.getRandomValues(new Uint8Array(12));
        
        const encrypted = await crypto.subtle.encrypt(
            { name: 'AES-GCM', iv: iv },
            key,
            data
        );
        
        // Prepare encrypted content in the format the server expects
        const encryptedBase64 = btoa(String.fromCharCode(...new Uint8Array(encrypted)));
        const ivBase64 = btoa(String.fromCharCode(...iv));
        
        // Create the encrypted content JSON that the paste endpoint expects
        const encryptedContent = {
            ct: encryptedBase64,
            iv: ivBase64,
            s: btoa('demo-salt') // Add a salt for the demo
        };
        
        const response = await fetch('/paste', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                content: JSON.stringify(encryptedContent),
                title: 'API Demo - Zero-Knowledge Test',
                syntax_highlighting: 'plaintext',
                expiry_time: '24',
                expiry_unit: 'hours',
                view_once: false,
                password: '',
                is_encrypted: true
            })
        });
        
        this.displayResult(resultDiv, 'encryption', {
            keyHex: keyHex.substring(0, 32),
            status: response.status,
            encrypted: encrypted.byteLength
        });
    }

    async demoKeypairGeneration(resultDiv) {
        // First check if user is authenticated
        const authCheck = await fetch('/api/user', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            credentials: 'same-origin'
        });
        
        if (!authCheck.ok) {
            this.displayResult(resultDiv, 'keypair-auth', {
                status: 401,
                needsAuth: true
            });
            return;
        }
        
        const response = await fetch('/api/keypairs/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                algorithm: 'ml-kem-768',
                purpose: 'demo'
            }),
            credentials: 'same-origin'
        });
        
        this.displayResult(resultDiv, 'keypair', {
            status: response.status,
            algorithm: 'ML-KEM-768'
        });
    }

    async demoZKProof(resultDiv) {
        // Generate more realistic proof data
        const generateHex = (length) => {
            const bytes = crypto.getRandomValues(new Uint8Array(length));
            return '0x' + Array.from(bytes).map(b => b.toString(16).padStart(2, '0')).join('');
        };
        
        const mockProof = {
            proof: {
                pi_a: [generateHex(32), generateHex(32)],
                pi_b: [[generateHex(32), generateHex(32)], [generateHex(32), generateHex(32)]],
                pi_c: [generateHex(32), generateHex(32)],
                protocol: 'groth16',
                curve: 'bn128'
            },
            public_signals: [generateHex(32), generateHex(32)],
            commitment: generateHex(32),
            nullifier: generateHex(32),
            encrypted_data: btoa(String.fromCharCode(...crypto.getRandomValues(new Uint8Array(64)))),
            timestamp: Math.floor(Date.now() / 1000)
        };
        
        const response = await fetch('/api/zk/encryption/submit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify(mockProof)
        });
        
        this.displayResult(resultDiv, 'zk', {
            status: response.status,
            protocol: 'Groth16'
        });
    }

    async demoSRPAuth(resultDiv) {
        const response = await fetch('/api/srp/support', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const result = await response.json();
        
        this.displayResult(resultDiv, 'srp', {
            status: response.status,
            supported: result.supported
        });
    }

    displayStatus(element, message) {
        element.innerHTML = `
            <div class="flex items-center space-x-2">
                <svg class="animate-spin h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-blue-400">${message}</span>
            </div>
        `;
    }

    displayResult(element, type, data) {
        const templates = {
            'keypair-auth': `
                <div class="bg-yellow-900/20 border border-yellow-500/30 rounded-lg p-4">
                    <h4 class="font-medium text-yellow-400 mb-2">Authentication Required</h4>
                    <div class="text-sm space-y-1">
                        <div class="text-yellow-300">This demo requires authentication to generate keypairs.</div>
                        <div class="text-gray-400">Status: <span class="text-yellow-400">${data.status}</span></div>
                    </div>
                    <div class="text-blue-400 text-xs mt-2">⚠️ The 401 response in DevTools proves the API is protected!</div>
                </div>
            `,
            encryption: `
                <div class="bg-green-900/20 border border-green-500/30 rounded-lg p-4">
                    <h4 class="font-medium text-green-400 mb-2">✓ Encryption API Called</h4>
                    <div class="text-sm space-y-1">
                        <div><span class="text-gray-400">Key:</span> <code class="text-blue-400">${data.keyHex}...</code></div>
                        <div><span class="text-gray-400">Status:</span> <span class="${data.status === 200 || data.status === 201 ? 'text-green-400' : 'text-yellow-400'}">${data.status}</span></div>
                        <div><span class="text-gray-400">Size:</span> <span class="text-white">${data.encrypted} bytes</span></div>
                    </div>
                    <div class="text-yellow-400 text-xs mt-2">⚠️ Check DevTools Network tab → "paste" request</div>
                    ${data.status !== 200 && data.status !== 201 ? '<div class="text-gray-400 text-xs mt-1">Note: Demo validation errors are expected - check request payload to see encrypted data!</div>' : ''}
                </div>
            `,
            keypair: `
                <div class="bg-purple-900/20 border border-purple-500/30 rounded-lg p-4">
                    <h4 class="font-medium text-purple-400 mb-2">✓ Keypair Generation API</h4>
                    <div class="text-sm space-y-1">
                        <div><span class="text-gray-400">Algorithm:</span> <span class="text-white">${data.algorithm}</span></div>
                        <div><span class="text-gray-400">Status:</span> <span class="text-white">${data.status}</span></div>
                    </div>
                    <div class="text-yellow-400 text-xs mt-2">⚠️ DevTools → Network → "generate" request</div>
                </div>
            `,
            zk: `
                <div class="bg-cyan-900/20 border border-cyan-500/30 rounded-lg p-4">
                    <h4 class="font-medium text-cyan-400 mb-2">✓ Zero-Knowledge Proof</h4>
                    <div class="text-sm space-y-1">
                        <div><span class="text-gray-400">Protocol:</span> <span class="text-white">${data.protocol}</span></div>
                        <div><span class="text-gray-400">Status:</span> <span class="text-white">${data.status}</span></div>
                    </div>
                    <div class="text-yellow-400 text-xs mt-2">⚠️ DevTools → Network → "submit" request</div>
                </div>
            `,
            srp: `
                <div class="bg-orange-900/20 border border-orange-500/30 rounded-lg p-4">
                    <h4 class="font-medium text-orange-400 mb-2">✓ SRP Authentication</h4>
                    <div class="text-sm space-y-1">
                        <div><span class="text-gray-400">Supported:</span> <span class="text-white">${data.supported ? 'Yes' : 'No'}</span></div>
                        <div><span class="text-gray-400">Status:</span> <span class="text-white">${data.status}</span></div>
                    </div>
                    <div class="text-yellow-400 text-xs mt-2">⚠️ DevTools → Network → "support" request</div>
                </div>
            `
        };
        
        element.innerHTML = templates[type] || '<div>Demo completed</div>';
    }

    displayError(element, error) {
        element.innerHTML = `
            <div class="bg-red-900/20 border border-red-500/30 rounded-lg p-4">
                <h4 class="font-medium text-red-400 mb-2">Error</h4>
                <p class="text-sm text-red-300">${error.message}</p>
            </div>
        `;
    }
}

// Initialize demo
new HowItWorksDemo();
