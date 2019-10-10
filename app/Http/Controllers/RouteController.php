<?php

namespace App\Http\Controllers;

use App\User;
use App\Apply;
use App\Group;
use App\WalkRoute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    /**
     * 查询申请者列表
     * @param Request $request
     * @return JsonResponse
     */
    public function getRouteList(Request $request)
    {
        return StandardSuccessJsonResponse(WalkRoute::orderBy('id', 'asc'));
    }


}
