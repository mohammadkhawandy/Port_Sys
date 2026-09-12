<?php

namespace App\Controllers;

use App\Libraries\ActivityLogger;
use App\Models\Ship as ShipModel;
use App\Models\Trip;
use CodeIgniter\Exceptions\PageNotFoundException;

class Ship extends BaseController
{
    protected ShipModel $shipModel;

    public function __construct()
    {
        $this->shipModel = new ShipModel();
    }

    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));
        $type = trim((string) $this->request->getGet('type'));
        $builder = $this->shipModel;

        if ($q !== '') {
            $builder->groupStart()->like('name', $q)->orLike('type', $q)->groupEnd();
        }
        if ($type !== '') {
            $builder->where('type', $type);
        }

        $filteredCountBuilder = new ShipModel();
        if ($q !== '') {
            $filteredCountBuilder->groupStart()->like('name', $q)->orLike('type', $q)->groupEnd();
        }
        if ($type !== '') {
            $filteredCountBuilder->where('type', $type);
        }
        $filteredCount = $filteredCountBuilder->countAllResults();
        $filteredCapacityRow = new ShipModel();
        if ($q !== '') {
            $filteredCapacityRow->groupStart()->like('name', $q)->orLike('type', $q)->groupEnd();
        }
        if ($type !== '') {
            $filteredCapacityRow->where('type', $type);
        }
        $filteredCapacity = $filteredCapacityRow->selectSum('capacity')->first();

        $ships = $builder->orderBy('name', 'ASC')->paginate(18);
        $types = (new ShipModel())->select('type')->groupBy('type')->orderBy('type')->findAll();
        $capacity = (new ShipModel())->selectSum('capacity')->first();

        return view('ships/index', [
            'ships' => $ships,
            'pager' => $this->shipModel->pager,
            'filters' => ['q' => $q, 'type' => $type],
            'types' => array_column($types, 'type'),
            'stats' => [
                'total_ships' => (new ShipModel())->countAllResults(),
                'filtered_ships' => $filteredCount,
                'total_capacity' => (int) ($capacity['capacity'] ?? 0),
                'filtered_capacity' => (int) ($filteredCapacity['capacity'] ?? 0),
            ],
        ]);
    }

    public function create()
    {
        return view('ships/create');
    }

    public function store()
    {
        $data = $this->payload();
        if (! $this->shipModel->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $this->shipModel->errors());
        }

        $id = (int) $this->shipModel->getInsertID();
        (new ActivityLogger())->log('ship.created', sprintf(lang('App.activity_ship_created'), $data['name']), 'ship', $id);
        return redirect()->to(site_url('ships'))->with('success', lang('App.msg_ship_created'));
    }

    public function edit(int $id)
    {
        $ship = $this->shipModel->find($id);
        if (! $ship) {
            throw PageNotFoundException::forPageNotFound(lang('App.msg_ship_not_found'));
        }
        return view('ships/edit', ['ship' => $ship]);
    }

    public function update(int $id)
    {
        $ship = $this->shipModel->find($id);
        if (! $ship) {
            return redirect()->to(site_url('ships'))->with('error', lang('App.msg_ship_not_found'));
        }

        $data = $this->payload();
        if (! $this->shipModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->shipModel->errors());
        }

        (new ActivityLogger())->log('ship.updated', sprintf(lang('App.activity_ship_updated'), $data['name']), 'ship', $id);
        return redirect()->to(site_url('ships'))->with('success', lang('App.msg_ship_updated'));
    }

    public function delete(int $id)
    {
        $ship = $this->shipModel->find($id);
        if (! $ship) {
            return redirect()->to(site_url('ships'))->with('error', lang('App.msg_ship_not_found'));
        }

        if ((new Trip())->where('ship_id', $id)->countAllResults() > 0) {
            return redirect()->to(site_url('ships'))->with('error', lang('App.msg_ship_in_use'));
        }

        if (! $this->shipModel->delete($id)) {
            return redirect()->back()->with('error', lang('App.msg_ship_delete_failed'));
        }
        (new ActivityLogger())->log('ship.deleted', sprintf(lang('App.activity_ship_deleted'), $ship['name']), 'ship', $id);
        return redirect()->to(site_url('ships'))->with('success', lang('App.msg_ship_deleted'));
    }

    private function payload(): array
    {
        return [
            'name' => trim((string) $this->request->getPost('name')),
            'type' => trim((string) $this->request->getPost('type')),
            'capacity' => (int) $this->request->getPost('capacity'),
        ];
    }
}
