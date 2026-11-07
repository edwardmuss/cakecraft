@extends('frontend.layouts.app')

@section('title', "$blog_post->title | RebueText - High-Volume Messaging for Businesses")
@section('keywords', "Reach thousands instantly with RebueText's Bulk SMS services. Whether for marketing, alerts, or notifications, our scalable platform ensures fast, reliable, and cost-effective message delivery worldwide")
@section('description', "$blog_post->excerpt")
@section('image', asset('storage/' . $blog_post->featured_image))

@section('breadcrumb-area')
<!-- breadcrumb-area start -->
<div class="breadcrumb-area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb_box text-center">
                    <h2 class="breadcrumb-title">Blog Details</h2>
                    <!-- breadcrumb-list start -->
                    <ul class="breadcrumb-list">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('blogs.home') }}">Blog</a></li>
                        <li class="breadcrumb-item active">Blog Details </li>
                    </ul>
                    <!-- breadcrumb-list end -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- breadcrumb-area end -->
@endsection

@section('content')

<div class="site-wrapper-reveal">










    <!--====================  Blog Area Start ====================-->
    <div class="blog-pages-wrapper section-space--ptb_100">
        <div class="container">
            <div class="row">

                <div class="col-lg-4 order-lg-2 order-2">
                    <div class="page-sidebar-content-wrapper page-sidebar-right small-mt__40 tablet-mt__40">

                        <!--=== Sidebar Widget Start ===-->
                        <div class="sidebar-widget search-post wow move-up">
                            <div class="widget-title">
                                <h4 class="sidebar-widget-title">Search</h4>
                            </div>
                            <form action="{{ route('blogs.search') }}" method="GET">
                                <div class="widget-search">
                                    <input type="text" name="query" placeholder="Enter search keyword…">

                                    <button type="submit" class="search-submit">
                                        <span class="search-btn-icon fa fa-search"></span>
                                    </button>

                                </div>
                            </form>
                        </div>
                        <!--=== Sidebar Widget End ===-->


                        <!-- === Sidebar Widget Start === -->
                        <div class="sidebar-widget widget-blog-recent-post wow move-up">

                            <h6 class="text-uppercase mt-3 mb-2">Recent Posts</h6>

                            <ul>
                                @foreach ($recent_posts as $post)
                                <li>
                                    <a href="{{ route('blogs.single', $post->slug) }}">{!! $post->title !!}</a>
                                </li>
                                @endforeach
                            </ul>

                            <h6 class="text-uppercase mt-3 mb-2">Popular Posts</h6>
                            <ul>
                                @foreach ($popular_posts as $post)
                                <li>
                                    <a class="text-color-secondary" href="{{ route('blogs.single', $post->slug) }}">{!! $post->title !!}</a>
                                </li>
                                @endforeach
                            </ul>

                            <h6 class="text-uppercase mt-3 mb-2">Categories</h6>
                            <ul>
                                @foreach ($categories as $category)
                                    <li>
                                        <a href="{{ route('blogs.category', $category->id) }}">
                                            {!! $category->name !!} ({{ $category->blogs_count }})
                                        </a>
                                    </li>
                                @endforeach
                            </ul>

                        </div>
                        <!-- === Sidebar Widget End === -->

                        <!-- === Sidebar Widget Start === -->
                        <div class="sidebar-widget widget-images wow move-up">

                            {{-- <img class="img-fluid" src="frontend/assets/images/blog/blog-sidebar-ads.webp" alt=""> --}}

                        </div>
                        <!-- === Sidebar Widget End === -->

                        <!-- === Sidebar Widget Start === -->
                        {{-- <div class="sidebar-widget widget-tag wow move-up">
                            <h4 class="sidebar-widget-title">Tags</h4>
                            <a href="#" class="ht-btn ht-btn-xs">business</a>
                            <a href="#" class="ht-btn ht-btn-xs">featured</a>
                            <a href="#" class="ht-btn ht-btn-xs">IT Security</a>
                            <a href="#" class="ht-btn ht-btn-xs">IT services</a>

                        </div> --}}
                        <!-- === Sidebar Widget End === -->
                    </div>
                </div>
                <div class="col-lg-8 order-lg-1 order-1">
                    <div class="main-blog-wrap">
                        <!--======= Single Blog Item Start ========-->
                        <div class="single-blog-item">
                            <!-- Post Feature Start -->
                            <div class="post-feature blog-thumbnail  wow move-up">
                                <img class="img-fluid" src="{{ asset('storage/' . $blog_post->featured_image) }}" alt="{!! $blog_post->title !!}">
                            </div>
                            <!-- Post Feature End -->

                            <!-- Post info Start -->
                            <div class="post-info lg-blog-post-info  wow move-up">
                                <div class="post-categories">
                                    <a href="{{ route('blogs.category', $blog_post->category->id) }}"> {{ $blog_post->category->name }}</a>
                                </div>

                                <h3 class="post-title">{!! $blog_post->title !!}</h3>

                                <div class="post-meta mt-20">
                                    <div class="post-author">
                                        <a href="#">
                                            <img class="img-fluid avatar-96" src="{{ asset('build/images/fav.png') }}" alt="RebueText"> RebueText
                                        </a>
                                    </div>
                                    <div class="post-date">
                                        <span class="far fa-calendar meta-icon"></span>
                                        {{ $blog_post->created_at->format('F j, Y') }}
                                    </div>
                                    <div class="post-view">
                                        <span class="meta-icon far fa-eye"></span>
                                        {{ $blog_post->views }} views
                                    </div>
                                    {{-- <div class="post-comments">
                                        <span class="far fa-comment-alt meta-icon"></span>
                                        <a href="#" class="smooth-scroll-link">4 Comments</a>
                                    </div> --}}
                                </div>

                                <div class="post-excerpt content-typography mt-15">
                                    {!! $blog_post->content !!}

                                    <div class="entry-post-share-wrap  border-bottom">
                                        <div class="row align-items-center">
                                            <div class="col-lg-6 col-md-6">
                                                Spread the love
                                                {{-- <div class="entry-post-tags">
                                                    <div class="tagcloud-icon">
                                                        <i class="far fa-tags"></i>
                                                    </div>
                                                    <div class="tagcloud">
                                                        <a href="#"> business</a>, <a href="#">IT Security</a>, <a href="#">IT services</a>
                                                    </div>
                                                </div> --}}
                                            </div>

                                            <div class="col-lg-6 col-md-6">
                                                <div class="entry-post-share">
                                                    <div class="share-label">
                                                        Share this post
                                                    </div>
                                                    <div class="share-media">
                                                        <span class="share-icon fas fa-share-alt"></span>
                                                        
                                                        <div class="share-list">
                                                            <!-- Generate the full URL to share -->
                                                            @php
                                                                // Generate the full URL for the blog post
                                                                $shareUrl = route('blogs.single', $blog_post->slug);
                                                            @endphp
                                                    
                                                            <a class="hint--bounce hint--top hint--primary twitter" 
                                                               target="_blank" 
                                                               aria-label="Twitter" 
                                                               href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ urlencode($blog_post->title) }}" 
                                                               title="Share on Twitter">
                                                                <i class="ri-twitter-line"></i>
                                                            </a>
                                                            
                                                            <a class="hint--bounce hint--top hint--primary facebook" 
                                                               target="_blank" 
                                                               aria-label="Facebook" 
                                                               href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" 
                                                               title="Share on Facebook">
                                                                <i class="fab fa-facebook-f"></i>
                                                            </a>
                                                            
                                                            <a class="hint--bounce hint--top hint--primary linkedin" 
                                                               target="_blank" 
                                                               aria-label="LinkedIn" 
                                                               href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($shareUrl) }}" 
                                                               title="Share on LinkedIn">
                                                                <i class="fab fa-linkedin"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="entry-author">
                                        <div class="author-info">
                                            <div class="author-avatar">
                                                <img src="{{ asset('build/images/fav.png') }}" width="90" alt="">
                                                <div class="author-social-networks">
                                                    <div class="inner">
                                                        <a class="hint--bounce hint--top hint--primary" 
                                                           aria-label="Twitter" 
                                                           href="https://twitter.com/cloudrebue" 
                                                           target="_blank">
                                                            <i class="fab fa-twitter"></i>
                                                        </a>
                                                
                                                        <a class="hint--bounce hint--top hint--primary" 
                                                           aria-label="Facebook" 
                                                           href="https://www.facebook.com/cloudrebue" 
                                                           target="_blank">
                                                            <i class="fab fa-facebook-f"></i>
                                                        </a>
                                                
                                                        <a class="hint--bounce hint--top hint--primary" 
                                                           aria-label="Instagram" 
                                                           href="https://www.instagram.com/cloudrebue/" 
                                                           target="_blank">
                                                            <i class="fab fa-instagram"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="author-description">
                                                <h6 class="author-name">RebueText</h6>

                                                <div class="author-biographical-info">
                                                    Founded with the vision of bridging the gap between businesses and their audiences, we offer innovative, reliable, and scalable messaging solutions that drive engagement and deliver measurable results.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="related-posts-wrapper">

                                        <div class="row">
                                            @foreach ($related_posts as $post)
                                            <div class="col-lg-6">
                                                <!-- Single Valid Post Start -->
                                                <a class="single-valid-post-wrapper" href="{{ route('blogs.single', $post->slug) }}">
                                                    <div class="single-blog__item">
                                                        <div class="single-valid__thum bg-img" data-bg="{{ asset('storage/' .$post->featured_image) }}">
                                                        </div>

                                                        <div class="post-content">
                                                            <h6 class="post-title font-weight--bold">{{ $post->title }}</h6>
                                                        </div>

                                                    </div>
                                                </a>
                                                <!-- Single Valid Post End -->
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="comment-list-wrapper">

                                        <div class="row">

                                            <div class="col-lg-12">
                                                <h4 class="widget-title mb-20">Leave your thought here </h4>
                                                <p>Your email address will not be published.</p>
                                                <div id="disqus_thread"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- Post info End -->
                        </div>
                        <!--===== Single Blog Item End =========-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--====================  Blog Area End  ====================-->






    <!--========== Call to Action Area Start ============-->
    <div class="cta-image-area_one section-space--ptb_80 cta-bg-image_two">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-xl-7 col-lg-7">
                    <div class="cta-content md-text-center">
                        <h3 class="heading">Fuel connections with RebueText<span class="text-color-primary"> Solutions today!</span></h3>
                    </div>
                </div>
                <div class="col-xl-5 col-lg-5">
                    <div class="cta-button-group--two text-center">
                        <a href="{{ route('register') }}" class="btn btn--white btn-one"><span class="btn-icon me-2"><i class="far fa-user"></i></span> Sign Up</a>
                        <a href="https://rebuetext.com" target="_blank" class="btn btn--secondary btn-two "><span class="btn-icon me-2"><i class="fab fa-whatsapp"></i></span> WhatsApp</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--========== Call to Action Area End ============-->




</div>

<script>
    /**
    *  RECOMMENDED CONFIGURATION VARIABLES: EDIT AND UNCOMMENT THE SECTION BELOW TO INSERT DYNAMIC VALUES FROM YOUR PLATFORM OR CMS.
    *  LEARN WHY DEFINING THESE VARIABLES IS IMPORTANT: https://disqus.com/admin/universalcode/#configuration-variables    */
    /*
    var disqus_config = function () {
    this.page.url = PAGE_URL;  // Replace PAGE_URL with your page's canonical URL variable
    this.page.identifier = PAGE_IDENTIFIER; // Replace PAGE_IDENTIFIER with your page's unique identifier variable
    };
    */
    (function() { // DON'T EDIT BELOW THIS LINE
    var d = document, s = d.createElement('script');
    s.src = 'https://cloud-rebue.disqus.com/embed.js';
    s.setAttribute('data-timestamp', +new Date());
    (d.head || d.body).appendChild(s);
    })();
</script>
<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>

@endsection