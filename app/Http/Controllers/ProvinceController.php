<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Province;

class ProvinceController extends Controller
{
    public function index()
    {
        $data = Province::OrderBy('value', 'ASC')->paginate(2)->toArray();
        $response = [
            'success' => true,
            'message' => 'Get data province',
            'data' => $data['data'],
            'pagination' => $data['links'],
        ];
        return response()->json($response, 200);
    }

    public function show($id)
    {
        $data = Province::find($id);
        if ($data) {
            $response = [
                'success' => true,
                'message' => 'Get detail province',
                'data' => $data,
            ];
        }else{
            $response = [
                'success' => false,
                'message' => 'ID not found.',
            ];
        }
        
        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|unique:province',
            'value' => 'required|unique:province',
            'file' => 'required|image|mimes:jpg,png,jpeg',
        ]);

        $check = Province::where(['key'=>$request->key, 'value'=>$request->value])->first();
        if($validator->errors()->count() > 0){
            $response = [
                'success' => false,
                'message' => 'Errors.',
                'errors' => $validator->errors()
            ]; $code = 200;
        }else{
            $name = Str::random(20).'.'.$request->file->extension();  
            
            $insert = Province::create([
                'key' => $request->key,
                'value' => $request->value,
                'file' => $name,
            ]);

            if ($insert) {
                $request->file->move(public_path('images'), $name);
                $response = [
                    'success' => true,
                    'message' => 'Created.',
                    'data' => $insert
                ]; $code = 201;
            }
            
        }
        return response()->json($response, $code);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'value' => 'required',
            'file' => 'image|mimes:jpg,png,jpeg',
        ]);

        if($validator->errors()->count() > 0){
            $response = [
                'success' => false,
                'message' => 'Errors',
                'errors' => $validator->errors()
            ];
        }else{
            $data = Province::find($id);
            if ($data) {
                if ($request->file != '') {
                    unlink(public_path('images/'.$data['file']));
                    $name = Str::random(20).'.'.$request->file->extension(); 
                    $request->file->move(public_path('images'), $name);
                }else{
                    $name = $data['file'];
                }
                $update = Province::where('id', $id)->update([
                    'key' => $request->key,
                    'value' => $request->value,
                    'file' => $name,
                ]);
                $response = [
                    'success' => true,
                    'message' => 'Updated.',
                    'data' => $data
                ];
            }else{
                $response = [
                    'success' => false,
                    'message' => 'ID not found.',
                ];
            }
        }

        return response()->json($response, 200);
    }

    public function destroy($id)
    {
        $data = Province::find($id);
        if ($data) {
            unlink(public_path('images/'.$data['file']));
            $data->delete();
            $response = [
                'success' => true,
                'message' => 'Deleted',
                'data' => $data
            ];
        }else{
            $response = [
                'success' => false,
                'message' => 'ID not found.',
            ];
        }
        return response()->json($response, 200);
    }

    public function search($value)
    {
       $data = Province::where('value','like','%'.$value.'%')->paginate(2)->toArray();
       if ($data['data']) {
            $response = [
                'success' => true,
                'message' => 'Search.',
                'data' => $data['data'],
                'pagination' => $data['links']  
            ];
        }else{
            $response = [
                'success' => false,
                'message' => 'Keyword not found.',
            ];
        }
        return response()->json($response, 200);
    }
}
