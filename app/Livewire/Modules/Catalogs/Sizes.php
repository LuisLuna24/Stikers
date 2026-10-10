<?php

namespace App\Livewire\Modules\Catalogs;

use App\Models\StickerSize;
use Livewire\Component;
use Livewire\WithPagination;

class Sizes extends Component
{

    use WithPagination;


    public function render()
    {
        $sizes = StickerSize::query()
            ->with([
                'finish'
            ])
            ->paginate(10);

        return view('livewire.modules.catalogs.sizes', compact('sizes'));
    }
}
