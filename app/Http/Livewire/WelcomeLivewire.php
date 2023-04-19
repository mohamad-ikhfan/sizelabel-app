<?php

namespace App\Http\Livewire;

use App\Models\Material;
use Livewire\Component;

class WelcomeLivewire extends Component
{
    public $materials = [];

    public function render()
    {
        return view('livewire.welcome-livewire');
    }

    public function mount()
    {
        $this->materials = Material::get();
    }
}