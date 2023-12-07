{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script> --}}
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{asset('web-assets/js/waypoints.js')}}"></script>
<script src="{{asset('web-assets/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('web-assets/js/swiper-bundle.js')}}"></script>
<script src="{{asset('web-assets/js/nice-select.js')}}"></script>
<script src="{{asset('web-assets/js/slick.js')}}"></script>
<script src="{{asset('web-assets/js/magnific-popup.js')}}"></script>
<script src="{{asset('web-assets/js/counterup.js')}}"></script>
<script src="{{asset('web-assets/js/wow.js')}}"></script>
<script src="{{asset('web-assets/js/isotope-pkgd.js')}}"></script>
<script src="{{asset('web-assets/js/imagesloaded-pkgd.js')}}"></script>
<script src="{{asset('web-assets/js/countdown.js')}}"></script>
<script src="{{asset('web-assets/js/ajax-form.js')}}"></script>
<script src="{{asset('web-assets/js/parallax-effect.min.js')}}"></script>
<script src="{{asset('web-assets/js/meanmenu.js')}}"></script>
<script src="{{asset('web-assets/js/main.js')}}"></script>
<script src="{{ asset('js/sweetalert.js') }}"></script>
<script src="{{ asset('js/method.js') }}"></script>
<script src="{{ asset('js/toastify.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

@yield('script')
<script>

    @if(session('error'))

    // Mengambil nilai session error
    var errorMessage = '{{ session('error') }}';

    // Menampilkan toast dengan pesan error
    Toastify({
        text: errorMessage,
        duration: 3000,
        gravity: 'top',
        position: 'right',
        backgroundColor: 'linear-gradient(to right, #FF0000, #FF5733)',
        progressBar: true, // Add progress bar
        close: true,
    }).showToast();
    @endif

    @if(session('success'))
         // Mengambil nilai session error
        var successMessage = '{{ session('success') }}';
        Toastify({
            text: successMessage,
            duration: 3000,
            gravity: 'top',
            position: 'right',
            backgroundColor: 'linear-gradient(to right, #00b09b, #96c93d)',
            progressBar: true, // Add progress bar
            close: true,
        }).showToast();

    @endif


    $(document).on('click', '.add-to-cart', function() {
        var productId = $(this).data('product-id');
        var quantity = $(this).siblings('input[name="quantity"]').val();

        $.ajax({
            url: '{{ route('product.addToCart') }}',
            type: 'POST',
            dataType: 'json',
            data: {
                'product_id': productId,
                'quantity': quantity,
                '_token': '{{ csrf_token() }}'
            },
            success: function(response) {
                Toastify({
                    text: response.message,
                    duration: 3000,
                    gravity: 'top',
                    position: 'right',
                    backgroundColor: 'linear-gradient(to right, #00b09b, #96c93d)',
                    progressBar: true,
                    close: true,
                }).showToast();
            },
            error: function(xhr, status, error) {
                var errorMessage = JSON.parse(xhr.responseText).message;
                Toastify({
                    text: errorMessage,
                    duration: 3000,
                    gravity: 'top',
                    position: 'right',
                    backgroundColor: 'linear-gradient(to right, #FF0000, #FF5733)',
                    progressBar: true,
                    close: true,
                }).showToast();
            }
        });
    });


</script>
<script src="{{ asset('js/jquery.slimscroll.min.js') }}"></script>
{{-- <script src="{{ asset('js/notif-user.js') }}"></script> --}}


<script>

</script>

<script>
    $(document).ready(function() {
        var height = $('.navi').data('height');
        var mobile_height = $('.navi').data('mobile-height');
        $('#notification_items').slimScroll({
            height: height,
            mobileHeight: mobile_height,
            color: '#fff',
            alwaysVisible: true,
            railVisible: true,
            railColor: '#fff',
            railOpacity: 1,
            wheelStep: 10,
            allowPageScroll: true,
            disableFadeOut: false
        });
        $('#notification_items_top').slimScroll({
            height: height,
            mobileHeight: mobile_height,
            color: '#fff',
            alwaysVisible: true,
            railVisible: true,
            railColor: '#fff',
            railOpacity: 1,
            wheelStep: 10,
            allowPageScroll: true,
            disableFadeOut: false
        });
    });
</script>
