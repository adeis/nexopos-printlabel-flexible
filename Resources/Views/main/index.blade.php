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
		<form action="" name="form" id="form" target="_blank">


		<div class="row">
			<div class="col-md-2 col-sm-1">{{__('Page Size')}}</div>
			<div class="col-md-3 col-sm-3">
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
			<div class="col-md-7 col-sm-7">
					<label for="size_page_custom">
						<input class="form-control" type="checkbox" name="size_page_custom" id="size_page_custom">
						{{__('Custom')}}
					</label>
					<input type="text" name="custom_page_width" class="custom_size" id="custom_page_width" placeholder="{{__('width')}}"> 
					<span class="custom_size">x</span> 
					<input type="text" name="custom_page_height" class="custom_size" id="custom_page_height" placeholder="{{__('height')}}">
					<span class="custom_size">mm</span>
			</div>
		</div>
		<div class="row">
			<div class="col-md-2 col-sm-1">{{__('Orientation')}}</div>
			<div class="col-md-10 col-sm-11">
				<select name="orientation" id="orientation">
					<option value="P">{{__('Portrait')}}</option>
					<option value="L">{{__('Landscape')}}</option>
				</select>
			</div>
		</div>
		<div class="row">
			<div class="col-md-2 col-sm-1">{{__('Page Margin')}}</div>
			<div class="col-md-10 col-sm-11">
				<input type="text" class="number" placeholder="{{__('left')}}" name="page_margin_left" id="page_margin_left" value="1">
				<input type="text" class="number" placeholder="{{__('top')}}" name="page_margin_top" id="page_margin_top" value="1">
				<input type="text" class="number" placeholder="{{__('right')}}" name="page_margin_right" id="page_margin_right" value="1">
				<input type="text" class="number" placeholder="{{__('bottom')}}" name="page_margin_bottom" id="page_margin_bottom" value="1">
				mm
			</div>
		</div>
		<div class="row">
			<div class="col-md-2 col-sm-1">{{__('Products')}}</div>
			<div class="col-md-10 col-sm-5">
				<table class="t-product" style="width:100%; background-color:none">

				</table>
			</div>
		</div>
		<div class="row">
			<div class="col-md-2"></div>
			<div class="col-md-8 btn-clone">
					<button class="btn" id="add-products">+</button>
			</div>
		</div>
		<div class="row" style="display: none">
			<div class="col-md-2 col-sm-1">{{__('Barcode Total')}}</div>
			<div class="col-md-10 col-sm-11">
				<input type="number" name="total" id="total" value="84">
			</div>
		</div>
		<div class="row">
			<div class="col-md-2 col-sm-1">{{__('Barcode Size')}}</div>
			<div class="col-md-10 col-sm-11">
				<input type="text" class="number" name="barcode_width" id="barcode_width" value="32"> x
				<input type="text" class="number" name="barcode_height" id="barcode_height" value="26.5">
				mm
			</div>
		</div>
		<div class="row">
			<div class="col-md-2 col-sm-1">{{__('Barcode Margin')}}</div>
			<div class="col-md-10 col-sm-11">
				<input type="number" name="barcode_margin" id="barcode_margin" value="2"> mm
			</div>
		</div>
		<div class="row">
			<div class="col-md-2 col-sm-1">{{__('Column Total')}}</div>
			<div class="col-md-10 col-sm-11">
				<input type="number" name="column" id="column" value="6">
			</div>
		</div>
		<div class="row">
			<div class="col-md-2 col-sm-1">{{__('Barcode View')}}</div>
			<div class="col-md-10 col-sm-11">
				<select name="barcode_view[]" id="barcode_view" class="select2" style="width: 100%" multiple>
					<option value="code" selected>{{__('Code')}}</option>
					<option value="name" selected>{{__('Name')}}</option>
					<option value="price" selected>{{__('Price')}}</option>
					<option value="store_name">{{__('Store Name')}}</option>
				</select>
			</div>
		</div>
		<div class="row">
			<div class="col-md-2">
				<button id="btn-preview" name="btn" value="preview" class="btn btn-primary">{{__('Preview')}}</button>
				<button id="btn-print" type="submit" name="btn" value="print" class="btn btn-primary">{{__('Print')}}</button>
			</div>
		</div>
	</div>
	</form>
</div>
</div>
<!-- Template untuk row produk -->
<template id="product-template">
    <tr class="row-product">
        <td>
            <input type="hidden" name="products[]" class="product-id">
            <span class="product-name"></span>
        </td>
        <td>
            <input type="number" required placeholder="{{__('Total')}}" class="number product-total" value="1" name="products_totals[]">
            <button class="btn remove-product" style="background-color: rgb(233, 174, 174)">x</button>
        </td>
    </tr>
</template>
<!-- modal -->

<div id="dialog">
	<iframe src="" id="modal-iframe" frameborder="0" width="100%" height="100%"></iframe>
