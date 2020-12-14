<?php

namespace Selene\Modules\AquaParkModule\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Selene\Modules\AquaParkModule\Models\AquaPark;
use Selene\Modules\AquaParkModule\Models\AquaParkPrice;
use Selene\Modules\AquaParkModule\Models\AquaParkPricePass;

class ApiController extends Controller
{
    public function get(Request $request): JsonResponse
    {
        $aquaParks = AquaPark::query()->orderBy('order');
        if ($request->has('id')) {
            $aquaParks->where('_id', '=', $request->get('id'));
            return response()->json($aquaParks->first());
        }

        if ($request->has('per_page')) {
            return response()->json(
                $aquaParks->paginate(
                    $request->get('per_page') >> 0,
                    ['*'],
                    'page',
                    $request->get('page', 1)
                )
            );
        }

        return response()->json($aquaParks->get());
    }

    public function prices(Request $request): JsonResponse
    {
        $prices = AquaParkPrice::query()->orderBy('order');

        if ($request->has('id')) {
            $prices->where('_id', '=', $request->get('id'));
            return response()->json($prices->first());
        }
        if ($request->has('aquaPark')) {
            $prices->where('aqua_park', '=', $request->get('aquaPark'));
        }

        if ($request->has('per_page')) {
            return response()->json(
                $prices->paginate(
                    $request->get('per_page') >> 0,
                    ['*'],
                    'page',
                    $request->get('page', 1)
                )
            );
        }

        return response()->json($prices->get());
    }

    public function passes(Request $request): JsonResponse
    {
        $passes = AquaParkPricePass::query()->orderBy('order');

        if ($request->has('id')) {
            $passes->where('_id', '=', $request->get('id'));
            return response()->json($passes->first());
        }
        if ($request->has('price')) {
            $passes->where('aqua_park_price', '=', $request->get('price'));
        }

        if ($request->has('per_page')) {
            return response()->json(
                $passes->paginate(
                    $request->get('per_page') >> 0,
                    ['*'],
                    'page',
                    $request->get('page', 1)
                )
            );
        }

        return response()->json($passes->get());
    }
}
