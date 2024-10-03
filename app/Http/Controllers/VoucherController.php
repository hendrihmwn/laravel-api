<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreVoucherRequest;
use App\Http\Requests\UpdateVoucherRequest;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Support\Facades\DB;
use Validator;

class VoucherController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vouchers = Voucher::all();
        return $this->sendResponse($vouchers, 'Vouchers retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function create(Request $request)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required|string',
            'code' => 'required|string',
            'disc_amount' => 'numeric',
            'disc_percentage' => 'numeric',
            'expired_at' => 'required|date_format:Y-m-d H:i:s|after_or_equal:' . date(DATE_ATOM),
            'active_at' => 'date_format:Y-m-d H:i:s|after_or_equal:' . date(DATE_ATOM),
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }

        $input['status'] = 'IN_SCHEDULE';
        if(!isset($input['active_at'])){
            $input['active_at'] = date('Y-m-d H:i:s');
            $input['status'] = 'ACTIVE';
        }
        
        DB::beginTransaction();
        try{
            $voucher = Voucher::create($input);
            DB::commit();

        }catch(\Exception $ex){
            DB::rollback();
            \Log::error('VoucherController:create :: '.$ex);
            return $this->sendError('Internal server error',[],500);
        }
   
        return $this->sendResponse($voucher, 'Voucher created successfully.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function view($id)
    {
        $voucher = Voucher::find($id);
  
        if (is_null($voucher)) {
            return $this->sendError('Voucher not found.',[],404);
        }
   
        return $this->sendResponse($voucher, 'Voucher retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required|string',
            'code' => 'required|string',
            'disc_amount' => 'numeric',
            'disc_percentage' => 'numeric',
            'expired_at' => 'required|date_format:Y-m-d H:i:s|after_or_equal:' . date(DATE_ATOM),
            'active_at' => 'date_format:Y-m-d H:i:s|after_or_equal:' . date(DATE_ATOM),
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $input['status'] = 'IN_SCHEDULE';
        if(!isset($input['active_at'])){
            $input['active_at'] = date('Y-m-d H:i:s');
            $input['status'] = 'ACTIVE';
        }

        DB::beginTransaction();
        try{
            $voucher = Voucher::find($id);
            if (is_null($voucher)) {
                return $this->sendError('Voucher not found.',[],404);
            }
       
            $voucher->name = $input['name'];
            $voucher->code = $input['code'];
            $voucher->disc_amount = isset($input['disc_amount']) ? $input['disc_amount'] : null;
            $voucher->disc_percentage = isset($input['disc_percentage']) ? $input['disc_percentage'] : null;
            $voucher->expired_at = $input['expired_at'];
            $voucher->save();
    
            DB::commit();

        }catch(\Exception $ex){
            DB::rollback();
            \Log::error('VoucherController:update :: '.$ex);
            return $this->sendError('Internal server error',[],500);
        }
  
        return $this->sendResponse($voucher, 'Voucher updated successfully.', 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try{
            $voucher = Voucher::find($id);
            if (is_null($voucher)) {
                return $this->sendError('Voucher not found.',[],404);
            }
            $voucher->delete();
            DB::commit();

        }catch(\Exception $ex){
            DB::rollback();
            \Log::error('VoucherController:destroy :: '.$ex);
            return $this->sendError('Internal server error',[],500);
        }
        
        return $this->sendResponse([], 'Voucher deleted successfully.', 204);
    }

    /**
     * Function for apply voucher
     */
    public function apply(Request $request)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'voucher_code' => 'required|string',
            'product_id' => 'required|numeric'
        ]);

        DB::beginTransaction();
        try{
            $voucher = Voucher::where('code', $input['voucher_code'])
                        ->whereDate('expired_at','>=',date('Y-m-d H:i:s'))
                        ->where('status','ACTIVE')
                        ->first();
  
            if (is_null($voucher)) {
                return $this->sendError('Voucher is not valid.',[],404);
            }

            $voucher_usage = VoucherUsage::where('product_id', $input['product_id'])
                        ->where('voucher_id',$voucher->id)
                        ->where('user_id',auth()->guard('api')->user()->id)
                        ->first();
            
            if (!is_null($voucher_usage)) {
                return $this->sendError('Voucher is already applied.',[],422);
            }

            VoucherUsage::create([
                'product_id'=> $input['product_id'],
                'voucher_id'=> $voucher->id,
                'user_id'=> auth()->guard('api')->user()->id,
            ]);

            DB::commit();

        }catch(\Exception $ex){
            DB::rollback();
            \Log::error('VoucherController:apply :: '.$ex);
            return $this->sendError('Internal server error',[],500);
        }

        return $this->sendResponse([], 'Voucher applied successfully.', 200);
    }

    /**
     * Function for apply voucher
     */
    public function remove_apply_voucher(Request $request)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'voucher_code' => 'required|string',
            'product_id' => 'required|numeric'
        ]);

        DB::beginTransaction();
        try{
            $voucher = Voucher::where('code', $input['voucher_code'])
                        ->whereDate('expired_at','>=',date('Y-m-d H:i:s'))
                        ->where('status','ACTIVE')
                        ->first();
  
            if (is_null($voucher)) {
                return $this->sendError('Voucher is not valid.',[],404);
            }
            
            $voucher_usage = VoucherUsage::where('product_id', $input['product_id'])
                        ->where('voucher_id',$voucher->id)
                        ->where('user_id',auth()->guard('api')->user()->id)
                        ->delete();

            DB::commit();

        }catch(\Exception $ex){
            DB::rollback();
            \Log::error('VoucherController:remove_apply_voucher :: '.$ex);
            return $this->sendError('Internal server error',[],500);
        }

        return $this->sendResponse([], 'Remove applied voucher successfully.', 200);
    }
}