</div>

<!-- end modal -->
<!-- Modal untuk memilih produk -->
<div id="productModal" title="{{__('Select a Product')}}">
    <select id="productSelect" class="select2" style="width: 100%">
        <option value="">{{ __('Select a product') }}</option>
        @foreach($products as $product)
        <option value="{{ $product->id }}">{{ $product->name }}</option>
        @endforeach
    </select>
    <button id="selectProductButton">{{ __('Add Product') }}</button>
</div>
@endsection
@section('layout.dashboard.header')
<script src="{{ asset('modules/barcodegenerator/jquery-1.7.1.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('modules/barcodegenerator/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('modules/barcodegenerator/mini-default.css') }}">
<link rel="stylesheet" href="{{ asset('modules/barcodegenerator/jquery-ui/jquery-ui.min.css') }}">
<link rel="stylesheet" href="{{ asset('modules/barcodegenerator/jquery-ui/jquery-ui.theme.css') }}">
<style>
  .select3 {
  box-sizing: border-box;
  background: var(--input-back-color);
  color: var(--input-fore-color);
  border: 0.0625rem solid var(--input-border-color);
  border-radius: var(--universal-border-radius);
  margin: calc(var(--universal-margin) / 2);
  padding: var(--universal-padding) calc(1.5 * var(--universal-padding));
}
.select2-container .select2-selection--single {
	height: 38px;
	margin-top:6px;
	margin-left: 4px;
}
.btn-clone {
    margin-top: -18px;
    margin-left: -4px;
}
table.t-product td {
	border: none;
	background:none;
	padding: 0px;
}
</style>
<script src="{{ asset('modules/barcodegenerator/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('modules/barcodegenerator/jquery-ui/jquery-ui.min.js') }}"></script>
<script>
	$(document).ready(function() { 
		$(".select2").select2(); 
		$("#page-size").select2(); 

		$(".custom_size").hide();
		$("#size_page_custom").on("change", function() {
			//if checkbox check 
			if($(this).is(":checked")) {
				$(".custom_size").show();
				$("#page_size").attr('disabled', false);
			}else{
				$(".custom_size").hide();
				$("page_size").removeAttr('disabled');
			}
			
		});

		// jQuery UI solved select2 on dialog
		if ($.ui && $.ui.dialog && $.ui.dialog.prototype._allowInteraction) {
			var ui_dialog_interaction = $.ui.dialog.prototype._allowInteraction;
			$.ui.dialog.prototype._allowInteraction = function(e) {
				if ($(e.target).closest('.select2-dropdown').length) return true;
				return ui_dialog_interaction.apply(this, arguments);
    		};
		}
		// Inisialisasi modal jQuery UI
        $("#productModal").dialog({
            autoOpen: false,
            modal: true,
            width: 400,
            height: 300
        });

        // Buka modal saat tombol diklik
        $('#add-products').click(function(e) {
            e.preventDefault();
            $('#productModal').dialog('open');
        });

        // Tambahkan produk ke tabel
        $('#selectProductButton').click(function() {
            var selectedProduct = $('#productSelect').find(":selected");
            var productId = selectedProduct.val();
            var productName = selectedProduct.text();

            if (productId) {
                var template = $('#product-template').html();
                var newRow = $(template);

                newRow.find('.product-id').val(productId);
                newRow.find('.product-id').attr('name', "products["+productId+"]");
				newRow.find('.product-total').attr('name', "products_totals["+productId+"]");
                newRow.find('.product-name').text(productName);

                $('.t-product').append(newRow);
                $('#productModal').dialog('close');
                $('#productSelect').val(null).trigger('change');
            }
        });

		 // Hapus produk dari tabel
		 $(document).on('click', '.remove-product', function() {
            $(this).closest('tr').remove();
        });

		// jika ada class number, maka hanya terima input [1-9] dan "."
		$(".number").keyup(function (e) {
			var regex = /^\d*\.?\d*$/;
			if (!regex.test(this.value)) {
				this.value = this.value.slice(0, -1);
			}
			/*
			if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
				return false;
			}
				*/
		});
		var preview = $("#btn-preview");
		preview.on("click", function() {
			// get form serialize
			var form = $("#form");
			var formSerialize = form.serialize();
			formSerialize += "&btn=preview";
			$('#modal-iframe').attr('src', "{{ route('bc.print-labels') }}?"+formSerialize);
			$("#dialog").dialog({
				title: "{{__('Preview')}}",
				height: $(window).height(),
				width: "90%",
				modal:true,
				draggable: false,
				buttons: [
					{
					text: "Ok",
					click: function() {
						$( this ).dialog( "close" );
					}
					}
				]
			});
			//return preventDefault();
			//window.open("{{ route('bc.print-labels') }}?"+formSerialize, "_blank");

			//cancel form submit
			return false;

		})
		var print = $("#btn-print");
	});
</script>
@endsection
