@extends( 'layout.dashboard' )

@section( 'layout.dashboard.body' )
<div>
	@include( Hook::filter( 'ns-dashboard-header-file', '../common/dashboard-header' ) )
    <div id="dashboard-content" class="px-4">
	<div class="page-inner-header mb-4"><h3 class="text-3xl text-primary font-bold">
		{{ $title }}</h3>
		<p class="text-secondary">{{ __('Bulk print label barcode')}}.</p>
	</div>
	<div id="print-barcode">
		<?php /*
		<!--
		<ns-print-label
			barcodeurl="{{ ns()->asset( 'storage/products/barcodes' ) }}"
			storename="{{ ns()->option->get( 'ns_store_name' ) }}">
		</ns-print-label>
		*/
		?>
		<form action="">


		<div class="row">
			<div class="col-md-2 col-sm-1">{{__('Page Size')}}</div>
			<div class="col-md-10 col-sm-11">
				<select id="page-size" name="page-size">
					@foreach( $commondPaperSizes as $group => $sizes )
						<optgroup label="{{ $group }}">
							@foreach( $sizes as $name => $size )
								@if(isset($size["mm"]))
								<option @if ($name == 'A4') selected @endif value="{{ implode('-', $size["mm"])}}">{{ $name }} ( {{ implode(' x ', $size["mm"])}} mm )</option>
								@endif
							@endforeach
						</optgroup>	
					@endforeach
				</select>
			</div>
		</div>
		<div class="row">
			<div class="col-md-2 col-sm-1">{{__('Products')}}</div>
			<div class="col-md-10 col-sm-11">
				<select name="products[]" id="products" required class="select2" multiple style="width: 100%">
					@foreach($products as $product)
					<option value="{{ $product->id }}">{{ $product->name }}</option>
					@endforeach
				</select>
			</div>
		</div>
		<div class="row">
			<div class="col-md-2 col-sm-1">{{__('Barcode Total')}}</div>
			<div class="col-md-10 col-sm-11">
				<input type="number" name="total" id="total" value="10">
			</div>
		</div>
		<div class="row">
			<div class="col-md-2 col-sm-1">{{__('Barcode Size')}}</div>
			<div class="col-md-10 col-sm-11">
				<input type="number" name="barcode_width" id="barcode_width" value="33"> x 
				<input type="number" name="barcode_height" id="barcode_height" value="15"> 
				mm
			</div>
		</div>
		<div class="row">
			<div class="col-md-2 col-sm-1">{{__('Barcode Margin')}}</div>
			<div class="col-md-10 col-sm-11">
				<input type="number" name="barcode_margin" id="barcode_margin" value="1"> mm
			</div>
		</div>
		<div class="row">
			<div class="col-md-2 col-sm-1">{{__('Column Total')}}</div>
			<div class="col-md-10 col-sm-11">
				<input type="number" name="column" id="column" value="3">
			</div>
		</div>
		<div class="row">
			<div class="col-md-2 col-sm-1">{{__('Barcode View')}}</div>
			<div class="col-md-10 col-sm-11">
				<select name="barcode_view[]" id="barcode_view" class="select2" style="width: 100%" multiple>
					<option value="code" selected>{{__('Code')}}</option>
					<option value="name">{{__('Name')}}</option>
					<option value="price">{{__('Price')}}</option>
					<option value="store_name">{{__('Store Name')}}</option>
				</select>
			</div>
		</div>
		<div class="row">
			<div class="col-md-2">
				<button id="btn-print" target="_blank" name="btn" value="preview" class="btn btn-primary">{{__('Preview')}}</button>
			</div>
		</div>
	</div>
	</form>
</div>
</div>
@endsection
@section('layout.dashboard.header')
<script src="{{ asset('modules/barcodegenerator/jquery-1.7.1.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('modules/barcodegenerator/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('modules/barcodegenerator/mini-default.css') }}">
<script src="{{ asset('modules/barcodegenerator/select2/js/select2.full.min.js') }}"></script>
<script>
	$(document).ready(function() { 
		$(".select2").select2(); 
		$("#page-size").select2(); 
	});
</script>
@endsection