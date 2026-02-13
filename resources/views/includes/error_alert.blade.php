@isset($errors)
    @if($errors->any())
        <div class="alert alert-danger fade show">
            <span class="close" data-dismiss="alert">×</span>
            <strong>Oppss!</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endisset
