@extends('layouts.site-layout')
@section('title', 'Sakthi Rice | Know More About Rice')
@section('content')
<!--====== Breadcrumb Part Start ======-->
<div class="breadcrumb-area">
    <div class="container-fluid custom-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Know More About Rice</li>
            </ol>
        </nav>
    </div>
</div>
<!--====== Breadcrumb Part Ends ======-->
<!--====== Rice Details Part Start ======-->
<section class="features-banner-area pt-80 pb-100">
    <div class="container-fluid custom-container">
        <div class="features-banner-wrapper d-flex flex-wrap">
            <div class="single-features-banner d-flex">
                <div class="banner-icon">
                    <i class="fa fa-history" style="font-size: 40px;color: #000000;"></i>
                </div>
                <div class="banner-content media-body">
                    <h3 class="banner-title">5,000 YEARS</h3>
                    <p>Rice has been feeding us for 5,000 years. The first known account was in China about 2,800 BC.</p>
                </div>
            </div>
            <div class="single-features-banner d-flex"></div>
            <div class="single-features-banner d-flex">
                <div class="banner-icon">
                    <i class="fa fa-suitcase" style="font-size: 40px;color: #000000;"></i>
                </div>
                <div class="banner-content media-body">
                    <h3 class="banner-title">STRENGTH AND STABILITY</h3>
                    <p>Rice has been found in mediaeval Chinese walls where they were added for strength and stability.</p>
                </div>
            </div>
            <div class="single-features-banner d-flex"></div>
            <div class="single-features-banner d-flex">
                <div class="banner-icon">
                    <i class="fa fa-leaf" style="font-size: 40px;color: #000000;"></i>
                </div>
                <div class="banner-content media-body">
                    <h3 class="banner-title">RICE PRODUCTION</h3>
                    <p>More than half the labour force in Thailand is involved in rice production.</p>
                </div>
            </div>
            <div class="single-features-banner d-flex">
                <div class="banner-icon">
                    <i class="fa fa-plus-circle" style="font-size: 40px;color: #000000;"></i>
                </div>
                <div class="banner-content media-body">
                    <h3 class="banner-title">LIFE AND FERTILITY</h3>
                    <p>Rice is a symbol of life and fertility, which is why rice was traditionally thrown at hindu weddings.</p>
                </div>
            </div>
            <div class="single-features-banner d-flex"></div>
            <div class="single-features-banner d-flex">
                <div class="banner-icon">
                    <i class="fa fa-globe" style="font-size: 40px;color: #000000;"></i>
                </div>
                <div class="banner-content media-body">
                    <h3 class="banner-title">EVERY CONTINENT</h3>
                    <p>There are more than 40,000 varieties of rice that grow across every continent.</p>
                </div>
            </div>
            <div class="single-features-banner d-flex"></div>
            <div class="single-features-banner d-flex">
                <div class="banner-icon">
                    <i class="fa fa-flag" style="font-size: 40px;color: #000000;"></i>
                </div>
                <div class="banner-content media-body">
                    <h3 class="banner-title">IN INDIA</h3>
                    <p>In India, rice is associated with prosperity and with the Hindu goddess of wealth, Lakshmi.</p>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="newsletter-area pt-100 pb-100 bg_cover" style="background-image: url(public/site-assets/images/site/bens.png)">
    <div class="container">
        <div class="row justify-content-end">
            <div class="col-lg-8">
                <div class="newsletter-content">
                    <h2 class="newsletter-title">RICE FACTS</h2>
                    <p>Rice Is One Of The Most Cultivated Grains In The World. It Belongs To The Family Poaceae. There Are 40 000 Varieties Of Rice That Differ In Size, Texture, Color And Taste. Rice Originates From China. People Consume Rice At Least 4000 Years. Rice Can Be Found On All Continents Except On The Antarctica. Hundreds Of Millions Tons Of Rice Are Produced Each Year. Depending On The Country, Rice Can Be Planted And Harvested Either Manually (Asia) Or Mechanically (North America). New Varieties Of Rice Can Tolerate Drought, Floods And Salty Terrains. Bacterial And Fungal Diseases And Insects Are Major Threats For The Successful Harvest.</p>
                </div>
            </div>
        </div>
    </div>
</section>



