@extends('dashboard.core', [
    'heading' => 'Settings'
])

@section('dashboard-body')
    <input type="hidden" id="token" value="{{ csrf_token() }}" />

    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-8">
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
