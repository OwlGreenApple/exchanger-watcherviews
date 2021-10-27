@if(count($data) > 0)
    @foreach($data as $row)
    <div class="card card-white post mb-4">
        <div class="post-heading">
            <div class="float-left meta">
                <div class="title h5">
                    <b>{{ $row['user'] }}</b>
                </div>
                <div class="title">{{ $row['no_trans'] }}</div>
                <div class="py-2">
                    @if($row['rate'] > 0)
                        @for($x=1;$x<=$row['rate'];$x++)
                          <i class="fas fa-star gold"></i>
                          @if($x == $row['rate'] && $row['rate'] < 5)
                            @if($row['star_float'] == 0)
                              <i class="fas fa-star gold"></i>
                            @else
                              <i class="fas fa-star-half"></i>
                            @endif
                          @endif
                        @endfor
                    @endif
                </div>
                <h6 class="text-muted time">{{ $row['created_at'] }}</h6>
            </div>
        </div> 
        <div class="post-description mt-1"> 
            <p>{{ $row['comments'] }}</p>
        </div>
    </div>
    @endforeach
@endif