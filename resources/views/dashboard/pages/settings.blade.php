@extends('dashboard.core', [
    'heading' => 'Settings'
])

@section('dashboard-body')
    <input type="hidden" id="token" value="{{ csrf_token() }}" />

    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-8">
                <form id="user-profile-image" class="edit-item user-profile-image">
                    @set('profile_image', $user->profileImage())
                    @set('default_image', App\User::$default_profile_image)

                    <div
                        class="image-display"
                        style="background-image: url('{{ $profile_image !== null ? $profile_image : $default_image }}')"
                        data-default="{{ $default_image }}">
                    </div>

                    <div class="image-buttons">
                        <input id="profile-image-upload" name="profile-image-upload" type="file" />
                        <label for="profile-image-upload" class="image-upload-button" title="Upload Profile Image"></label>
                        <span id="profile-image-delete" class="image-delete-button {{ $profile_image === null ? 'inactive' : '' }}" title="Delete Profile Image"></span>
                    </div>
                </form>
            </div>

            <div class="col-12 col-md-4">
                <form id="user-password" class="edit-item">
                    <input class="text-input" type="password" name="oldpass" id="oldpass" placeholder="Old Password" value="" />
                    <input class="text-input" type="password" name="newpass" id="newpass" placeholder="New Password" value="" />
                    <input class="text-input" type="password" name="newpass_confirmation" id="newpass_confirmation" placeholder="Repeat New Password" value="" />
                    <button type="button" class="submit-button no-horizontal-margins btn btn-primary no-input">Update Password</button>
                </form>
            </div>
        </div>
    </div>
@endsection
