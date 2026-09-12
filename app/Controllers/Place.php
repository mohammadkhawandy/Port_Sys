<?php

namespace App\Controllers;

use App\Libraries\ActivityLogger;
use App\Libraries\RealPortCatalog;
use App\Models\Place as PlaceModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Place extends BaseController
{
    protected PlaceModel $placeModel;

    public function __construct()
    {
        $this->placeModel = new PlaceModel();
    }

    public function index()
    {
        $filters = [
            'q' => trim((string) $this->request->getGet('q')),
            'country' => strtoupper(trim((string) $this->request->getGet('country'))),
            'type' => trim((string) $this->request->getGet('type')),
            'status' => trim((string) $this->request->getGet('status')),
        ];

        $builder = $this->applyFilters(new PlaceModel(), $filters);
        $places = $builder
            ->orderBy('status', 'ASC')
            ->orderBy('country_en', 'ASC')
            ->orderBy('name_en', 'ASC')
            ->paginate(20);
        $pager = $builder->pager;

        $mapPlaces = $this->applyFilters(new PlaceModel(), $filters)
            ->select('id, code, name, name_en, name_ar, country, country_code, country_en, country_ar, city_en, city_ar, latitude, longitude, type, status')
            ->orderBy('name_en', 'ASC')
            ->findAll(250);

        $countryRows = (new PlaceModel())
            ->select('country_code, country_en, country_ar, country')
            ->where('country_code IS NOT NULL', null, false)
            ->groupBy('country_code, country_en, country_ar, country')
            ->orderBy('country_en', 'ASC')
            ->findAll();

        return view('places/index', [
            'places' => $places,
            'mapPlaces' => $mapPlaces,
            'pager' => $pager,
            'filters' => $filters,
            'countries' => $countryRows,
            'stats' => [
                'total' => (new PlaceModel())->countAllResults(),
                'active' => (new PlaceModel())->where('status', 'active')->countAllResults(),
                'countries' => count($countryRows),
                'seeded' => (new PlaceModel())->where('is_seeded', 1)->countAllResults(),
            ],
        ]);
    }

    public function create()
    {
        return view('places/create', [
            'catalogCount' => count(RealPortCatalog::all()),
        ]);
    }

    public function store()
    {
        $data = $this->placePayload();
        if ($this->codeExists($data['code'] ?? null)) {
            return redirect()->back()->withInput()->with('error', lang('App.place_code_exists'));
        }
        if (! $this->placeModel->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $this->placeModel->errors());
        }

        $id = (int) $this->placeModel->getInsertID();
        (new ActivityLogger())->log('place.created', sprintf(lang('App.activity_place_created'), $data['name']), 'place', $id);

        return redirect()->to(site_url('places'))->with('success', lang('App.msg_place_created'));
    }

    public function edit(int $id)
    {
        $place = $this->placeModel->find($id);
        if (! $place) {
            throw PageNotFoundException::forPageNotFound(lang('App.msg_place_not_found'));
        }

        return view('places/edit', ['place' => $place]);
    }

    public function update(int $id)
    {
        $place = $this->placeModel->find($id);
        if (! $place) {
            return redirect()->to(site_url('places'))->with('error', lang('App.msg_place_not_found'));
        }

        $data = $this->placePayload();
        if ($this->codeExists($data['code'] ?? null, $id)) {
            return redirect()->back()->withInput()->with('error', lang('App.place_code_exists'));
        }
        if (! $this->placeModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->placeModel->errors());
        }

        (new ActivityLogger())->log('place.updated', sprintf(lang('App.activity_place_updated'), $data['name']), 'place', $id);

        return redirect()->to(site_url('places'))->with('success', lang('App.msg_place_updated'));
    }

    public function delete(int $id)
    {
        $place = $this->placeModel->find($id);
        if (! $place) {
            return redirect()->to(site_url('places'))->with('error', lang('App.msg_place_not_found'));
        }

        if (! $this->placeModel->delete($id)) {
            return redirect()->back()->with('error', lang('App.msg_place_delete_failed'));
        }

        (new ActivityLogger())->log('place.deleted', sprintf(lang('App.activity_place_deleted'), place_display_name($place)), 'place', $id);

        return redirect()->to(site_url('places'))->with('success', lang('App.msg_place_deleted'));
    }

    public function importDefaults()
    {
        $inserted = 0;
        $updated = 0;
        $now = date('Y-m-d H:i:s');

        foreach (RealPortCatalog::all() as $item) {
            $existing = $this->placeModel->where('code', $item['code'])->first();
            if ($existing) {
                $payload = array_merge($item, ['updated_at' => $now]);
                unset($payload['created_at']);
                if ($this->placeModel->skipValidation(true)->update((int) $existing['id'], $payload)) {
                    $updated++;
                }
                continue;
            }

            if ($this->placeModel->skipValidation(true)->insert(array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now,
            ]))) {
                $inserted++;
            }
        }

        (new ActivityLogger())->log(
            'place.catalog_imported',
            sprintf(lang('App.activity_place_catalog_imported'), $inserted, $updated),
            'place'
        );

        return redirect()->to(site_url('places'))->with(
            'success',
            sprintf(lang('App.msg_place_catalog_imported'), $inserted, $updated)
        );
    }

    /**
     * @param array{q:string,country:string,type:string,status:string} $filters
     */
    private function applyFilters(PlaceModel $model, array $filters): PlaceModel
    {
        if ($filters['q'] !== '') {
            $model->groupStart()
                ->like('name', $filters['q'])
                ->orLike('name_en', $filters['q'])
                ->orLike('name_ar', $filters['q'])
                ->orLike('country', $filters['q'])
                ->orLike('country_en', $filters['q'])
                ->orLike('country_ar', $filters['q'])
                ->orLike('city_en', $filters['q'])
                ->orLike('city_ar', $filters['q'])
                ->orLike('code', $filters['q'])
                ->groupEnd();
        }

        if ($filters['country'] !== '') {
            $model->where('country_code', $filters['country']);
        }
        if (in_array($filters['type'], ['port', 'terminal', 'anchorage', 'city', 'other'], true)) {
            $model->where('type', $filters['type']);
        }
        if (in_array($filters['status'], ['active', 'inactive'], true)) {
            $model->where('status', $filters['status']);
        }

        return $model;
    }

    private function placePayload(): array
    {
        $nameEn = trim((string) $this->request->getPost('name_en'));
        $nameAr = trim((string) $this->request->getPost('name_ar'));
        $countryEn = trim((string) $this->request->getPost('country_en'));
        $countryAr = trim((string) $this->request->getPost('country_ar'));

        return [
            'code' => $this->nullableUpper((string) $this->request->getPost('code')),
            'name' => $nameEn !== '' ? $nameEn : $nameAr,
            'name_en' => $nameEn !== '' ? $nameEn : null,
            'name_ar' => $nameAr !== '' ? $nameAr : null,
            'country' => $countryEn !== '' ? $countryEn : $countryAr,
            'country_code' => $this->nullableUpper((string) $this->request->getPost('country_code')),
            'country_en' => $countryEn !== '' ? $countryEn : null,
            'country_ar' => $countryAr !== '' ? $countryAr : null,
            'city_en' => $this->nullable((string) $this->request->getPost('city_en')),
            'city_ar' => $this->nullable((string) $this->request->getPost('city_ar')),
            'latitude' => trim((string) $this->request->getPost('latitude')),
            'longitude' => trim((string) $this->request->getPost('longitude')),
            'type' => trim((string) ($this->request->getPost('type') ?: 'port')),
            'timezone' => $this->nullable((string) $this->request->getPost('timezone')),
            'status' => trim((string) ($this->request->getPost('status') ?: 'active')),
            'is_seeded' => 0,
        ];
    }

    private function codeExists(?string $code, ?int $exceptId = null): bool
    {
        if ($code === null || $code === '') {
            return false;
        }

        $model = new PlaceModel();
        $model->where('code', $code);
        if ($exceptId !== null) {
            $model->where('id !=', $exceptId);
        }

        return $model->countAllResults() > 0;
    }

    private function nullable(string $value): ?string
    {
        $value = trim($value);
        return $value === '' ? null : $value;
    }

    private function nullableUpper(string $value): ?string
    {
        $value = strtoupper(trim($value));
        return $value === '' ? null : $value;
    }
}
