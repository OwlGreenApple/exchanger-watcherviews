@if(count($data) > 0)
    @foreach($data as $row)
    <div class="card card-white post mb-4">
        <div class="post-heading">
            <div class="float-left meta">
                <div class="title h5">
                    <b>{{ $row['buyer'] }}</b>
                </div>
                <div class="py-2">
                    @for($x= 0; $x < $row['rate']; $x++ )
                        <i class="fas fa-star gold"></i>
                    @endfor
                </div>
                <h6 class="text-muted time">{{ $row['created_at'] }}</h6>
            </div>
        </div> 
        <div class="post-description"> 
            <p>{{ $row['comments'] }}</p>
        </div>
    </div>
    @endforeach
@endif