@if(isset($items))
	<h3>{!! apply_filters('the_title', $post_title) !!}</h3>
	<ul>
		@foreach ($items as $item)
			<li><a href="{{ $item['url'] }}">{{ $item['title'] }}</a></li>
		@endforeach
	</ul>
@endif