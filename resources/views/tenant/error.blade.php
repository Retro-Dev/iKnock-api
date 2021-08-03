
@if($error)
    <div class="alert alert-danger">
        <ul>
            @foreach ($error['data'][0] as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif 
<div style="display: none;" class="alert alert-danger error"></div>
<div style="display: none;" class="alert alert-success success"></div>


