<a href="{{ route('store.cake', $cake->slug) }}" class="card">
    <div class="ph"><img src="{{ $cake->image_url }}" alt="{{ $cake->name }}" loading="lazy"></div>
    <div class="body">
        <div class="tag">{{ \App\Models\Product::TYPES[$cake->product_type] ?? 'Cake' }}</div>
        <h3>{{ $cake->name }}</h3>
        <div class="price">From KES {{ number_format($cake->min_price) }}</div>
    </div>
</a>
