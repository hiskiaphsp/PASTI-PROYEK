<x-admin-layout title="Tambah Layanan">
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/select2.css')}}">
@endsection

@section('breadcrumb-title')
<h3>Add Service</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Services</li>
<li class="breadcrumb-item active">Add Service</li>
@endsection
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Add Service</h5>
                    </div>
                    <div class="card-body">
                        <form enctype='multipart/form-data' action="{{route('admin.service.store')}}" method="post">
                            @csrf
                            <div class="mb-3">
                                <label class="col-form-label pt-0" for="service_name">Service Name</label>
                                <input class="form-control @error('service_name') is-invalid @enderror" id="service_name" type="text"placeholder="Enter service Name" name="service_name">
                                 @error('service_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="col-form-label pt-0" for="price">Service Price</label>
                                <input class="form-control @error('price') is-invalid @enderror" id="price" type="text"placeholder="Enter service Price" name="price">
                                 @error('price')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="col-form-label pt-0" for="service_image">Service Image</label>
                                <input class="form-control @error('service_image') is-invalid @enderror" id="service_image" name="service_image" type="file">
                                @error('service_image')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="col-form-label pt-0" for="description">Service Description</label>
                                    <textarea id="text-box" name="description" cols="10" rows="2"></textarea>
                                {{-- <textarea class="CodeMirror smde @error('description') is-invalid @enderror" id="description" name="description"></textarea> --}}
                                @error('description')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="card-footer text-end">
                                <button class="btn btn-primary" type="submit">Submit</button>
                                <button class="btn btn-secondary">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@section('script')
    <script src="{{asset('assets/js/editor/ckeditor/ckeditor.js')}}"></script>
    <script src="{{asset('assets/js/editor/ckeditor/adapters/jquery.js')}}"></script>
    <script src="{{asset('assets/js/select2/select2.full.min.js')}}"></script>
    <script src="{{asset('assets/js/select2/select2-custom.js')}}"></script>
    <script src="{{asset('assets/js/email-app.js')}}"></script>
    <script src="{{asset('assets/js/form-validation-custom.js')}}"></script>
@endsection
</x-admin-layout>

