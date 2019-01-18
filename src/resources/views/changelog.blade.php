# ChangeLog
@foreach($releases as $release => $releaseInfo)

@if($release == 'unreleased')
## [Unreleased]
@else
## [{{strtoupper($release)}}] - {{$releaseInfo['date']}}
@endif
@foreach($releaseInfo['changes'] as $type => $typeChanges)
### {{strtoupper($type)}}
@foreach($typeChanges as $change)
{{ $change }}
@endforeach
@endforeach
@endforeach
