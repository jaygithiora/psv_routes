<?php

namespace App\Http\Controllers;

use App\Models\MyRoute;
use App\Models\Stage;
use App\Models\RouteStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CrudController extends Controller
{
    public function getRoutes(Request $request){
        $page = $request->has('page') ? intval($request->page) : 1;
        $offset = ($page-1) * 20;
        $routes = MyRoute::skip($offset)->take(20)->get();
        return response()->json(['routes' => $routes]);
    }

    public function addRoute(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:0',
            'name' => 'required|string|max:255|unique:my_routes,name,' . $request->id,
            "description" => "string|nullable"
        ]);
        if($validator->fails()){
            return response()->json(['errors' => $validator->messages()], 400);
        }
        $route = new MyRoute;
        if($request->id > 0){
            $route = MyRoute::findOrFail($request->id);
            if($route == null){
                return response()->json(['error' => 'Route ID is invalid'], 401);
            }
        }
        $route->name = $request->name;
        $route->description = $request->description;
        if($route->save()){
            return response()->json(['success' => "Route updated successfully"]);
        }else{
            return response()->json(['error' => 'Unable to update route!'], 401);
        }
    }
    public function getRouteStages(Request $request){
        $page = $request->has('page') ? intval($request->page) : 1;
        $offset = ($page-1) * 20;
        $routeStages = RouteStage::skip($offset)->take(20)->get();
        return response()->json(['route_stages' => $routeStages]);
    }
    public function getStages(Request $request){
        $page = $request->has('page') ? intval($request->page) : 1;
        $offset = ($page-1) * 20;
        $stages = Stage::skip($offset)->take(20)->get();
        return response()->json(['stages' => $stages]);
    }
    public function addStage(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:0',
            'name' => 'required|string|max:255|unique:stages,name,' . $request->id,
            'longitude'=>'required|numeric',
            'latitude'=>'required|numeric',
        ]);
        if($validator->fails()){
            return response()->json(['errors' => $validator->messages()], 400);
        }
        $stage = new Stage;
        if($request->id > 0){
            $stage = Stage::findOrFail($request->id);
            if($stage == null){
                return response()->json(['error' => 'Stage ID is invalid'], 401);
            }
        }
        $stage->name = $request->name;
        $stage->longitude= $request->longitude;
        $stage->latitude= $request->latitude;
        if($stage->save()){
            return response()->json(['success' => "Stage updated successfully"]);
        }else{
            return response()->json(['error' => 'Unable to update stage!'], 401);
        }
    }
}
