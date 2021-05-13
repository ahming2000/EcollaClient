@extends('en.layouts.customer')

@section('title')
    About Us | Ecolla
@endsection

@section('content')
    <main class="flex-fill"> <!--put content-->
        <section class="mt-5">
            <div class="container">
                <div class="row pl-3 mx-auto">
                    <div class="col pt-5">
                        <h1 class="about">About</h1><h1 class="us"> Us</h1>
                        <p class="mt-4">Snacks from foreign country China Thailand Korea<br>Best taste ever</p>
                    </div>
                    <div class="col">
                        <img src="{{ asset('img/ads/shop-image.jpg') }}" height="350" width="410" alt="image">
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-5">
            <div class="container">
                <div class="row mx-auto">
                    <div class="col">
                        <img src="{{ asset('img/ads/shop-image.jpg') }}" height="350" width="410" alt="image">
                    </div>
                    <div class="col pt-5">
                        <h1>Operation Hours</h1>
                        <p class="mt-4">10AM - 10PM</p>
                    </div>
                </div>
            </div>
        </section>

    </main>
@endsection
