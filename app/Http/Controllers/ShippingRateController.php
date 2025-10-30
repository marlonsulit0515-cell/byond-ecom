<?php

namespace App\Http\Controllers;

use App\Models\ShippingPrice;
use App\Models\ShippingQuantityRate;
use Illuminate\Http\Request;

class ShippingRateController extends Controller
{
    public function shipping_settings()
    {
        $shippingRates = ShippingPrice::all();
        $fixedRates = ShippingQuantityRate::orderBy('quantity_from')->get(); 
        return view('AdminPanel.products.shipping-setting', compact('shippingRates', 'fixedRates'));
    }

    public function updateShipPrice(Request $request, $id)
    {
        $shippingRate = ShippingPrice::findOrFail($id);
        $shippingRate->update($request->only('province', 'price', 'is_active'));

        return response()->json(['success' => true, 'message' => 'Shipping rate updated successfully!']);
    }

    public function deleteShipPrice($id)
    {
        ShippingPrice::destroy($id);

        return response()->json(['success' => true, 'message' => 'Shipping rate deleted successfully!']);
    }

    // ADD THESE NEW METHODS FOR FIXED RATES
    public function addFixedRate(Request $request)
    {
        $request->validate([
            'quantity_from' => 'required|integer|min:1',
            'quantity_to' => 'required|integer|min:1',
            'fixed_price' => 'required|numeric|min:0',
        ]);

        ShippingQuantityRate::create([
            'quantity_from' => $request->quantity_from,
            'quantity_to' => $request->quantity_to,
            'fixed_price' => $request->fixed_price,
            'is_active' => true,
        ]);

        return response()->json(['success' => true, 'message' => 'Fixed rate added successfully!']);
    }

    public function updateFixedRate(Request $request, $id)
    {
        $fixedRate = ShippingQuantityRate::findOrFail($id);
        $fixedRate->update($request->only('quantity_from', 'quantity_to', 'fixed_price', 'is_active'));

        return response()->json(['success' => true, 'message' => 'Fixed rate updated successfully!']);
    }

    public function deleteFixedRate($id)
    {
        ShippingQuantityRate::destroy($id);

        return response()->json(['success' => true, 'message' => 'Fixed rate deleted successfully!']);
    }
}
