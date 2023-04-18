<?php

namespace App\Http\Livewire;

use App\Models\Material;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\StockMaterial;
use Laravel\Jetstream\InteractsWithBanner;

class StockMaterialTable extends DataTableComponent
{
    use InteractsWithBanner;

    protected $model = StockMaterial::class;

    public $isModalOpen = false, $isModalConfirm = false, $title, $content;
    public $stock_id = [], $material_quantities = [], $date = "", $description = "", $materials = [];

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setColumnSelectDisabled()
            ->setDefaultSort('id', 'desc')
            ->setTdAttributes(function (Column $column) {
                if ($column->isField('code')) {
                    $class = 'uppercase';
                } else {
                    $class = 'capitalize';
                }

                return [
                    'class' => $class,
                ];
            })
            ->setConfigurableAreas([
                'before-tools' => 'stock-materials.header',
            ]);
    }

    public function columns(): array
    {
        return [
            Column::make("Material Name", "material.name")
                ->sortable()
                ->searchable(),

            Column::make("Material Code", "material.code")
                ->sortable()
                ->searchable(),

            Column::make("Date", "date")
                ->sortable()
                ->searchable(),

            Column::make("Quantity", "quantity")
                ->sortable()
                ->searchable(),

            Column::make("Status", "status")
                ->sortable()
                ->searchable(),

            Column::make("First Stock", "first_stock")
                ->sortable()
                ->searchable(),

            Column::make("Last Stock", "last_stock")
                ->sortable()
                ->searchable(),

            Column::make("Description", "description")
                ->sortable()
                ->searchable(),

            Column::make(__("Action"), "id")
                ->format(function ($value) {

                    return view('components.button-action')
                        ->with('slots', [
                            [
                                'class' => 'inline-flex items-center px-2 py-1 text-xs space-x-1 font-medium text-center text-white rounded-lg bg-amber-700 hover:bg-amber-800 focus:ring-4 focus:ring-amber-300 dark:bg-amber-600 dark:hover:bg-amber-700 dark:focus:ring-amber-800',
                                'icon' => 'fa-solid fa-pen-to-square',
                                'method' => 'wire:click="edit(' . $value . ')"',
                                'name' => __('Edit')
                            ],
                            [
                                'class' => 'inline-flex items-center px-2 py-1 text-xs space-x-1 font-medium text-center text-white rounded-lg bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800',
                                'icon' => 'fa-solid fa-trash-can',
                                'method' => 'wire:click="delete(' . $value . ')"',
                                'name' => __('Delete')
                            ],
                        ]);
                }),
        ];
    }

    public function customView(): string
    {
        return 'stock-materials.modal';
    }

    public function materialIn()
    {
        $this->resetInputFields();

        $this->materials = Material::all();
        $this->title = "Material In";
        $this->content = "Save";
        $this->isModalOpen = true;
    }

    public function takeMaterial()
    {
        $this->resetInputFields();

        $this->materials = Material::all();
        $this->title = "Take Material";
        $this->content = "Save";
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'material_quantities' => 'nullable|array',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255'
        ]);

        $id_materials = $this->material_quantities ? array_keys($this->material_quantities) : [];
        $quantity_materials = $this->material_quantities ? array_values($this->material_quantities) : [];

        for ($i = 0; $i < count($this->material_quantities); $i++) {
            $material_id = $id_materials[$i];
            $quantity = $quantity_materials[$i];
            $last_stock = StockMaterial::where('material_id', $material_id)->latest()->first()->last_stock ?? 0;
            $stock_material = new StockMaterial;
            $stock_material->user_id = auth()->user()->id;
            $stock_material->material_id = $material_id;
            $stock_material->quantity = $quantity;
            $stock_material->date = $this->date;
            $stock_material->description = !$this->description ? null : $this->description;
            if ($this->title === 'Material In') {
                $stock_material->status = 'in';
                $stock_material->first_stock = $last_stock;
                $stock_material->last_stock = $last_stock + $quantity;
            } else {
                $stock_material->status = 'out';
                $stock_material->first_stock = $last_stock;
                $stock_material->last_stock = $last_stock - $quantity;
            }
            $stock_material->save();
        }
        if (count($this->material_quantities) > 0) {
            $this->banner('Successfully added ' . strtolower($this->title));
        }
        $this->closeModal();
    }

    public function edit($id)
    {
        $this->resetInputFields();

        $stock = StockMaterial::find($id);
        $this->materials = Material::where('id', $stock->material->id)->get();
        $this->stock_id = $stock->id;
        $this->material_quantities[$stock->material->id] = "$stock->quantity";
        $this->date = $stock->date;
        $this->description = $stock->description;
        $this->title = "Edit Stock";
        $this->content = "Update";
        $this->isModalOpen = true;
    }

    public function update()
    {
        $this->validate([
            'material_quantities' => 'nullable|array',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255'
        ]);
        $stock_material = StockMaterial::find($this->stock_id);
        $quantity_material = $this->material_quantities[$stock_material->material->id];
        if ($stock_material->status === 'in') {
            if ($quantity_material < $stock_material->quantity) {
                $quantity = $stock_material->quantity - $quantity_material;
                $last_stock = $stock_material->last_stock - $quantity;
            } elseif ($quantity_material > $stock_material->quantity) {
                $quantity = $quantity_material - $stock_material->quantity;
                $last_stock = $stock_material->last_stock + $quantity;
            } else {
                $last_stock = $stock_material->last_stock;
            }
        } else {
            if ($quantity_material < $stock_material->quantity) {
                $quantity = $stock_material->quantity - $quantity_material;
                $last_stock = $stock_material->last_stock + $quantity;
            } elseif ($quantity_material > $stock_material->quantity) {
                $quantity = $quantity_material - $stock_material->quantity;
                $last_stock = $stock_material->last_stock - $quantity;
            } else {
                $last_stock = $stock_material->last_stock;
            }
        }
        $stock_material->user_id = auth()->user()->id;
        $stock_material->quantity = $quantity_material;
        $stock_material->date = $this->date;
        $stock_material->description = !$this->description ? null : $this->description;
        $stock_material->last_stock = $last_stock;
        $stock_material->save();

        if ($stock_material->wasChanged()) {
            $this->banner('Successfully updated stock material.');
        }
        $this->closeModal();
    }

    public function delete($id)
    {
        $this->resetInputFields();

        $this->stock_id = $id;
        $this->title = "Are you sure delete this data?";
        $this->content = "Delete";
        $this->isModalConfirm = true;
    }

    public function destroy()
    {
        $stock = StockMaterial::find($this->stock_id);
        $stock->delete();

        $this->banner('Successfully deleted stock.');
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->isModalConfirm = false;
        $this->title = "";
        $this->content = "";
        $this->resetErrorBag();
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->stock_id = [];
        $this->material_quantities = [];
        $this->date = "";
        $this->description = "";
        $this->materials = [];
    }
}