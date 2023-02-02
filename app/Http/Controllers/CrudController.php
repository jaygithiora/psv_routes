<?php

namespace App\Http\Controllers;

use App\Models\MyRoute;
use App\Models\Stage;
use App\Models\RouteStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CrudController extends Controller
{
    public function getFirstRoute(){
        $routes = MyRoute::with(['route_stages.stage'])->skip(0)->take(1)
        ->orderBy('name', 'ASC')->get();
        return response()->json(['routes' => $routes]);
    }
    public function getRoutes(Request $request){
        $page = $request->has('page') ? intval($request->page) : 1;
        $offset = ($page-1) * 20;
        $routes = MyRoute::where('name', 'LIKE', '%'.$request->search.'%')
        ->with(['route_stages.stage'])->skip($offset)->take(20)
        ->orderBy('name', 'ASC')->get();
        return response()->json(['routes' => $routes]);
    }

    public function addRoute(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:0',
            'name' => 'required|string|max:255|unique:my_routes,name,' . $request->id,
            "description" => "string|nullable",
            "distance"=>"numeric|nullable"
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
        $route->distance = $request->distance;
        if($route->save()){
            return response()->json(['success' => "Route updated successfully"]);
        }else{
            return response()->json(['error' => 'Unable to update route!'], 401);
        }
    }
    public function getStages(Request $request){
        $page = $request->has('page') ? intval($request->page) : 1;
        $offset = ($page-1) * 20;
        $stages = Stage::where('name', 'LIKE', '%'.$request->search."%")->skip($offset)->take(20)->orderBy('name', 'ASC')->get();
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
    public function getRouteStages(Request $request){
        $page = $request->has('page') ? intval($request->page) : 1;
        $offset = ($page-1) * 20;
        $routeStages = RouteStage::with(['stage'])->where('my_route_id', $request->id)
        ->whereHas('stage', function ($query) use ($request){
            $query->where('name', 'like', '%'.$request->search.'%');
        })->skip($offset)->take(20)->get();
        return response()->json(['route_stages' => $routeStages]);
    }
    public function addRouteStages(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1',
            'stage_ids'=>'required|string',
        ]);
        if($validator->fails()){
            return response()->json(['errors' => $validator->messages()], 400);
        }
        $stage_ids = explode(',',str_replace(']','',str_replace('[','',$request->stage_ids)));
        foreach($stage_ids as $stage_id){
            if(RouteStage::where('stage_id', $stage_id)->where('my_route_id', $request->id)->count() == 0){
                $routeStage = new RouteStage;
                $routeStage->my_route_id = $request->id;
                $routeStage->stage_id = $stage_id;
                $routeStage->save();
            }
        }
        return response()->json(['success' => "Route Stage(s) updated successfully"]);
    }

    public function addTerminus(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1',
            'stage_id'=>'required|integer|min:1',
            'status'=>'required|integer|min:0|max:2',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }
        RouteStage::where('my_route_id', $request->id)->where('status', $request->status)->update(["status"=>0]);
        RouteStage::where('my_route_id', $request->id)->where('stage_id', $request->stage_id)
            ->update(['status' => $request->status]);
        return response()->json(['success' => "Terminus updated successfully"]);
    }
}
