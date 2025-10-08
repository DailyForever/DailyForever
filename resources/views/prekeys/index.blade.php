@extends('layouts.app')

@section('title', 'Prekeys')

@section('content')
<div id="prekeysRoot" class="mx-auto max-w-6xl px-4 py-8">
    @php
        $availablePrekeys = $prekeys->whereNull('used_at')->count();
        $totalPrekeys = $prekeys->count();
        $usedPrekeys = $totalPrekeys - $availablePrekeys;
    @endphp

    <div class="mb-8">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold" data-i18n="prekeys.index.title">One-time prekeys</h1>
                <p class="text-sm text-yt-text-secondary mt-1" data-i18n="prekeys.index.subtitle">Generate ML-KEM one-time public keys locally. Private keys never leave your browser. Export and import to move devices.</p>
            </div>
            <div class="shrink-0">
                <button id="importSecretsBtnHero" type="button" class="btn-secondary px-3 py-2 text-sm" data-i18n="prekeys.index.import_secrets">Import secrets</button>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-yt-surface border border-yt-border rounded-xl p-4">
                <div class="grid md:grid-cols-3 gap-3">
                    <label class="text-sm">
                        <span class="block text-xs uppercase tracking-wider text-yt-text-disabled mb-1" data-i18n="prekeys.index.algorithm">Algorithm</span>
                        <select id="prekeyAlg" class="input-field w-full px-2 py-2 text-sm">
                            <option value="ML-KEM-512" data-i18n="prekeys.index.alg.512">ML-KEM-512</option>
                            <option value="ML-KEM-768" selected data-i18n="prekeys.index.alg.768">ML-KEM-768 (recommended)</option>
                            <option value="ML-KEM-1024" data-i18n="prekeys.index.alg.1024">ML-KEM-1024</option>
                        </select>
                    </label>
                    <label class="text-sm">
                        <span class="block text-xs uppercase tracking-wider text-yt-text-disabled mb-1" data-i18n="prekeys.index.count_label">Number of keys</span>
                        <div class="flex items-center gap-2">
                            <input id="prekeyCount" type="number" min="1" max="100" value="10" class="input-field w-24 px-2 py-2 text-sm" />
                            <div class="flex gap-1">
                                <button type="button" class="px-2 py-1 text-xs bg-yt-bg border border-yt-border rounded" data-quick-count="1">1</button>
                                <button type="button" class="px-2 py-1 text-xs bg-yt-bg border border-yt-border rounded" data-quick-count="10">10</button>
                                <button type="button" class="px-2 py-1 text-xs bg-yt-bg border border-yt-border rounded" data-quick-count="50">50</button>
                            </div>
                        </div>
                    </label>
                    <div class="flex items-end">
                        <button id="genPrekeysBtn" type="button" class="btn-primary w-full md:w-auto px-4 py-2 text-sm flex items-center gap-2">
                            <span class="gen-label" data-i18n="prekeys.index.generate_keys">Generate keys</span>
                            <svg class="gen-spinner hidden animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                        </button>
                    </div>
                </div>
                <p class="text-xs text-yt-text-secondary mt-2" data-i18n="prekeys.index.gen_hint">Keys are generated in your browser. Only public keys are uploaded.</p>
            </div>

            <div class="bg-yt-surface border border-yt-border rounded-xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <label for="bundle" class="block text-xs uppercase tracking-wider text-yt-text-disabled" data-i18n="prekeys.index.bundle_label">Key bundle (JSON)</label>
                        <p class="text-xs text-yt-text-secondary" data-i18n="prekeys.index.bundle_desc">Paste or drop a bundle of public keys for upload.</p>
                    </div>
                    <div class="flex gap-2">
                        <button id="clearBundleBtn" type="button" class="text-xs bg-yt-bg border border-yt-border rounded px-2 py-1" data-i18n="prekeys.index.clear">Clear</button>
                        <button id="copyBundleBtn" type="button" class="btn-secondary px-2 py-1 text-xs" data-i18n="common.buttons.copy_json">Copy JSON</button>
                        <button id="downloadBundleBtn" type="button" class="btn-secondary px-2 py-1 text-xs" data-i18n="common.buttons.download">Download</button>
                    </div>
                </div>
                <div id="dropzone" class="rounded-md border border-dashed border-yt-border p-3 bg-black/20">
                    <textarea id="bundle" rows="10" class="input-field w-full font-mono text-sm px-3 py-2 bg-black/40 border-2 border-transparent hover:border-white/20 focus:border-white/30 transition-colors" placeholder='[{"kid":"k1","alg":"ML-KEM-768","public_key":"base64..."}]'></textarea>
                    <div class="flex items-center justify-between mt-2 text-xs">
                        <span id="bundleStats" class="text-yt-text-disabled">0 keys in bundle</span>
                        <form id="uploadForm" method="POST" action="{{ route('prekeys.store') }}" class="inline">
                            @csrf
                            <input type="hidden" name="bundle" id="bundleField" />
                            <button id="uploadBundleBtn" class="btn-primary px-3 py-1 text-xs" data-i18n="prekeys.index.upload_public">Upload public</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="bg-yt-surface border border-yt-border rounded-xl p-4">
                <div class="flex items-center gap-2 mb-3">
                    <button id="exportSecretsBtn" type="button" class="btn-secondary px-3 py-2 text-sm" data-i18n="prekeys.index.export_secrets">Export secrets</button>
                    <button id="importSecretsBtn" type="button" class="btn-secondary px-3 py-2 text-sm" data-i18n="prekeys.index.import_secrets">Import secrets</button>
                </div>
                <p class="text-xs text-yt-text-secondary" data-i18n="prekeys.index.export_hint">Export your secret keys to a file and import them on another device. Secrets are stored in your browser's local storage under keys like <code>pq.prekeys.&lt;kid&gt;.sk</code>.</p>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-yt-surface border border-yt-border rounded-xl p-4">
                <h2 class="text-xs uppercase tracking-wider text-yt-text-disabled mb-3" data-i18n="prekeys.index.status_title">Your key status</h2>
                <div class="grid grid-cols-3 gap-2">
                    <div class="rounded-lg border border-yt-border bg-black/30 p-3">
                        <div class="text-xl font-semibold">{{ $availablePrekeys }}</div>
                        <div class="text-xs text-yt-text-secondary" data-i18n="prekeys.index.available">Available</div>
                    </div>
                    <div class="rounded-lg border border-yt-border bg-black/30 p-3">
                        <div class="text-xl font-semibold">{{ $usedPrekeys }}</div>
                        <div class="text-xs text-yt-text-secondary" data-i18n="prekeys.index.used">Used</div>
                    </div>
                    <div class="rounded-lg border border-yt-border bg-black/30 p-3">
                        <div class="text-xl font-semibold">{{ $totalPrekeys }}</div>
                        <div class="text-xs text-yt-text-secondary" data-i18n="prekeys.index.total">Total</div>
                    </div>
                </div>
            </div>

            <div class="bg-yt-surface border border-yt-border rounded-xl p-4">
                <h2 class="text-xs uppercase tracking-wider text-yt-text-disabled mb-3" data-i18n="prekeys.index.recent_title">Recent keys</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs text-yt-text-disabled">
                            <tr>
                                <th class="text-left py-1 pr-3" data-i18n="prekeys.index.th.kid">Key ID</th>
                                <th class="text-left py-1 pr-3" data-i18n="prekeys.index.th.alg">Algorithm</th>
                                <th class="text-left py-1 pr-3" data-i18n="prekeys.index.th.status">Status</th>
                                <th class="text-left py-1 pr-3" data-i18n="prekeys.index.th.created">Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($prekeys->take(10) as $pk)
                                <tr class="border-t border-yt-border/60">
                                    <td class="py-2 pr-3 font-mono text-xs">{{ $pk->kid }}</td>
                                    <td class="py-2 pr-3">{{ $pk->alg }}</td>
                                    <td class="py-2 pr-3">
                                        @if($pk->used_at)
                                            <span class="px-2 py-0.5 text-[10px] rounded-full bg-white/10" data-i18n="prekeys.index.used">Used</span>
                                        @else
                                            <span class="px-2 py-0.5 text-[10px] rounded-full bg-emerald-600/20 text-emerald-300" data-i18n="prekeys.index.available">Available</span>
                                        @endif
                                    </td>
                                    <td class="py-2 pr-3 text-xs">{{ $pk->created_at->format('M j, H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-xs text-yt-text-secondary py-4" data-i18n="prekeys.index.empty">No keys yet. Generate some above.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($prekeys->count() > 10)
                    <div class="text-xs text-yt-text-secondary mt-2 text-center"><span data-i18n="prekeys.index.latest_10">Showing latest 10 keys.</span> <a href="{{ route('prekeys.all') }}" class="text-link" data-i18n="prekeys.index.view_all">View all keys</a></div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Import/Export modal --}}
@section('modals')
<div id="secretsModal" class="hidden fixed inset-0 z-50 items-center justify-center">
    <div class="absolute inset-0 bg-black/70"></div>
    <div class="relative bg-yt-surface border border-yt-border rounded-lg w-full max-w-2xl mx-auto p-6 m-4">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center text-sm font-bold">🔑</div>
                <div>
                    <div class="text-base font-medium" data-i18n="prekeys.modal.title_import">Import Private Keys</div>
                    <div class="text-xs text-yt-text-secondary" data-i18n="prekeys.modal.subtitle_import">Restore keys from a backup file</div>
                </div>
            </div>
            <button id="closeSecretsBtn" type="button" class="px-3 py-2 text-sm bg-yt-bg border border-yt-border rounded" data-i18n="common.buttons.close">Close</button>
        </div>
        <div class="border border-dashed border-white/20 rounded-lg p-4 mb-4 bg-black/10">
            <div class="text-xs text-yt-text-secondary mb-2" data-i18n="prekeys.modal.paste_json">Paste JSON here, or drop your exported file:</div>
            <textarea id="secretsJsonTa" rows="8" class="input-field w-full font-mono text-sm px-3 py-2 bg-black/30 border border-white/10 rounded" placeholder='{"type":"prekey-secrets","version":1,"secrets":{"k-xyz":"base64..."}}'></textarea>
        </div>
        <div class="flex justify-end gap-2">
            <button id="doImportSecretsBtn" type="button" class="btn-primary px-4 py-2 text-sm" data-i18n="prekeys.modal.import">Import</button>
        </div>
    </div>
</div>
@endsection
@endsection
