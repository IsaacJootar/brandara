<div>

    {{-- ── Upload zone ─────────────────────────────────────────────────────── --}}
    <div x-data="{ dragging: false }"
         x-on:dragover.prevent="dragging = true"
         x-on:dragleave.prevent="dragging = false"
         x-on:drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; $wire.upload('uploads', $event.dataTransfer.files)"
         style="border:2px dashed; border-radius:14px; padding:1.75rem 1.5rem; text-align:center; transition:all 0.15s; margin-bottom:1.5rem;"
         :style="dragging ? 'border-color:#7C3AED; background:#F5F3FF;' : 'border-color:#E2E8F0; background:#FAFBFF;'">

        <div style="width:44px;height:44px;background:#F5F3FF;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 0.875rem;">
            <svg style="width:22px;height:22px;color:#7C3AED;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>

        <p style="font-size:0.9rem; font-weight:600; color:#0F172A; margin:0 0 0.25rem;">Drop files here or click to browse</p>
        <p style="font-size:0.78rem; color:#94A3B8; margin:0 0 1rem;">JPG, PNG, GIF, WEBP, MP4 — max 20 MB each</p>

        <label style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.5rem 1.25rem; background:#7C3AED; color:#fff; border-radius:8px; font-size:0.82rem; font-weight:600; cursor:pointer;">
            <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Choose files
            <input x-ref="fileInput" type="file" multiple accept="image/*,video/mp4"
                wire:model="uploads"
                style="display:none;">
        </label>

        {{-- Upload progress --}}
        <div wire:loading wire:target="uploads" style="margin-top:1rem;">
            <p style="font-size:0.8rem; color:#7C3AED;">Uploading…</p>
        </div>
    </div>

    {{-- Stage + upload button when files are queued --}}
    @if(count($uploads))
        <div style="background:#F5F3FF; border:1px solid #DDD6FE; border-radius:10px; padding:0.875rem 1rem; display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; gap:1rem; flex-wrap:wrap;">
            <span style="font-size:0.85rem; color:#6D28D9; font-weight:500;">{{ count($uploads) }} file(s) ready to upload</span>
            <button type="button" wire:click="upload"
                wire:loading.attr="disabled" wire:target="upload"
                style="padding:0.4rem 1rem; background:#7C3AED; color:#fff; border:none; border-radius:8px; font-size:0.82rem; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:0.375rem;">
                <span wire:loading.remove wire:target="upload">Save to library</span>
                <span wire:loading wire:target="upload">Saving…</span>
            </button>
        </div>
    @endif

    {{-- Error --}}
    @if($uploadStatus === 'error')
        <div style="background:#FEF2F2; border:1px solid #FECACA; border-radius:10px; padding:0.75rem 1rem; margin-bottom:1.25rem; font-size:0.85rem; color:#991B1B;">
            {{ $uploadError }}
        </div>
    @endif

    {{-- Success flash --}}
    @if($uploadStatus === 'done')
        <div style="background:#F0FDF4; border:1px solid #BBF7D0; border-radius:10px; padding:0.75rem 1rem; margin-bottom:1.25rem; font-size:0.85rem; color:#16A34A;">
            Files uploaded successfully.
        </div>
    @endif

    {{-- ── Search + storage bar ──────────────────────────────────────────── --}}
    <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:1.25rem; flex-wrap:wrap;">
        <div style="position:relative; flex:1; min-width:200px; max-width:320px;">
            <svg style="position:absolute; left:0.625rem; top:50%; transform:translateY(-50%); width:14px;height:14px;color:#94A3B8; pointer-events:none;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by filename…"
                style="width:100%; padding:0.45rem 0.75rem 0.45rem 2rem; border:1px solid #E2E8F0; border-radius:8px; font-size:0.82rem; color:#0F172A; background:#fff; outline:none;">
        </div>
        <div style="font-size:0.75rem; color:#94A3B8;">
            {{ number_format($storageUsedKb / 1024, 1) }} MB used
        </div>
    </div>

    {{-- ── Media grid ───────────────────────────────────────────────────── --}}
    @if($files->isEmpty())
        <div style="text-align:center; padding:3rem 1.5rem; color:#94A3B8;">
            <svg style="width:36px;height:36px;margin:0 auto 0.75rem;display:block;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p style="font-size:0.875rem; margin:0;">No files yet. Upload your first image above.</p>
        </div>
    @else
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(130px, 1fr)); gap:0.75rem; margin-bottom:1.5rem;">
            @foreach($files as $file)
                @php $url = $service->url($file); @endphp
                <div style="position:relative; border-radius:10px; overflow:hidden; border:1px solid #E2E8F0; background:#F8FAFC; aspect-ratio:1;">

                    {{-- Thumbnail --}}
                    @if(str_starts_with($file->mime_type, 'image/'))
                        <img src="{{ $url }}" alt="{{ $file->alt_text ?? $file->filename }}"
                            style="width:100%; height:100%; object-fit:cover; display:block;">
                    @else
                        <div style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:0.375rem;">
                            <svg style="width:28px;height:28px;color:#7C3AED;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.868V15.13a1 1 0 01-1.447.9L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                            </svg>
                            <span style="font-size:0.65rem;color:#64748B;">MP4</span>
                        </div>
                    @endif

                    {{-- Picker checkbox overlay --}}
                    @if($pickerMode)
                        <button type="button" wire:click="toggleSelect('{{ $file->id }}')"
                            style="position:absolute;inset:0;width:100%;height:100%;background:{{ in_array($file->id, $selected) ? 'rgba(124,58,237,0.45)' : 'rgba(0,0,0,0)' }};border:none;cursor:pointer;transition:background 0.15s;">
                            @if(in_array($file->id, $selected))
                                <svg style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:28px;height:28px;color:#fff;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            @endif
                        </button>
                    @endif

                    {{-- Delete button (non-picker mode) --}}
                    @if(!$pickerMode)
                        <button type="button"
                            wire:click="deleteFile('{{ $file->id }}')"
                            wire:confirm="Remove this file from your library?"
                            style="position:absolute;top:4px;right:4px;width:22px;height:22px;background:rgba(0,0,0,0.55);border:none;border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                            <svg style="width:11px;height:11px;color:#fff;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    @endif

                    {{-- File name tooltip --}}
                    <div style="position:absolute;bottom:0;left:0;right:0;background:linear-gradient(transparent,rgba(0,0,0,0.6));padding:0.375rem 0.375rem 0.3rem;">
                        <p style="font-size:0.6rem;color:#fff;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $file->filename }}</p>
                        <p style="font-size:0.58rem;color:rgba(255,255,255,0.7);margin:0;">{{ $file->file_size_kb }} KB</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Picker confirm bar --}}
        @if($pickerMode && count($selected))
            <div style="position:sticky;bottom:0;background:#fff;border-top:1px solid #E2E8F0;padding:0.875rem 0;display:flex;align-items:center;justify-content:space-between;gap:1rem;">
                <span style="font-size:0.85rem;color:#64748B;">{{ count($selected) }} file(s) selected</span>
                <button type="button" wire:click="confirmSelection"
                    style="padding:0.45rem 1.25rem;background:#7C3AED;color:#fff;border:none;border-radius:8px;font-size:0.82rem;font-weight:600;cursor:pointer;">
                    Add to post
                </button>
            </div>
        @endif

        {{-- Pagination --}}
        @if(!$pickerMode)
            <div style="margin-top:0.5rem;">
                {{ $files->links() }}
            </div>
        @endif
    @endif

</div>
