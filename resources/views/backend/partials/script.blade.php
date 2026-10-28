<!-- latest jquery-->
<script src="{{ asset('backend/js/jquery.min.js') }}"></script>
{{-- dashboard --}}
<script src="{{ asset('backend/js/bootstrap.bundle.min.js') }}"></script>
<!-- Bootstrap js-->
<script src="{{ asset('backend/js/bootstrap.bundle.min.js') }}"></script>
<!-- feather icon js-->
<script src="{{ asset('backend/js/dropify.min.js') }}"></script>
<script src="{{ asset('backend/js/ckeditor.js') }}"></script>
<script src="{{ asset('backend/js/feather.min.js') }}"></script>
<script src="{{ asset('backend/js/feather-icon.js') }}"></script>
<script src="{{ asset('backend/js/datatables.min.js') }}"></script>
<!-- scrollbar js-->
<script src="{{ asset('backend/js/simplebar.js') }}"></script>
<script src="{{ asset('backend/js/custom.js') }}"></script>
<!-- Sidebar jquery-->
<script src="{{ asset('backend/js/config.js') }}"></script>
<!-- Plugins JS start-->
<script src="{{ asset('backend/js/sidebar-menu.js') }}"></script>
<script src="{{ asset('backend/js/sidebar-pin.js') }}"></script>
<script src="{{ asset('backend/js/slick.min.js') }}"></script>
<script src="{{ asset('backend/js/slick.js') }}"></script>
<script src="{{ asset('backend/js/header-slick.js') }}"></script>
{{-- <script src="{{ asset('backend/js/apex-chart.js') }}"></script> --}}
<script src="{{ asset('backend/js/stock-prices.js') }}"></script>
<script src="{{ asset('backend/js/moment.min.js') }}"></script>
<script src="{{ asset('backend/js/esl.js') }}"></script>
<script src="{{ asset('backend/js/echart_config.js') }}"></script>
<script src="{{ asset('backend/js/facePrint.js') }}"></script>
<script src="{{ asset('backend/js/testHelper.js') }}"></script>
<script src="{{ asset('backend/js/custom-transition-texture.js') }}"></script>
<script src="{{ asset('backend/js/symbols.js') }}"></script>
<!-- calendar js-->
<script src="{{ asset('backend/js/datepicker.js') }}"></script>
<script src="{{ asset('backend/js/datepicker.en.js') }}"></script>
<script src="{{ asset('backend/js/datepicker.custom.js') }}"></script>
<script src="{{ asset('backend/js/dashboard_3.js') }}"></script>
<!-- Plugins JS Ends-->
<!-- Theme js-->
<script src="{{ asset('backend/js/script.js') }}"></script>
<script>
    // 1. Register FilePond plugins
    FilePond.registerPlugin(FilePondPluginImagePreview);

    // 2. Query all .filepond inputs
    const inputElements = document.querySelectorAll('.filepond');

    // 3. Initialize FilePond instance for each element
    inputElements.forEach(inputElement => {
        const pond = FilePond.create(inputElement, {
            allowMultiple: false,
            allowReorder: false,
            imagePreviewHeight: 140,
            // Submit file with form data
            storeAsFile: true,
            labelIdle: 'Drag & Drop your picture or <span class="filepond--label-action">Browse</span>',
        });

        // 4. Populate pre-existing files if available
        @isset($setting)
            if (inputElement.id === 'logo' && "{{ asset($setting->logo) }}") {
                pond.addFile("{{ asset($setting->logo) }}");
            }
            if (inputElement.id === 'favicon' && "{{ asset($setting->favicon) }}") {
                pond.addFile("{{ asset($setting->favicon) }}");
            }
        @endisset
    });

    // CKEditor initialization
    ClassicEditor
        .create(document.querySelector('#description'))
        .catch(error => {
            console.error(error);
        });
</script>
@if (session('success') || session('t-success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') ?? session('t-success') }}',
            showConfirmButton: false,
            timer: 2000
        });
    </script>
@endif

@if (session('error') || session('t-error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '{{ session('error') ?? session('t-error') }}',
            confirmButtonColor: '#d33'
        });
    </script>
@endif

{{-- dropify end --}}

@stack('script')
