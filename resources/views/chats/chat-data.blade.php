@if(count($data) > 0)
    @foreach($data as $row)
        <div class="card card-white post mb-4">
            <div class="post-heading" style="height:auto; padding-bottom:5px">
                <div class="float-left meta">
                    <div class="title h5">
                        <b class="@if($row->role == 1) text-primary @elseif($row->role == 2) text-success @else text-black-50 @endif">{{ $row->name }}</b>
                    </div>
                  
                    <h6 class="text-muted time">{{ $row->created_at }}</h6>
                </div>
            </div> 
            <div class="post-description"> 
                <p>{{ $row->comments }}</p>
            </div>
        </div>
    @endforeach
@endif