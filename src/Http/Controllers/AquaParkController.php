<?php

namespace Selene\Modules\AquaParkModule\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Selene\Modules\DashboardModule\ZdrojowaTable;
use Selene\Modules\AquaParkModule\Models\AquaPark;

class AquaParkController extends Controller
{
    public function index()
    {
        return view('AquaParkModule::index', [
            'aquaParks' => AquaPark::query()->orderBy('order')->get()
        ]);
    }

    public function sort()
    {
        return view('AquaParkModule::sort');
    }

    public function ajax(Request $request): JsonResponse
    {
        return ZdrojowaTable::response(AquaPark::query(), $request);
    }

    public function create()
    {
        return view('AquaParkModule::edit');
    }

    public function edit(AquaPark $aquaPark = null)
    {
        return view('AquaParkModule::edit', [
            'aquaPark' => $aquaPark,
        ]);
    }

    public function store(Request $request)
    {
        $aquaPark = $this->save($request);
        if ($aquaPark) {
            $request->session()->flash('alert-success', 'Aquapark pomyślnie dodano.');
            return ['redirect' => route('AquaParkModule::aqua.parks.edit', ['aquaPark' => $aquaPark])];
        }

        $request->session()->flash('alert-error', 'Ooops. Try again.');
        return ['redirect' => route('AquaParkModule::aqua.parks')];
    }

    public function update(Request $request, AquaPark $aquaPark)
    {
        if ($this->save($request, $aquaPark)) {
            $request->session()->flash('alert-success', 'Aquapark został pomyślnie zaktualizowany.');
        } else {
            $request->session()->flash('alert-error', 'Ooops. Try again.');
        }
        return ['redirect' => route('AquaParkModule::aqua.parks.edit', ['aquaPark' => $aquaPark])];
    }

    private function save(Request $request, AquaPark $aquaPark = null) {
        foreach ($request->all() as $key => $val) {
            if ($val === 'null' || $val == '') {
                $request->merge([$key => null]);
            }
        }

        $fields = ['work_days', 'season_low', 'season_high', 'icons', 'gallery', 'map', 'disclaimer'];
        foreach($fields as $field) {
            if ($request->has($field)) {
                $request->merge([$field => json_decode($request->get($field))]);
            }
        }

        if ($aquaPark === null) {
            $request->merge(['order' => AquaPark::query()->count() + 1]);
            return AquaPark::create($request->all());
        }

        return $aquaPark->update($request->all());
    }

    public function destroy(AquaPark $aquaPark, Request $request): void
    {
        try {
            $aquaPark->delete();
            $request->session()->flash('alert-success', 'Aquapark is deleted');
        } catch (Exception $e) {
            $request->session()->flash('alert-error', 'Error: ' . $e->getMessage());
        }
    }

    public function saveOrder(Request $request)
    {
        $list = json_decode($request->get('list'), true);
        foreach ($list as $i => $aquaPark) {
            AquaPark::query()->where('_id', '=', $aquaPark['_id'])->update(['order' => $i + 1]);
        }
        return ['redirect' => route('AquaParkModule::aqua.parks')];
    }
}
