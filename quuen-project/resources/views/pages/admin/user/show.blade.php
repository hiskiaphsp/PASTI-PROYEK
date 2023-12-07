<x-admin-layout title="Profile">
    @section('breadcrumb-title')
    <h3>User</h3>
    @endsection

    @section('breadcrumb-items')
        <li class="breadcrumb-item">User</li>
        <li class="breadcrumb-item active">User Profile</li>
    @endsection
    <div class="container-fluid">
        <div class="edit-profile">
            <div class="row justify-content-center">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Profile</h4>
                            <div class="card-options"><a class="card-options-collapse" href="#" data-bs-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a><a class="card-options-remove" href="#" data-bs-toggle="card-remove"><i class="fe fe-x"></i></a></div>
                        </div>
                        <div class="card-body">
                            <form enctype='multipart/form-data' class="theme-form" method="post" action="{{route('admin.user.update', $user->id)}}">
                                @method('put')
                                @csrf
                                <div class="row mb-2">
                                <div class="profile-title">
                                    <div class="media">                        <img class="img-70 rounded-circle" alt="" src="{{ asset('assets/images/user/7.jpg') }}">
                                        <div class="media-body">
                                            <h5 class="mb-1">{{ $user->name }}</h5>
                                            <p>{{ $user->getRoleNames()[0] }}</p>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="name" class="col-form-label">Name<span class="text-danger">*</span></label>
                                    <input value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" placeholder="User Name" autofocus old="name">
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">Email Address<span class="text-danger">*</span></label>
                                    <input value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" placeholder="Test@gmail.com" autocomplete="email">
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">Phone Number<span class="text-danger">*</span></label>
                                    <input value="{{ old('nohp', $user->nohp) }}" class="form-control @error('nohp') is-invalid @enderror" id="nohp" name="nohp" type="text" placeholder="62xxxxxxxxx">
                                    @error('nohp')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">Password<span class="text-danger">*</span></label>
                                    <input class="form-control @error('password') is-invalid @enderror" id="password" type="password" name="password" autocomplete="new-password" placeholder="*********">
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="card-footer text-end">
                                    <button class="btn btn-primary" type="submit">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
