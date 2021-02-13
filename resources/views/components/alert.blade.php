@if(session('message'))
    <div class="col-12 mt-3">
        <div class="alert alert-{{ session('message')[0] }}">
            {!! session('message')[1] !!}
        </div>
    </div>
@endif
