<?php

namespace Selene\Modules\AquaParkModule\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Selene\Modules\AquaParkModule\Models\AquaParkPrice;
use Selene\Modules\AquaParkModule\Models\AquaParkPricePass;
use Selene\Modules\DashboardModule\ZdrojowaTable;

class PassController extends Controller
{
    public function index(AquaParkPrice $price)
    {
        return view('AquaParkModule::passes.index', [
            'passes' => AquaParkPricePass::query()
                ->where('aqua_park_price', '=', $price->_id)
                ->orderBy('order')
                ->get(),
            'price' => $price
        ]);
    }

    public function sort(AquaParkPrice $price)
    {
        return view('AquaParkModule::passes.sort', [
            'price' => $price
        ]);
    }

    public function ajax(AquaParkPrice $price, Request $request): JsonResponse
    {
        return ZdrojowaTable::response(
            AquaParkPricePass::query()->where('aqua_park_price', '=', $price->_id),
            $request
        );
    }

    public function create(AquaParkPrice $price)
    {
        return view('AquaParkModule::passes.edit', [
            'price' => $price
        ]);
    }

    public function edit(AquaParkPricePass $pass = null)
    {
        return view('AquaParkModule::passes.edit', [
            'pass'  => $pass,
            'price' => $pass->getAquaParkPrice()
        ]);
    }

    public function store(Request $request): array
    {
        $pass = $this->save($request);
        if ($pass) {
            $request->session()->flash('alert-success', 'Karnet pomyślnie dodany');
            return ['redirect' => route('AquaParkModule::passes.edit', ['pass' => $pass])];
        }

        $request->session()->flash('alert-error', 'Ooops. Try again.');
        return ['redirect' => route('AquaParkModule::passes')];
    }

    public function update(Request $request, AquaParkPricePass $pass): array
    {
        if ($this->save($request, $pass)) {
            $request->session()->flash('alert-success', 'Karnet został pomyślnie zaktualizowany.');
        } else {
            $request->session()->flash('alert-error', 'Ooops. Try again.');
        }
        return ['redirect' => route('AquaParkModule::passes.edit', ['pass' => $pass])];
    }

    private function save(Request $request, AquaParkPricePass $pass = null) {
        foreach ($request->all() as $key => $val) {
            if ($val === 'null' || $val == '') {
                $request->merge([$key => null]);
            }
        }

        $fields = ['titles', 'descriptions', 'prices'];
        foreach($fields as $field) {
            if ($request->has($field)) {
                $request->merge([$field => json_decode($request->get($field))]);
            }
        }

        if ($pass === null) {
            $request->merge([
                'order' => AquaParkPricePass::query()
                    ->where('aqua_park_price', '=', $request->get('price'))
                    ->count() + 1
            ]);
            return AquaParkPricePass::query()->create($request->all());
        }

        return $pass->update($request->all());
    }

    public function destroy(AquaParkPricePass $pass, Request $request): void
    {
        try {
            $pass->delete();
            $request->session()->flash('alert-success', 'Karnet usunięty');
        } catch (Exception $e) {
            $request->session()->flash('alert-error', 'Error: ' . $e->getMessage());
        }
    }

    public function saveOrder(Request $request)
    {
        $list = json_decode($request->get('list'), true);
        foreach ($list as $i => $pass) {
            AquaParkPricePass::query()
                ->where('_id', '=', $pass['_id'])
                ->update(['order' => $i + 1]);
        }
        return response()->json();
    }
}
