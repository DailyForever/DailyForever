@extends('layouts.app')

@section('title', 'Support Report - Admin Dashboard')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="content-card p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-yt-text" data-i18n-doc-title="admin.support.show.doc_title"><span data-i18n="admin.support.show.title">Support Report</span> #{{ $report->id }}</h1>
                <p class="text-yt-text-secondary mt-1">{{ $report->type_label }} • {{ $report->status_label }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.support.index') }}" class="btn-secondary px-4 py-2 text-sm" data-i18n="admin.support.show.back">← Back to Reports</a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-yt-success/20 border border-yt-success rounded-lg p-4 mb-6">
                <p class="text-yt-success font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Report Details -->
                <div class="bg-yt-elevated border border-yt-border rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-yt-text mb-4" data-i18n="admin.support.show.details.title">Report Details</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-yt-text mb-1" data-i18n="admin.support.show.details.subject">Subject</label>
                            <p class="text-yt-text">{{ $report->subject }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-yt-text mb-1" data-i18n="admin.support.show.details.description">Description</label>
                            <div class="bg-yt-bg border border-yt-border rounded-lg p-4">
                                <p class="text-yt-text whitespace-pre-wrap">{{ $report->description }}</p>
                            </div>
                        </div>

                        @if($report->paste_identifier)
                            <div>
                                <label class="block text-sm font-medium text-yt-text mb-1" data-i18n="admin.support.show.details.paste_identifier">Paste Identifier</label>
                                <div class="flex items-center gap-2">
                                    <code class="bg-yt-bg border border-yt-border rounded px-2 py-1 text-sm font-mono">{{ $report->paste_identifier }}</code>
                                    <a href="{{ route('paste.show', $report->paste_identifier) }}" target="_blank" 
                                       class="btn-secondary px-2 py-1 text-xs" data-i18n="admin.support.show.details.view_paste">View Paste</a>
                                </div>
                            </div>
                        @endif

                        @if($report->violation_type)
                            <div>
                                <label class="block text-sm font-medium text-yt-text mb-1" data-i18n="admin.support.show.details.violation_type">Violation Type</label>
                                <p class="text-yt-text">{{ ucfirst(str_replace('_', ' ', $report->violation_type)) }}</p>
                            </div>
                        @endif

                        @if($report->copyright_work)
                            <div>
                                <label class="block text-sm font-medium text-yt-text mb-1" data-i18n="admin.support.show.details.copyrighted_work">Copyrighted Work</label>
                                <div class="bg-yt-bg border border-yt-border rounded-lg p-4">
                                    <p class="text-yt-text whitespace-pre-wrap">{{ $report->copyright_work }}</p>
                                </div>
                            </div>
                        @endif

                        @if($report->authorization)
                            <div>
                                <label class="block text-sm font-medium text-yt-text mb-1" data-i18n="admin.support.show.details.authorization">Authorization</label>
                                <div class="bg-yt-bg border border-yt-border rounded-lg p-4">
                                    <p class="text-yt-text whitespace-pre-wrap">{{ $report->authorization }}</p>
                                </div>
                            </div>
                        @endif

                        @if($report->contact_info)
                            <div>
                                <label class="block text-sm font-medium text-yt-text mb-1" data-i18n="admin.support.show.details.contact_info">Contact Information</label>
                                <div class="bg-yt-bg border border-yt-border rounded-lg p-4">
                                    <p class="text-yt-text whitespace-pre-wrap">{{ $report->contact_info }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Admin Notes -->
                <div class="bg-yt-elevated border border-yt-border rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-yt-text mb-4" data-i18n="admin.support.show.notes.title">Admin Notes</h2>
                    
                    @if($report->admin_notes)
                        <div class="bg-yt-bg border border-yt-border rounded-lg p-4 mb-4">
                            <p class="text-yt-text whitespace-pre-wrap">{{ $report->admin_notes }}</p>
                        </div>
                    @else
                        <p class="text-yt-text-disabled italic" data-i18n="admin.support.show.notes.empty">No admin notes yet.</p>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Management -->
                <div class="bg-yt-elevated border border-yt-border rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-yt-text mb-4" data-i18n="admin.support.show.manage.title">Manage Report</h2>
                    
                    <form method="POST" action="{{ route('admin.support.update', $report) }}" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-yt-text mb-1" data-i18n="admin.support.show.manage.status">Status</label>
                            <select name="status" id="status" class="input-field w-full px-3 py-2 text-sm">
                                <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }} data-i18n="admin.support.show.manage.options.pending">Pending</option>
                                <option value="in_progress" {{ $report->status === 'in_progress' ? 'selected' : '' }} data-i18n="admin.support.show.manage.options.in_progress">In Progress</option>
                                <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }} data-i18n="admin.support.show.manage.options.resolved">Resolved</option>
                                <option value="closed" {{ $report->status === 'closed' ? 'selected' : '' }} data-i18n="admin.support.show.manage.options.closed">Closed</option>
                            </select>
                        </div>

                        <div>
                            <label for="admin_notes" class="block text-sm font-medium text-yt-text mb-1" data-i18n="admin.support.show.manage.notes">Admin Notes</label>
                            <textarea name="admin_notes" id="admin_notes" rows="4" 
                                      class="input-field w-full px-3 py-2 text-sm resize-vertical"
                                      data-i18n-attr="placeholder" data-i18n-placeholder="admin.support.show.manage.notes_placeholder" placeholder="Add notes about this report...">{{ $report->admin_notes }}</textarea>
                        </div>

                        <button type="submit" class="btn-primary w-full px-4 py-2 text-sm" data-i18n="admin.support.show.manage.update">Update Report</button>
                    </form>
                </div>

                <!-- Report Info -->
                <div class="bg-yt-elevated border border-yt-border rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-yt-text mb-4" data-i18n="admin.support.show.info.title">Report Information</h2>
                    
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-yt-text-secondary" data-i18n="admin.support.show.info.id">Report ID:</span>
                            <span class="text-yt-text font-mono">#{{ $report->id }}</span>
                        </div>
                        
                        <div>
                            <span class="text-yt-text-secondary" data-i18n="admin.support.show.info.type">Type:</span>
                            <span class="text-yt-text">{{ $report->type_label }}</span>
                        </div>
                        
                        <div>
                            <span class="text-yt-text-secondary" data-i18n="admin.support.show.info.status">Status:</span>
                            <span class="text-yt-text">{{ $report->status_label }}</span>
                        </div>
                        
                        <div>
                            <span class="text-yt-text-secondary" data-i18n="admin.support.show.info.created">Created:</span>
                            <span class="text-yt-text">{{ $report->created_at->format('M j, Y g:i A') }}</span>
                        </div>
                        
                        @if($report->resolved_at)
                            <div>
                                <span class="text-yt-text-secondary" data-i18n="admin.support.show.info.resolved">Resolved:</span>
                                <span class="text-yt-text">{{ $report->resolved_at->format('M j, Y g:i A') }}</span>
                            </div>
                        @endif
                        
                        @if($report->resolver)
                            <div>
                                <span class="text-yt-text-secondary" data-i18n="admin.support.show.info.resolved_by">Resolved by:</span>
                                <span class="text-yt-text">{{ $report->resolver->username ?? $report->resolver->email }}</span>
                            </div>
                        @endif
                        
                        @if($report->email)
                            <div>
                                <span class="text-yt-text-secondary" data-i18n="admin.support.show.info.email">Email:</span>
                                <a href="mailto:{{ $report->email }}" class="text-link">{{ $report->email }}</a>
                            </div>
                        @endif
                        
                        <!-- IP Address removed per no-logs policy -->
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-yt-elevated border border-yt-border rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-yt-text mb-4" data-i18n="admin.support.show.quick.title">Quick Actions</h2>
                    
                    <div class="space-y-2">
                        @if($report->paste_identifier)
                            <a href="{{ route('paste.show', $report->paste_identifier) }}" target="_blank"
                               class="block w-full btn-secondary px-4 py-2 text-sm text-center" data-i18n="admin.support.show.quick.view_paste">View Related Paste</a>
                        @endif
                        
                        @if($report->email)
                            <a href="mailto:{{ $report->email }}?subject=Re: {{ $report->subject }}"
                               class="block w-full btn-secondary px-4 py-2 text-sm text-center" data-i18n="admin.support.show.quick.reply_email">Reply via Email</a>
                        @endif
                        
                        <a href="{{ route('admin.pastes.index') }}?search={{ $report->paste_identifier }}"
                           class="block w-full btn-secondary px-4 py-2 text-sm text-center" data-i18n="admin.support.show.quick.search_pastes">Search Pastes</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
