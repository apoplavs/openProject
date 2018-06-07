	{{--likes--}}
	@if(Auth::check())
		<label class="float-left ml-2 likes">
			<span>{{ $judge->likes }}</span>
			<i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
		</label>
		@else
	<a href="{{ route('login') }}">
		<label class="float-left ml-2 likes">
			<span>{{ $judge->likes }}</span>
			<i class="fa fa-thumbs-o-up text-muted" aria-hidden="true"></i>
		</label>
	</a>
	@endif
	
	{{--unlikes--}}
	@if(Auth::check())
		<label class="float-right mr-2 unlikes">
			<i class="fa fa-thumbs-o-down" aria-hidden="true"></i> <span>{{ $judge->unlikes }}</span>
		</label>
	@else
		<a href="{{ route('login') }}">
			<label class="float-right mr-2 unlikes">
				<i class="fa fa-thumbs-o-down text-muted" aria-hidden="true"></i> <span>{{ $judge->unlikes }}</span>
			</label>
		</a>
	@endif
	

