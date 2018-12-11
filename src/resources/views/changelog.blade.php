@foreach($changes as $type => $typeChanges)
## {{strtoupper($type)}}
@foreach($typeChanges as $change)
{{ $change }}
@endforeach
@endforeach
