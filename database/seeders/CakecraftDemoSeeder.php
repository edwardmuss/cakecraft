<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\CakeFlavour;
use App\Models\CakeSize;
use App\Models\Collection;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Optional sample catalogue so the storefront has something to show.
 * Prices are PLACEHOLDERS - edit them in Admin > Cakes.
 *
 *   php artisan db:seed --class=CakecraftDemoSeeder
 */
class CakecraftDemoSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::query()->orderBy('id')->first();
        if (!$branch) {
            $this->command?->warn('Create a branch first (register the first admin user).');
            return;
        }
        $b = $branch->id;

        // Sizes
        $sizeDefs = [['0.5 kg', 0.5, '4'], ['1 kg', 1, '8-10'], ['1.5 kg', 1.5, '12-15'], ['2 kg', 2, '16-20'], ['3 kg', 3, '25-30'], ['4 kg', 4, '35-40'], ['5 kg', 5, '45-50']];
        $sizes = [];
        foreach ($sizeDefs as $i => [$label, $kg, $serves]) {
            $sizes[(string) $kg] = CakeSize::firstOrCreate(['branch_id' => $b, 'label' => $label], ['weight_kg' => $kg, 'serves' => $serves, 'sort_order' => $i, 'is_active' => true]);
        }

        // Collections (occasion_type + is_recurring drive the yearly reminders)
        $colDefs = [
            ['Birthday Cakes', 'birthday', true], ['Wedding Cakes', 'wedding', false], ['Anniversary Cakes', 'anniversary', true],
            ['Graduation Cakes', 'graduation', false], ['Baby Shower', 'baby_shower', false], ['Special Occasion', 'other', false], ['Cupcakes & Minis', null, false],
        ];
        $cols = [];
        foreach ($colDefs as $i => [$name, $type, $recurring]) {
            $cols[$name] = Collection::firstOrCreate(['branch_id' => $b, 'name' => $name], [
                'slug' => Str::slug($name), 'occasion_type' => $type, 'is_recurring' => $recurring, 'sort_order' => $i, 'is_active' => true,
            ]);
        }

        // Flavours
        $flavours = [];
        foreach (['Vanilla', 'Chocolate', 'Red Velvet', 'Black Forest', 'Strawberry', 'Lemon', 'Carrot', 'Marble', 'Orange', 'Coffee'] as $i => $f) {
            $flavours[$f] = CakeFlavour::firstOrCreate(['branch_id' => $b, 'name' => $f], ['sort_order' => $i, 'is_active' => true]);
        }

        // Signature flavour cakes: [name, 0.5kg price, 1kg price]. Larger sizes = 1kg price x weight.
        $signature = [
            ['Vanilla', 1400, 2300], ['Strawberry', 1400, 2300], ['Red Velvet', 1600, 2500], ['Black Forest', 1600, 2700],
            ['White Forest', 1600, 2700], ['Chocolate Fudge', 1900, 3000], ['Blueberry', 1900, 3000], ['Carrot', 1600, 2300], ['Lemon Syrup', 1400, 2300],
        ];
        foreach ($signature as [$name, $half, $one]) {
            $cake = Product::firstOrCreate(['branch_id' => $b, 'name' => $name . ' Cake'], [
                'unit_price' => $half, 'product_type' => 'signature', 'is_active' => true, 'is_published' => true,
                'short_description' => "Classic {$name} sponge, baked fresh to order.", 'lead_time_days' => 2,
            ]);
            $cake->collections()->syncWithoutDetaching([$cols['Birthday Cakes']->id, $cols['Anniversary Cakes']->id]);
            foreach ($sizes as $kg => $size) {
                $price = $kg === '0.5' ? $half : (int) round($one * (float) $kg, -1);
                $cake->sizePrices()->updateOrCreate(['cake_size_id' => $size->id], ['price' => $price, 'is_active' => true]);
            }
        }

        // Custom / themed cakes
        $custom = [
            ['Custom Birthday Cake', 'Birthday Cakes', 3500], ['Themed Kids Cake', 'Birthday Cakes', 3500], ['Naked Wedding Cake', 'Wedding Cakes', 12000],
            ['Two-Tier Wedding Cake', 'Wedding Cakes', 18000], ['Anniversary Cake', 'Anniversary Cakes', 4500], ['Graduation Cake', 'Graduation Cakes', 4500],
            ['Baby Shower Cake', 'Baby Shower', 5000],
        ];
        foreach ($custom as [$name, $collection, $from]) {
            $cake = Product::firstOrCreate(['branch_id' => $b, 'name' => $name], [
                'unit_price' => $from, 'product_type' => str_contains($name, 'Wedding') ? 'wedding' : 'custom', 'is_active' => true,
                'is_published' => true, 'is_featured' => true, 'lead_time_days' => 5,
                'short_description' => 'Designed around your theme, colours and message.',
            ]);
            $cake->collections()->syncWithoutDetaching([$cols[$collection]->id]);
            foreach (['1 kg' => 1, '2 kg' => 2, '3 kg' => 3, '5 kg' => 5] as $label => $kg) {
                $size = CakeSize::where('branch_id', $b)->where('label', $label)->first();
                if ($size) {
                    $cake->sizePrices()->updateOrCreate(['cake_size_id' => $size->id], ['price' => $from + ($kg - 1) * 1500, 'is_active' => true]);
                }
            }
            $cake->flavours()->syncWithoutDetaching(collect($flavours)->mapWithKeys(fn ($f) => [$f->id => ['extra_price' => 0]])->all());
        }

        $this->command?->info('Demo catalogue created. Replace placeholder prices and photos in Admin > Cakes.');
    }
}
