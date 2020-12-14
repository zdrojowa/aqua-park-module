<?php

namespace Selene\Modules\AquaParkModule\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Selene\Modules\AquaParkModule\Models\AquaPark;
use Selene\Modules\AquaParkModule\Models\AquaParkPrice;
use Selene\Modules\DashboardModule\ZdrojowaTable;

class PriceController extends Controller
{
    public function index(AquaPark $aquaPark)
    {
        return view('AquaParkModule::prices.index', [
            'prices' => AquaParkPrice::query()
                ->where('aqua_park', '=', $aquaPark->_id)
                ->orderBy('order')
                ->get(),
            'aquaPark' => $aquaPark
        ]);
    }

    public function sort(AquaPark $aquaPark)
    {
        return view('AquaParkModule::prices.sort', [
            'aquaPark' => $aquaPark
        ]);
    }

    public function ajax(AquaPark $aquaPark, Request $request): JsonResponse
    {
        return ZdrojowaTable::response(
            AquaParkPrice::query()->where('aqua_park', '=', $aquaPark->_id),
            $request
        );
    }

    public function create(AquaPark $aquaPark)
    {
        return view('AquaParkModule::prices.edit', [
            'aquaPark' => $aquaPark
        ]);
    }

    public function edit(AquaParkPrice $price = null)
    {
        return view('AquaParkModule::prices.edit', [
            'price'    => $price,
            'aquaPark' => $price->getAquaPark()
        ]);
    }

    public function store(Request $request): array
    {
        $price = $this->save($request);
        if ($price) {
            $request->session()->flash('alert-success', 'Cena pomyślnie dodana');
            return ['redirect' => route('AquaParkModule::prices.edit', ['price' => $price])];
        }

        $request->session()->flash('alert-error', 'Ooops. Try again.');
        return ['redirect' => route('AquaParkModule::prices')];
    }

    public function update(Request $request, AquaParkPrice $price): array
    {
        if ($this->save($request, $price)) {
            $request->session()->flash('alert-success', 'Cena została pomyślnie zaktualizowana.');
        } else {
            $request->session()->flash('alert-error', 'Ooops. Try again.');
        }
        return ['redirect' => route('AquaParkModule::prices.edit', ['price' => $price])];
    }

    private function save(Request $request, AquaParkPrice $price = null) {
        foreach ($request->all() as $key => $val) {
            if ($val === 'null' || $val == '') {
                $request->merge([$key => null]);
            }
        }

        $fields = ['titles', 'descriptions', 'icons', 'groups', 'passes'];
        foreach($fields as $field) {
            if ($request->has($field)) {
                $request->merge([$field => json_decode($request->get($field))]);
            }
        }

        if ($price === null) {
            $request->merge([
                'order' => AquaParkPrice::query()
                    ->where('aqua_park', '=', $request->get('aqua_park'))
                    ->count() + 1
            ]);
            return AquaParkPrice::query()->create($request->all());
        }

        return $price->update($request->all());
    }

    public function destroy(AquaParkPrice $price, Request $request): void
    {
        try {
            $price->delete();
            $request->session()->flash('alert-success', 'Cena usunięta');
        } catch (Exception $e) {
            $request->session()->flash('alert-error', 'Error: ' . $e->getMessage());
        }
    }

    public function saveOrder(Request $request)
    {
        $list = json_decode($request->get('list'), true);
        foreach ($list as $i => $aquaPark) {
            AquaParkPrice::query()
                ->where('_id', '=', $aquaPark['_id'])
                ->update(['order' => $i + 1]);
        }
        return response()->json();
    }
}
