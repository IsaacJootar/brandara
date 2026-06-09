<div>

    {{-- Trigger button --}}
    @if(!$open)
        <button type="button" wire:click="openPicker"
            style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.45rem 0.875rem; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; font-size:0.82rem; color:#374151; font-weight:500; cursor:pointer; transition:border-color 0.15s;"
            onmouseover="this.style.borderColor='#7C3AED'" onmouseout="this.style.borderColor='#E2E8F0'">
            <svg style="width:14px;height:14px;color:#7C3AED;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Add media
        </button>
    @endif

    {{-- Modal --}}
    @if($open)
        <div style="position:fixed;inset:0;z-index:50;display:flex;align-items:flex-end;justify-content:center;padding:1rem;"
             x-data x-on:keydown.escape.window="$wire.closePicker()">

            {{-- Backdrop --}}
            <div wire:click="closePicker"
                 style="position:fixed;inset:0;background:rgba(0,0,0,0.45);"></div>

            {{-- Panel --}}
            <div style="position:relative;width:100%;max-width:680px;max-height:80vh;background:#fff;border-radius:18px 18px 0 0;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 -4px 32px rgba(0,0,0,0.12);">

                {{-- Header --}}
                <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;border-bottom:1px solid #F1F5F9;flex-shrink:0;">
                    <div>
                        <p style="font-size:0.95rem;font-weight:700;color:#0F172A;margin:0 0 0.125rem;">Media library</p>
                        <p style="font-size:0.75rem;color:#94A3B8;margin:0;">Pick files to attach to your post</p>
                    </div>
                    <button type="button" wire:click="closePicker"
                        style="width:28px;height:28px;background:#F1F5F9;border:none;border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:14px;height:14px;color:#64748B;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Search --}}
                <div style="padding:0.75rem 1.25rem;border-bottom:1px solid #F1F5F9;flex-shrink:0;">
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search files…"
                        style="width:100%;padding:0.4rem 0.75rem;border:1px solid #E2E8F0;border-radius:8px;font-size:0.82rem;color:#0F172A;outline:none;">
                </div>

                {{-- Grid --}}
                <div style="overflow-y:auto;padding:1rem 1.25rem;flex:1;">
                    @if($files->isEmpty())
                        <div style="text-align:center;padding:2.5rem 1rem;color:#94A3B8;">
                            <p style="font-size:0.875rem;margin:0;">No files in your library yet.</p>
                            <a href="{{ route('media', ['brand' => \App\Models\Brand::find($brandId)?->slug ?? '']) }}"
                               style="font-size:0.8rem;color:#7C3AED;text-decoration:none;font-weight:500;">Upload files →</a>
                        </div>
                    @else
                        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:0.625rem;">
                            @foreach($files as $file)
                                @php $url = $service->url($file); @endphp
                                <button type="button" wire:click="toggleSelect('{{ $file->id }}')"
                                    style="position:relative;aspect-ratio:1;border-radius:9px;overflow:hidden;border:2px solid {{ in_array($file->id, $selected) ? '#7C3AED' : '#E2E8F0' }};cursor:pointer;background:#F8FAFC;padding:0;transition:border-color 0.15s;">

                                    @if(str_starts_with($file->mime_type, 'image/'))
                                        <img src="{{ $url }}" alt="{{ $file->filename }}"
                                            style="width:100%;height:100%;object-fit:cover;display:block;">
                                    @else
                                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                                            <svg style="width:24px;height:24px;color:#7C3AED;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.868V15.13a1 1 0 01-1.447.9L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                                            </svg>
                                        </div>
                                    @endif

                                    @if(in_array($file->id, $selected))
                                        <div style="position:absolute;inset:0;background:rgba(124,58,237,0.35);display:flex;align-items:center;justify-content:center;">
                                            <svg style="width:22px;height:22px;color:#fff;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                @if(count($selected))
                    <div style="padding:0.875rem 1.25rem;border-top:1px solid #E2E8F0;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
                        <span style="font-size:0.82rem;color:#64748B;">{{ count($selected) }} selected</span>
                        <button type="button" wire:click="confirmSelection"
                            style="padding:0.45rem 1.25rem;background:#7C3AED;color:#fff;border:none;border-radius:8px;font-size:0.82rem;font-weight:600;cursor:pointer;">
                            Add to post
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>
