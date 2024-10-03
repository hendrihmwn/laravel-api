<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Controllers\BaseController as BaseController;
use Validator;
use Illuminate\Support\Facades\DB;

class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $limit = 10;
        $page = 0;
        if(isset($_GET['limit'])){
            $limit = $_GET['limit'];
        }
        if(isset($_GET['page'])){
            $page = $_GET['page'];
        }
        $products = Product::skip($page)->take($limit)->get();
        return $this->sendResponse($products, 'Products retrieved successfully.', 200, [
            'limit' => $limit,
            'page' => $page,
            'total' => count($products)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function create(Request $request)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required|string',
            'sku' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }
        
        DB::beginTransaction();
        try{
            $product = Product::create($input);
            DB::commit();

        }catch(\Exception $ex){
            DB::rollback();
            \Log::error('ProductController:create :: '.$ex);
            return $this->sendError('Internal server error',[],500);
        }
   
        return $this->sendResponse($product, 'Product created successfully.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function view($id)
    {
        $product = Product::find($id);
  
        if (is_null($product)) {
            return $this->sendError('Product not found.',[],404);
        }
   
        return $this->sendResponse($product, 'Product retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required|string',
            'sku' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        DB::beginTransaction();
        try{
            $product = Product::find($id);
  
            if (is_null($product)) {
                return $this->sendError('Product not found.',[],404);
            }
    
            $product->name = $input['name'];
            $product->sku = $input['sku'];
            $product->description = $input['description'];
            $product->price = $input['price'];
            $product->save();
            DB::commit();

        }catch(\Exception $ex){
            DB::rollback();
            \Log::error('ProductController:update :: '.$ex);
            return $this->sendError('Internal server error',[],500);
        }

        return $this->sendResponse($product, 'Product updated successfully.', 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try{
            $product = Product::find($id);
            if (is_null($product)) {
                return $this->sendError('Product not found.',[],404);
            }
            $product->delete();
            DB::commit();

        }catch(\Exception $ex){
            DB::rollback();
            \Log::error('ProductController:destroy :: '.$ex);
            return $this->sendError('Internal server error',[],500);
        }
        
        return $this->sendResponse([], 'Product deleted successfully.', 204);
    }
}
