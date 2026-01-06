<div {!! $htmlAttributes !!}>
    <div class="user-cover">
        @if($account?->customer)
            <div class="info-label" data-toggle="tooltip" data-html="true" title="Customer: <b>{{ $companyName }}</b>">
                <svg xmlns="http://www.w3.org/2000/svg" height="12" width="9" class="mr-1" viewBox="0 0 384 512">
                    <path fill="#9da9b9"
                          d="M48 0C21.5 0 0 21.5 0 48L0 464c0 26.5 21.5 48 48 48l96 0 0-80c0-26.5 21.5-48 48-48s48 21.5 48 48l0 80 96 0c26.5 0 48-21.5 48-48l0-416c0-26.5-21.5-48-48-48L48 0zM64 240c0-8.8 7.2-16 16-16l32 0c8.8 0 16 7.2 16 16l0 32c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-32zm112-16l32 0c8.8 0 16 7.2 16 16l0 32c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-32c0-8.8 7.2-16 16-16zm80 16c0-8.8 7.2-16 16-16l32 0c8.8 0 16 7.2 16 16l0 32c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-32zM80 96l32 0c8.8 0 16 7.2 16 16l0 32c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-32c0-8.8 7.2-16 16-16zm80 16c0-8.8 7.2-16 16-16l32 0c8.8 0 16 7.2 16 16l0 32c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-32zM272 96l32 0c8.8 0 16 7.2 16 16l0 32c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-32c0-8.8 7.2-16 16-16z" />
                </svg>
                {{ $companyName }}
            </div>
        @endif
    </div>
    <div class="user-info pb-3">
        <div class="user-avatar">
            <a class="edit-avatar" href="#" data-toggle="modal" data-target="#profileImageUpdate"></a>
            <img
                src="{{$accountProfileImage }}"
                alt="{{ $accountName }}">
        </div>
        <div class="user-data text-truncate">
            <h6 data-toggle="tooltip" data-html="true" title="Account: <b>{{ $accountName }}</b>">{{ $accountName }}</h6>
            @if(strlen($companyCode) > 0)
                <span class="text-muted text-sm text-truncate" data-toggle="tooltip"
                      data-html="true" title="Customer Code: <b>{{ $companyCode }}</b>">
               {{ $companyCode }}
            </span>
            @endif
        </div>
    </div>
</div>


@pushonce('html-default')
    <div class="modal fade" id="profileImageUpdate" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('frontend.profile.photo-update') }}" method="post" enctype='multipart/form-data'>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Profile Photo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center p-2 mb-4">
                            <img id="profile_image_preview" src="{{$accountProfileImage}}"
                                 alt="{{ $accountName }}"
                                 style="height: 128px !important; width: 128px !important; border-radius:50%; object-fit: cover;">
                        </div>
                        <div class="form-group">
                            <div class="custom-file">
                                <input type="file"
                                       name="profile_image"
                                       accept="image/*"
                                       onchange="loadFile(event, 'profile_image_preview');"
                                       id="profile_image"
                                       class="custom-file-input form-control-plaintext">
                                <label class="custom-file-label" for="profile_image" id="profile_image_preview_label">Select profile image</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary ml-0" data-dismiss="modal"><i class='icon-cross mr-1'></i>Close</button>
                        <button type="submit" class="btn btn-primary"><i class='icon-upload mr-1'></i>Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpushonce
