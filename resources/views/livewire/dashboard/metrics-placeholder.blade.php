<div class="metric-grid">
    @foreach (['metric-violet','metric-blue','metric-amber','metric-teal'] as $variant)
        <div class="metric-card {{ $variant }}">
            <div class="metric-label">&nbsp;</div>
            <div class="metric-value">&nbsp;</div>
            <div class="metric-sub">&nbsp;</div>
        </div>
    @endforeach
</div>
<div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.25rem;">
    @foreach([0,1] as $i)
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; height:160px;"></div>
    @endforeach
</div>
<div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.25rem;">
    @foreach([0,1] as $i)
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; height:180px;"></div>
    @endforeach
</div>
