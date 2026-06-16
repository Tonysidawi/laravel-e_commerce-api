<?php

// namespace Database\Seeders;

// use App\Models\ProductCategory;
// use Illuminate\Database\Seeder;
// use Illuminate\Support\Facades\DB;

// class ProductCategorySeeder extends Seeder
// {
//     /**
//      * Run the database seeds.
//      */
//     public function run(): void
//     {

//         $productCategories = $this->getProductCategories();

//         DB::transaction(function () use ($productCategories) {
//             foreach ($productCategories as $category) {
//                 $productCategory = ProductCategory::create([
//                     'name' => $category,
//                 ]);

//                 $productCategory->save();
//             }
//         });
//     }

//     public function getProductCategories(): array
//     {
//         return [
//             'Electronics',
//             'Clothing',
//             'Home',
//             'Sports',
//             'Toys',
//         ];
//     }
// }
