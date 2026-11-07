<!--====================  brand logo slider area ====================-->
<div class="brand-logo-slider-area section-space--ptb_60">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <!-- brand logo slider -->
                <div class="brand-logo-slider__container-area">
                    <div class="swiper-container brand-logo-slider__container swiper-container-initialized swiper-container-horizontal">
                        <div class="swiper-wrapper brand-logo-slider__one" style="transform: translate3d(-3015px, 0px, 0px); transition-duration: 0ms;">
                            @foreach ($clients->sortBy('order') as $client)
                            <div class="swiper-slide brand-logo brand-logo--slider" data-swiper-slide-index="{{ $loop->index }}" style="width: 171px; margin-right: 30px;" title="{{ $client->name }}">
                                <a href="{{ $client->website }}" target="_blank">
                                    <div class="brand-logo__image">
                                        <img src="{{ asset($client->logo) }}" class="img-fluid" alt="{{ $client->name }}">
                                    </div>
                                    <div class="brand-logo__image-hover">
                                        <img src="{{ asset($client->logo) }}" class="img-fluid" alt="{{ $client->name }}">
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--====================  End of brand logo slider area  ====================-->