<section class="contact-page pt-20">
    <div class="container-fluid custom-container">
        <h2 class="newsletter-title" style="text-align: center;">Interesting Rice Facts</h2>
        <div class="row">
            <div class="col-lg-4 col-xl-4">
                <div class="contact-info mt-30">
                    <ul class="contact-info-block">
                        <li>
                            <p>Size of rice depends on the variety. It usually grows from 3.3 to 5.9 inches in height.</p>
                        </li>
                        <br>
                        <li>
                            <p> Rice has elongated, slender leaves that can reach 20 to 39 inches in length.</p>
                        </li>
                        <br>
                        <li>
                            <p>Rice develops small flowers collected in pendulous inflorescence. Flowers are pollinated by wind.</p>
                        </li>
                        <br>
                        <li>
                            <p>
                                Seed of rice is edible part of the plant that is widely used in human diet.
                            </p>
                        </li>
                        <br>
                        <li>
                            <p>Rice can be white, yellow, golden, brown, purple, red or black in color. All varieties of rice are divided in three major categories based on the size of grain: long, medium and short. Medium and short types are sticky due to high content of starch.</p>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-xl-4">
                <div class="contact-info mt-30">
                    <ul class="contact-info-block">
                        <li>
                            <p>Rice is staple food in most countries in the world. 90% of global rice is produced and consumed in Asia.</p>
                        </li>
                        <br>
                        <li>
                            <p>Most rice is consumed in Burma: 500 pounds of rice per person each year.</p>
                        </li>
                        <br>
                        <li>
                            <p>Rice is rich source of sugars, proteins and vitamins of the B group.</p>
                        </li>
                        <br>
                        <li>
                            <p>
                                Rice need to be cooked before consumption. Grains of rice swell and become three times bigger during cooking. Rice can be consumed as a part of numerous sweet and salty dishes.
                            </p>
                        </li>
                        <br>
                        <li>
                            <p>Depending on the culture, rice can be consumed with fork (western societies), chopsticks (China, Korea and Japan) or with hands (India).</p>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-xl-4">
                <div class="contact-info mt-30">
                    <ul class="contact-info-block">
                        <li>
                            <p>Raw rice is used in the production of alcoholic beverages such as rice wine and sake, or in the production of rice milk.</p>
                        </li>
                        <br>
                        <li>
                            <p>Scientists created genetically altered type of rice that contains beta-carotene. This type of rice is golden in color. Goal of this project is to prevent vitamin deficiency in areas with low level of vitamin A in diet.</p>
                        </li>
                        <br>
                        <li>
                            <p>Rice symbolizes life and fertility. It is often thrown above the heads of newlyweds after wedding ceremony to ensure happy life filled with children.</p>
                        </li>
                        <br>
                        <li>
                            <p>Rice can be used as a piece of jewelry. Even though small in size, grain of rice can be personalized with individual’s name. That grain can be worn on a necklace after it is placed in the glass tube.</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="newsletter-area pt-100 pb-100 bg_cover" style="margin-top:20px;background-image: url(public/site-assets/images/site/bens.png)">
    <div class="container">
        <div class="row justify-content-end">
            <div class="col-lg-8">
                <div class="newsletter-content">
                    <h2 class="newsletter-title">RICE : NUTRITION</h2>
                    <p>Rice Whole Rice Is Low In Calories And Fat, Sodium-Free, And Rich In Complex Carbohydrates, Vitamins, Minerals, And Fiber. It’s No Wonder That Rice Is A Staple Food For A Large Segment Of The World’s Population. Although Rice Is Lower In Protein Than Other Cereal Grains, Its Protein Quality Is Good Because It Contains Relatively High Levels Of The Essential Amino Acid Lysine.
When White Rice Is Refined, It Is Milled And Polished, A Process That Removes The Bran And Germ As Well As Many Valuable Nutrients. In The US, However, Most White Rice Is Enriched With Thiamin, Niacin, Folate, And Iron, As Well As Fiber And Selenium.
Brown Rice, Which Has Only The Outer Hull Removed, Retains—Along With Its Bran Layer—Thiamin, Niacin, And Vitamin B6. And Because The Bran Is Not Milled Away, Brown Rice Is A Better Source Of Fiber Than White Rice. Brown Rice Supplies 3.5 Grams Of Fiber Per 1-Cup Serving—Five Times More Fiber Than You Get From White Rice.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!--====== Rice Details Part Ends ======-->
@endsection