<?php

namespace App\Http\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Material;
use App\Models\MaterialGroup;
use Laravel\Jetstream\InteractsWithBanner;

class MaterialTable extends DataTableComponent
{
    use InteractsWithBanner;

    protected $model = Material::class;

    public $isModalOpen = false, $isModalConfirm = false, $title, $content;
    public $material_id = "", $material_group = "", $name = "", $code = "", $description = "";

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setColumnSelectDisabled()
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
                'before-tools' => 'materials.header',
            ]);
    }

    public function columns(): array
    {
        return [
            Column::make("Material Group", "material_group.name")
                ->sortable()
                ->searchable(),

            Column::make("Material Name", "name")
                ->sortable()
                ->searchable(),

            Column::make("Material Code", "code")
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
                                'class' => 'inline-flex items-center px-2 py-1 text-xs space-x-1 font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800',
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
        return 'materials.modal';
    }

    public function create()
    {
        $this->resetInputFields();

        $this->title = 'Add Material';
        $this->content = "Add";
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'material_group' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'description' => 'nullable|string|max:255'
        ]);

        $material_group = MaterialGroup::firstOrNew(['name' => strtolower($this->material_group)]);
        $material_group->name = strtolower($this->material_group);
        $material_group->save();

        $new_material = new Material;
        $new_material->material_group_id = $material_group->id;
        $new_material->name = strtolower($this->name);
        $new_material->code = strtolower($this->code);
        $new_material->description = !$this->description ? null : strtolower($this->description);
        $new_material->save();

        $this->banner('Successfully added material.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $this->resetInputFields();

        $material = Material::find($id);
        $this->material_id = $material->id;
        $this->material_group = $material->material_group->name;
        $this->name = $material->name;
        $this->code = $material->code;
        $this->description = $material->description;

        $this->title = 'Edit Material';
        $this->content = "Update";
        $this->isModalOpen = true;
    }

    public function update()
    {
        $this->validate([
            'material_group' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'description' => 'nullable|string|max:255'
        ]);

        $material_group = MaterialGroup::firstOrNew(['name' => strtolower($this->material_group)]);
        $material_group->name = strtolower($this->material_group);
        $material_group->save();

        $material = Material::find($this->material_id);
        $material->material_group_id = $material_group->id;
        $material->name = strtolower($this->name);
        $material->code = strtolower($this->code);
        $material->description = !$this->description ? null : strtolower($this->description);
        $material->save();

        if ($material->wasChanged()) {
            $this->banner('Successfully updated material.');
        }
        $this->closeModal();
    }

    public function delete($id)
    {
        $this->resetInputFields();

        $this->material_id = $id;
        $this->title = "Are you sure delete this data?";
        $this->content = "Delete";
        $this->isModalConfirm = true;
    }

    public function destroy()
    {
        $package = Material::find($this->material_id);
        $package->delete();

        $this->banner('Successfully deleted material.');
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
        $this->material_id = "";
        $this->material_group = "";
        $this->name = "";
        $this->code = "";
        $this->description = "";
    }
}
