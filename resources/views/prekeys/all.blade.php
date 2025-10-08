@extends('layouts.app')

@section('title', 'All Prekeys')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8">
    <div class="mb-6 flex items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold" data-i18n="prekeys.all.title">All prekeys</h1>
            <p class="text-sm text-yt-text-secondary mt-1" data-i18n="prekeys.all.subtitle">Filter, search, and perform bulk actions on your one-time prekeys.</p>
        </div>
        <a href="{{ route('prekeys.index') }}" class="btn-secondary px-3 py-2 text-sm" data-i18n="prekeys.all.back">Back to prekeys</a>
    </div>

    <form method="GET" action="{{ route('prekeys.all') }}" class="bg-yt-surface border border-yt-border rounded-xl p-4 mb-6">
        <div class="grid sm:grid-cols-4 gap-3">
            <label class="text-sm">
                <span class="block text-xs uppercase tracking-wider text-yt-text-disabled mb-1" data-i18n="prekeys.all.filters.status.label">Status</span>
                <select name="status" class="input-field w-full px-2 py-2 text-sm">
                    <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }} data-i18n="prekeys.all.filters.status.options.all">All</option>
                    <option value="available" {{ ($filters['status'] ?? '') === 'available' ? 'selected' : '' }} data-i18n="prekeys.index.available">Available</option>
                    <option value="used" {{ ($filters['status'] ?? '') === 'used' ? 'selected' : '' }} data-i18n="prekeys.index.used">Used</option>
                </select>
            </label>
            <label class="text-sm">
                <span class="block text-xs uppercase tracking-wider text-yt-text-disabled mb-1" data-i18n="prekeys.index.algorithm">Algorithm</span>
                <select name="alg" class="input-field w-full px-2 py-2 text-sm">
                    <option value="all" {{ ($filters['alg'] ?? 'all') === 'all' ? 'selected' : '' }} data-i18n="prekeys.all.filters.alg.options.all">All</option>
                    <option value="ML-KEM-512" {{ ($filters['alg'] ?? '') === 'ML-KEM-512' ? 'selected' : '' }} data-i18n="prekeys.index.alg.512">ML-KEM-512</option>
                    <option value="ML-KEM-768" {{ ($filters['alg'] ?? '') === 'ML-KEM-768' ? 'selected' : '' }} data-i18n="prekeys.index.alg.768">ML-KEM-768</option>
                    <option value="ML-KEM-1024" {{ ($filters['alg'] ?? '') === 'ML-KEM-1024' ? 'selected' : '' }} data-i18n="prekeys.index.alg.1024">ML-KEM-1024</option>
                </select>
            </label>
            <label class="text-sm sm:col-span-2">
                <span class="block text-xs uppercase tracking-wider text-yt-text-disabled mb-1" data-i18n="prekeys.all.filters.search">Search (Key ID)</span>
                <input type="text" name="q" value="{{ old('q', $filters['q'] ?? '') }}" placeholder="k-..." class="input-field w-full px-2 py-2 text-sm" />
            </label>
        </div>
        <div class="mt-3">
            <button class="btn-primary px-3 py-2 text-sm" data-i18n="prekeys.all.filters.apply">Apply filters</button>
            <a href="{{ route('prekeys.all') }}" class="btn-secondary px-3 py-2 text-sm ml-2" data-i18n="prekeys.all.filters.reset">Reset</a>
        </div>
    </form>

    <form id="bulkForm" method="POST" action="{{ route('prekeys.bulk') }}" class="bg-yt-surface border border-yt-border rounded-xl p-4">
        @csrf
        <input type="hidden" name="action" id="bulkAction" value="">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
            <div class="flex items-center gap-2 text-sm">
                <input id="checkAll" type="checkbox" class="align-middle">
                <label for="checkAll" data-i18n="prekeys.all.bulk.select_page">Select page</label>
                <span id="selCount" class="text-xs text-yt-text-secondary ml-1">(0 selected)</span>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" data-action="mark_available" class="btn-secondary px-3 py-1.5 text-sm" data-i18n="prekeys.all.bulk.mark_available">Mark available</button>
                <button type="button" data-action="mark_used" class="btn-secondary px-3 py-1.5 text-sm" data-i18n="prekeys.all.bulk.mark_used">Mark used</button>
                <button type="button" data-action="delete" class="btn-danger px-3 py-1.5 text-sm" data-i18n="prekeys.all.bulk.delete_selected">Delete selected</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs text-yt-text-disabled">
                    <tr>
                        <th class="py-1 pr-3"><span class="sr-only" data-i18n="prekeys.all.table.select">Select</span></th>
                        <th class="text-left py-1 pr-3" data-i18n="prekeys.index.th.kid">Key ID</th>
                        <th class="text-left py-1 pr-3" data-i18n="prekeys.index.th.alg">Algorithm</th>
                        <th class="text-left py-1 pr-3" data-i18n="prekeys.index.th.status">Status</th>
                        <th class="text-left py-1 pr-3" data-i18n="prekeys.index.th.created">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prekeys as $pk)
                        <tr class="border-t border-yt-border/60">
                            <td class="py-2 pr-3 align-top">
                                <input type="checkbox" name="kids[]" value="{{ $pk->kid }}" class="rowCheck align-middle">
                            </td>
                            <td class="py-2 pr-3 font-mono text-xs">{{ $pk->kid }}</td>
                            <td class="py-2 pr-3">{{ $pk->alg }}</td>
                            <td class="py-2 pr-3">
                                @if($pk->used_at)
                                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-white/10">Used</span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-emerald-600/20 text-emerald-300">Available</span>
                                @endif
                            </td>
                            <td class="py-2 pr-3 text-xs">{{ $pk->created_at->format('M j, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-xs text-yt-text-secondary py-6" data-i18n="prekeys.all.empty">No keys match your filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $prekeys->links() }}</div>
    </form>
</div>
@endsection

@section('scripts')
<script>
(function() {
  const bulkForm = document.getElementById('bulkForm');
  const bulkAction = document.getElementById('bulkAction');
  const checkAll = document.getElementById('checkAll');
  const selCount = document.getElementById('selCount');
  const rowChecks = Array.from(document.querySelectorAll('.rowCheck'));
  const actionBtns = Array.from(document.querySelectorAll('[data-action]'));

  function updateCount() {
    const n = rowChecks.filter(cb => cb.checked).length;
    if (selCount) selCount.textContent = `(${n} selected)`;
  }

  checkAll?.addEventListener('change', () => {
    const v = !!checkAll.checked; rowChecks.forEach(cb => { cb.checked = v; }); updateCount();
  });
  rowChecks.forEach(cb => cb.addEventListener('change', updateCount));
  updateCount();

  actionBtns.forEach(btn => btn.addEventListener('click', () => {
    const act = btn.getAttribute('data-action');
    if (!act || !bulkForm || !bulkAction) return;
    const selected = rowChecks.filter(cb => cb.checked);
    if (selected.length === 0) { alert('Select at least one key'); return; }
    if (act === 'delete') {
      if (!confirm('Delete selected keys? This cannot be undone.')) return;
    }
    bulkAction.value = act;
    bulkForm.submit();
  }));
})();
</script>
@endsection
