<!--====================  testimonial section ====================-->
<div class="testimonial-slider-area section-space--pt_100">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title-wrap text-center section-space--mb_40">
                    <h6 class="section-sub-title mb-20">Testimonials</h6>
                    <h3 class="heading">What do people praise about <span class="text-color-primary"> RebueText?</span></h3>
                </div>
                <div class="testimonial-slider">
                    <div class="swiper-container testimonial-slider__container swiper-container-initialized swiper-container-horizontal">
                        <div class="swiper-wrapper testimonial-slider__wrapper" style="transform: translate3d(-1206px, 0px, 0px); transition-duration: 0ms;">
                            
                            @foreach($testimonials as $testimonial)
                            <div class="swiper-slide" data-swiper-slide-index="{{ $loop->index }}" style="width: 573px; margin-right: 30px;">
                                <div class="testimonial-slider__one wow move-up">
                                    <div class="testimonial-slider--info">
                                        <div class="testimonial-slider__media">
                                            <img src="{{ asset($testimonial->image) }}" class="img-fluid" alt="{{ $testimonial->name }}" width="90">
                                        </div>
                                        <div class="testimonial-slider__author">
                                            <div class="testimonial-rating">
                                                @for($i = 0; $i < $testimonial->rating; $i++)
                                                    <span class="fa fa-star"></span>
                                                @endfor
                                            </div>
                                            <div class="author-info">
                                                <h6 class="name">{{ $testimonial->name }}</h6>
                                                <span class="designation">{{ $testimonial->occupation }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="testimonial-slider__text">{{ $testimonial->content }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination swiper-pagination-t01 section-space--mt_30 swiper-pagination-clickable swiper-pagination-bullets"><span class="swiper-pagination-bullet swiper-pagination-bullet-active" tabindex="0" role="button" aria-label="Go to slide 1"></span><span class="swiper-pagination-bullet" tabindex="0" role="button" aria-label="Go to slide 2"></span><span class="swiper-pagination-bullet" tabindex="0" role="button" aria-label="Go to slide 3"></span><span class="swiper-pagination-bullet" tabindex="0" role="button" aria-label="Go to slide 4"></span></div>
                    <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--====================  End of testimonial section  ====================-->