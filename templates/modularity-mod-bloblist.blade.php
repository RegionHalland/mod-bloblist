@if(isset($lists) && !empty($lists))
	@foreach ($lists as $list)	
	<div class="mb3">
		<h3>{{ $list['title'] }} ({{ $list['count'] }})</h3>
		<ul>
			@foreach ($list['blobs'] as $blob)
			<li><a href="{{ $blob }}">{{ $blob }}</a></li>
			@endforeach
		</ul>
	</div>
	@endforeach
@endif