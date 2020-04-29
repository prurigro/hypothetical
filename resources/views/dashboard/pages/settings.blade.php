@extends('dashboard.core', [
    'heading' => 'Settings'
])

@section('dashboard-body')
    <input type="hidden" id="token" value="{{ csrf_token() }}" />

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="dashboard-settings-container">
                    <form id="user-profile-image" class="user-profile-image">
                        @set('profile_image', $user->profileImage())
                        @set('default_image', App\User::$default_profile_image)
                        <h2 class="form-title">Profile Image</h2>

                        <div
                            class="image-display"
                            style="background-image: url('{{ $profile_image !== null ? $profile_image : $default_image }}')"
                            data-default="{{ $default_image }}">
                        </div>

                        <div class="image-buttons">
                            <input id="profile-image-upload" name="profile-image-upload" type="file" />
                            <label for="profile-image-upload" class="image-upload-button" title="Upload Profile Image"><i class="fas fa-upload"></i></label>
                            <button id="profile-image-delete" class="image-delete-button {{ $profile_image === null ? 'inactive' : '' }}" title="Delete Profile Image"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </form>

                    <form id="user-profile-update" class="edit-item user-profile-update">
                        <h2 class="form-title">User Profile <i class="fas fa-upload"></i></h2>

                        <label for="email">Email:</label>
                        <input class="text-input" type="text" name="email" id="email" value="{{ $user->email }}" disabled />

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <label for="name">Name:</label>
                                <input class="text-input" type="text" name="name" id="name" value="{{ $user->name }}" />
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="website">Website:</label>
                                <input class="text-input" type="text" name="website" id="website" value="{{ $user->website }}" />
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="facebook">Facebook URL:</label>
                                <input class="text-input" type="text" name="facebook" id="facebook" value="{{ $user->facebook }}" />
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="soundcloud">SoundCloud URL:</label>
                                <input class="text-input" type="text" name="soundcloud" id="soundcloud" value="{{ $user->soundcloud }}" />
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="instagram">Instagram Handle:</label>
                                <input class="text-input" type="text" name="instagram" id="instagram" value="{{ $user->instagram }}" />
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="twitter">Twitter Handle:</label>
                                <input class="text-input" type="text" name="twitter" id="twitter" value="{{ $user->twitter }}" />
                            </div>
                        </div>

                        <button type="button" class="submit-button btn btn-primary no-input">Update User Profile</button>
                    </form>

                    <form id="user-password-update" class="edit-item user-password-update">
                        <h2 class="form-title">Update Password</h2>
                        <input class="text-input" type="password" name="oldpass" id="oldpass" placeholder="Old Password" value="" />
                        <input class="text-input" type="password" name="newpass" id="newpass" placeholder="New Password" value="" />
                        <input class="text-input" type="password" name="newpass_confirmation" id="newpass_confirmation" placeholder="Repeat New Password" value="" />
                        <button type="button" class="submit-button btn btn-primary no-input">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
