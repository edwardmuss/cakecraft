<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', \App\Support\Brand::name())</title>
    <meta name="description" content="@yield('description', \App\Support\Brand::copy('meta_description'))">
    <link rel="icon" href="{{ \App\Support\Brand::logoUrl() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--rose:#b76e79;--rose-dark:#9a5560;--blush:#f8ecee;--cream:#fdf8f5;--ink:#2b2226;--muted:#7b6f73;--line:#eadfe1}
        *{box-sizing:border-box}
        body{margin:0;font-family:Inter,system-ui,sans-serif;color:var(--ink);background:var(--cream);line-height:1.55}
        a{color:inherit;text-decoration:none}
        img{max-width:100%;display:block}
        h1,h2,h3{font-family:"Playfair Display",Georgia,serif;font-weight:600;line-height:1.2;margin:0}
        .wrap{max-width:1160px;margin:0 auto;padding:0 20px}
        .btn{display:inline-block;background:var(--rose);color:#fff;border:0;padding:12px 24px;border-radius:999px;font-weight:600;font-size:14px;cursor:pointer;transition:.2s}
        .btn:hover{background:var(--rose-dark)}
        .btn.ghost{background:transparent;color:var(--rose-dark);border:1.5px solid var(--rose)}
        .btn.ghost:hover{background:var(--blush)}
        header.site{background:#fff;border-bottom:1px solid var(--line);position:sticky;top:0;z-index:20}
        .nav{display:flex;align-items:center;justify-content:space-between;height:68px;gap:16px}
        .brand{display:flex;align-items:center;gap:10px;font-family:"Playfair Display",serif;font-weight:700;font-size:20px}
        .brand img{height:44px;width:44px;border-radius:50%;object-fit:cover}
        .menu{display:flex;gap:22px;font-size:14px;font-weight:500;align-items:center}
        .menu a:hover{color:var(--rose-dark)}
        .menu .drop{position:relative}
        .menu .drop>div{display:none;position:absolute;top:100%;left:-12px;background:#fff;border:1px solid var(--line);border-radius:12px;padding:8px;min-width:200px;box-shadow:0 12px 30px rgba(0,0,0,.08)}
        .menu .drop:hover>div{display:block}
        .menu .drop>div a{display:block;padding:8px 12px;border-radius:8px}
        .menu .drop>div a:hover{background:var(--blush)}
        .burger{display:none;background:none;border:0;font-size:26px;cursor:pointer}
        .hero{background:linear-gradient(135deg,var(--blush),#fff 60%);padding:70px 0}
        .hero .grid{display:grid;grid-template-columns:1.1fr .9fr;gap:40px;align-items:center}
        .hero h1{font-size:clamp(34px,5vw,56px)}
        .hero p{color:var(--muted);font-size:18px;max-width:520px}
        .hero img{border-radius:24px;aspect-ratio:1/1;object-fit:cover;box-shadow:0 30px 60px rgba(183,110,121,.25)}
        .eyebrow{color:var(--rose);font-size:12px;letter-spacing:.2em;text-transform:uppercase;font-weight:600;margin-bottom:10px}
        section.block{padding:60px 0}
        .section-head{text-align:center;margin-bottom:34px}
        .section-head h2{font-size:clamp(26px,3.5vw,38px)}
        .section-head p{color:var(--muted);margin:8px 0 0}
        .cols{display:grid;grid-template-columns:repeat(4,1fr);gap:18px}
        .collection{position:relative;border-radius:18px;overflow:hidden;aspect-ratio:4/5;background:var(--blush)}
        .collection img{width:100%;height:100%;object-fit:cover;transition:.4s}
        .collection:hover img{transform:scale(1.05)}
        .collection span{position:absolute;inset:auto 0 0 0;padding:40px 16px 16px;color:#fff;background:linear-gradient(transparent,rgba(30,15,20,.75));font-family:"Playfair Display",serif;font-size:20px}
        .collection small{display:block;font-family:Inter;font-size:12px;opacity:.85}
        .products{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}
        .card{background:#fff;border:1px solid var(--line);border-radius:16px;overflow:hidden;transition:.25s;display:flex;flex-direction:column}
        .card:hover{transform:translateY(-4px);box-shadow:0 16px 36px rgba(183,110,121,.16)}
        .card .ph{aspect-ratio:1/1;background:var(--blush);overflow:hidden}
        .card .ph img{width:100%;height:100%;object-fit:cover}
        .card .body{padding:14px 16px 18px}
        .card .tag{font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:var(--rose)}
        .card h3{font-size:17px;margin:4px 0 6px}
        .card .price{color:var(--rose-dark);font-weight:600}
        .pills{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:26px;justify-content:center}
        .pills a{padding:8px 16px;border:1px solid var(--line);border-radius:999px;font-size:13px;background:#fff}
        .pills a.on,.pills a:hover{background:var(--rose);color:#fff;border-color:var(--rose)}
        .steps{background:var(--blush);padding:26px 0}
        .steps .row{display:flex;justify-content:space-around;gap:20px;flex-wrap:wrap;font-size:14px}
        .steps b{display:inline-flex;width:28px;height:28px;border-radius:50%;background:var(--rose);color:#fff;align-items:center;justify-content:center;margin-right:8px;font-size:13px}
        footer.site{background:var(--ink);color:#d9cdd0;padding:50px 0 24px;margin-top:40px;font-size:14px}
        footer.site .fgrid{display:grid;grid-template-columns:2fr 1fr 1fr;gap:30px}
        footer.site h4{color:#fff;font-family:"Playfair Display";margin:0 0 12px}
        footer.site a:hover{color:#fff}
        footer.site li{list-style:none;margin:6px 0} footer.site ul{padding:0;margin:0}
        .copy{border-top:1px solid #4a3d42;margin-top:30px;padding-top:16px;font-size:12px;color:#9c8f93}
        .wa{position:fixed;right:18px;bottom:18px;background:#25d366;color:#fff;border-radius:50%;width:56px;height:56px;display:flex;align-items:center;justify-content:center;font-size:28px;box-shadow:0 10px 24px rgba(0,0,0,.25);z-index:30}
        /* product page */
        .pgrid{display:grid;grid-template-columns:1fr 1fr;gap:44px;padding:40px 0}
        .pgrid .main{border-radius:20px;aspect-ratio:1/1;object-fit:cover;width:100%;background:var(--blush)}
        .opt{display:flex;gap:8px;flex-wrap:wrap;margin:8px 0 18px}
        .opt label{cursor:pointer}
        .opt input{position:absolute;opacity:0;pointer-events:none}
        .opt span{display:block;padding:9px 16px;border:1.5px solid var(--line);border-radius:999px;font-size:14px;background:#fff}
        .opt input:checked+span{border-color:var(--rose);background:var(--blush);color:var(--rose-dark);font-weight:600}
        .total{font-size:30px;font-family:"Playfair Display";color:var(--rose-dark);margin:6px 0 4px}
        form.order{background:#fff;border:1px solid var(--line);border-radius:18px;padding:22px;margin-top:20px}
        form.order label.l{font-size:13px;font-weight:600;display:block;margin-bottom:5px}
        form.order input[type=text],form.order input[type=tel],form.order input[type=email],form.order input[type=date],form.order input[type=number],form.order select,form.order textarea{width:100%;padding:11px 13px;border:1px solid #dccfd2;border-radius:10px;font:inherit;background:#fff}
        form.order input:focus,form.order select:focus,form.order textarea:focus{outline:2px solid rgba(183,110,121,.35);border-color:var(--rose)}
        .two{display:grid;grid-template-columns:1fr 1fr;gap:14px}
        .field{margin-bottom:14px}
        .err{color:#c0233f;font-size:13px;margin-top:4px}
        table.menu-table{width:100%;border-collapse:collapse;background:#fff;border-radius:14px;overflow:hidden;border:1px solid var(--line)}
        .menu-table th{background:var(--ink);color:#fff;text-align:right;font-size:12px;letter-spacing:.08em;text-transform:uppercase;padding:12px 14px;white-space:nowrap}
        .menu-table th:first-child,.menu-table td:first-child{text-align:left}
        .menu-table td{padding:11px 14px;border-top:1px solid var(--line);text-align:right;color:var(--rose-dark);font-weight:600;white-space:nowrap}
        .menu-table td:first-child{color:var(--ink);font-family:"Playfair Display";font-weight:600}
        @media(max-width:900px){
            .cols,.products{grid-template-columns:repeat(2,1fr)}
            .hero .grid,.pgrid,footer.site .fgrid{grid-template-columns:1fr}
            .menu{display:none;position:absolute;top:68px;left:0;right:0;background:#fff;flex-direction:column;align-items:flex-start;padding:16px 20px;border-bottom:1px solid var(--line)}
            .menu.open{display:flex}.burger{display:block}
            .menu .drop>div{display:block;position:static;border:0;box-shadow:none;padding:0 0 0 12px}
            .two{grid-template-columns:1fr}
        }
    </style>
    @include('includes.brand-css')
    @stack('head')
</head>
<body>
@php $cc = array_merge(config('cakecraft'), ['business_name' => \App\Support\Brand::name(), 'whatsapp' => \App\Support\Brand::get('whatsapp'), 'phone' => \App\Support\Brand::get('phone'), 'address' => \App\Support\Brand::get('address'), 'tagline' => \App\Support\Brand::get('tagline')]); $navCollections = \App\Models\Collection::where('is_active', true)->where('branch_id', (int)($cc['branch_id'] ?: \App\Models\Branch::query()->orderBy('id')->value('id')))->orderBy('sort_order')->get(); @endphp
<header class="site">
    <div class="wrap nav">
        <a href="{{ route('store.home') }}" class="brand"><img src="{{ \App\Support\Brand::logoUrl() }}" alt=""> {{ $cc['business_name'] }}</a>
        <button class="burger" onclick="document.querySelector('.menu').classList.toggle('open')" aria-label="Menu">&#9776;</button>
        <nav class="menu">
            <a href="{{ route('store.home') }}">Home</a>
            <div class="drop"><a href="{{ route('store.shop') }}">Cakes &#9662;</a>
                <div>
                    <a href="{{ route('store.shop') }}">All cakes</a>
                    @foreach($navCollections as $c)<a href="{{ route('store.collection', $c->slug) }}">{{ $c->name }}</a>@endforeach
                </div>
            </div>
            <a href="{{ route('store.menu') }}">Price list</a>
            <a href="https://wa.me/{{ $cc['whatsapp'] }}" target="_blank">Contact</a>
            <a href="{{ route('store.shop') }}" class="btn" style="padding:9px 20px">Order a cake</a>
        </nav>
    </div>
</header>

@yield('content')

<footer class="site">
    <div class="wrap">
        <div class="fgrid">
            <div><h4>{{ $cc['business_name'] }}</h4><p>{{ $cc['tagline'] }}</p></div>
            <div><h4>Collections</h4><ul>@foreach($navCollections as $c)<li><a href="{{ route('store.collection', $c->slug) }}">{{ $c->name }}</a></li>@endforeach</ul></div>
            <div><h4>Visit &amp; call</h4><ul><li>{{ $cc['address'] }}</li><li><a href="tel:{{ $cc['phone'] }}">{{ $cc['phone'] }}</a></li><li><a href="https://wa.me/{{ $cc['whatsapp'] }}" target="_blank">WhatsApp us</a></li></ul></div>
        </div>
        <div class="copy">&copy; {{ date('Y') }} {{ $cc['business_name'] }}. All rights reserved. &nbsp;&middot;&nbsp; <a href="{{ route('login') }}" style="opacity:.7">Staff login</a></div>
    </div>
</footer>
<a class="wa" href="https://wa.me/{{ $cc['whatsapp'] }}" target="_blank" aria-label="WhatsApp">&#9990;</a>
@stack('scripts')
</body>
</html>
