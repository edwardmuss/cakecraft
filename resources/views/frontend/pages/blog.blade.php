@extends('frontend.layouts.app')

@section('title', 'Blog | RebueText - High-Volume Messaging for Businesses')
@section('keywords', "Reach thousands instantly with RebueText's Bulk SMS services. Whether for marketing, alerts, or notifications, our scalable platform ensures fast, reliable, and cost-effective message delivery worldwide")
@section('description', 'RebueText offers robust and reliable messaging solutions, including Bulk SMS, WhatsApp messaging, Shortcodes, and USSD services. Drive engagement and reach your audience effectively with our secure, scalable platform.')
@section('image', asset('frontend/assets/images/banners/whatsapp.jpg'))

@section('breadcrumb-area')
<!-- breadcrumb-area start -->
<div class="breadcrumb-area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb_box text-center">
                    <h2 class="breadcrumb-title">Blog</h2>
                    <!-- breadcrumb-list start -->
                    <ul class="breadcrumb-list">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Blog</li>
                    </ul>
                    <div class="col-md-6 offset-md-3 mt-3">
                        <form action="{{ route('blogs.search') }}" method="GET">
                            <div class="widget-search">
                                <input type="text" name="query" placeholder="Enter search keyword…">

                                <button type="submit" class="search-submit">
                                    <span class="search-btn-icon fa fa-search"></span>
                                </button>

                            </div>
                        </form>
                    </div>
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



    <!--====================   Blog Area Start ====================-->
    <div class="blog-pages-wrapper section-space--ptb_100">
        <div class="container">
            <div class="row">

                <div class="col-lg-12 order-lg-2 order-1">
                    {{-- <div class="section-title-wrap text-center section-space--mb_60">
                        <h3 class="heading">Interesting articles<span class="text-color-primary"> updated daily</span></h3>
                    </div> --}}
                    <div class="main-blog-wrap">
                        @if($blogs->isEmpty())
                            <div class="ht-message-box style-error mb-30 mt-30" role="alert">
                                <span class="icon"><i class="far fa-times-circle"></i></span> We regret! No blog posts found.
                            </div>
                        @else
                            @foreach($blogs as $post)
                                <!--======= Single Blog Item Start ========-->
                                <div class="single-blog-item post-list-wrapper xs-list-blog-item border-bottom wow move-up">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <!-- Post Feature Start -->
                                            <div class="post-feature blog-thumbnail">
                                                <a href="{{ route('blogs.single', $post->slug) }}">
                                                    <img class="img-fluid" src="{{ asset('storage/'. $post->featured_image) }}" alt="{{ $post->title }}">
                                                </a>
                                            </div>
                                            <!-- Post Feature End -->
                                        </div>

                                        <div class="col-lg-6">
                                            <!-- Post info Start -->
                                            <div class="post-info lg-blog-post-info">
                                                <h4 class="post-title">
                                                    <a href="{{ route('blogs.single', $post->slug) }}">{{ $post->title }}</a>
                                                </h4>

                                                <div class="post-meta mt-20">
                                                    <div class="post-author">
                                                        <a href="#">
                                                            <img class="img-fluid avatar-96" src="{{ asset('build/images/fav.png') }}" alt=""> RebueText
                                                        </a>
                                                    </div>
                                                    <div class="post-date">
                                                        <span class="far fa-calendar meta-icon"></span>
                                                        {{ $post->created_at->format('F j, Y') }}
                                                    </div>
                                                    <div class="post-view">
                                                        <span class="meta-icon far fa-eye"></span>
                                                        {{ $post->views }} views <a href="{{ route('blogs.single', $post->slug) }}#disqus_thread"></a>
                                                    </div>
                                                </div>

                                                <div class="post-excerpt txt-truncate mt-15">
                                                    <p>{{ $post->excerpt }}</p>
                                                </div>

                                                <div class="read-post-share-wrap mt-30">
                                                    <div class="post-read-more">
                                                        <a href="{{ route('blogs.single', $post->slug) }}" class="ht-btn ht-btn-md">Read more</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Post info End -->
                                        </div>
                                    </div>
                                </div>
                                <!--===== Single Blog Item End =========-->
                            @endforeach
                        @endif

                        <div class="ht-pagination mt-30 pagination justify-content-center">
                            <div class="pagination-wrapper">
                                {{ $blogs->links() }}
                            </div>
                        </div>

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
<script id="dsq-count-scr" src="//cloud-rebue.disqus.com/count.js" async></script>

@endsection