@extends('stores.crowdox.order-lines.layout', ["title" => "Index"])

@section('page')


<div class="card">
    <form action="{{ route('stores.crowdox.orderlines.import') }}" method="post">
        @csrf
        Order Line ID to import (blank for all)
        <input type="number" name="crowd_ox_order_line_id" id="crowd_ox_order_line_id"/>
        <button type="submit" class="btn btn-warning">Import CrowdOx Order Lines</button>
    </form>
    <form action="{{ route('stores.crowdox.orderlines.import.dispatch') }}" method="post">
        @csrf
        Page to start fetching:
        <input type="number" name="page_start" id="page_start" value="1">
        <button type="submit" class="btn btn-warning">Dispatch CrowdOx Order Lines Importer Job</button>
    </form>

</div>

@include('components.data-table', [
    "headers" => ["Crowdox ID", "Project", "Order", "Product Bundle", "Line Type", "Product Price", "Shipping Price", "Total Price"],
    "data" => $crowdox_order_lines,
    "keys" => ["crowd_ox_id", "crowd_ox_project_id", "crowd_ox_order_id", "crowd_ox_product_id", "crowd_ox_line_type", "crowd_ox_price_product", "crowd_ox_price_shipping", "crowd_ox_price_total"],
    "routes" => ["route" => "stores.crowdox.orderlines"],
    "name" => "CrowdOx Order Lines"
])



@endsection
