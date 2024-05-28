<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Category;
use App\Models\TvModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Collection;

class CrawlerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:crawler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all Tv Models and accessories from the website';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $tvHtml = file_get_contents('https://www.shoptok.si/televizorji/cene/206');

        $dom = new Crawler($tvHtml);

        $pagination = $dom->filter('.pagination li')
            ->last()
            ->text();

        $categories = Category::all();
        $tvCategoryID =  $categories->where('name', 'tv')->first()->id;

        $brands = Brand::all();
        $tvModels = TvModel::where('category_id',
           $tvCategoryID
        )->get();

        $elements = $dom->filter('.relative.item-box')
            ->each(function (Crawler $node, $i) use (&$tvModels, &$brands, $tvCategoryID) {

                $brandID = $this->saveBrands($brands, $node);
                $this->saveData($tvModels, $node, $brandID, $tvCategoryID);
            });

        for($i = 2; $i <= $pagination; $i++) {
            $html = file_get_contents( 'https://www.shoptok.si/televizorji/cene/206?page=' . $i);

            $dom = new Crawler($html);

            $elements = $dom->filter('.col-6.col-xl-3.mb-3 .relative.item-box')
                ->each(function (Crawler $node, $i) use ($tvCategoryID, &$tvModels, &$brands) {

                    $brandID = $this->saveBrands($brands, $node);
                    $this->saveData($tvModels, $node, $brandID, $tvCategoryID);
                });
        }

        $receiverHtml = file_get_contents('https://www.shoptok.si/tv-prijamnici/cene/56');

        $this->info('TV models fetched!');

        $receiveDom = new Crawler($receiverHtml);

        $receiverID = $categories->where('name', 'receivers')->first()->id;

        $receivePagination = $receiveDom->filter('.pagination li')
            ->last()
            ->text();

        $receivers = TvModel::where('category_id',
            $receiverID
        )->get();

        $elements = $dom->filter('.relative.item-box')
            ->each(function (Crawler $node, $i) use (&$receivers, &$brands, $receiverID) {

                $brandID = $this->saveBrands($brands, $node);
                $this->saveData($receivers, $node,
                    $brandID,
                    $receiverID);
            });

        for($i = 2; $i <= $receivePagination; $i++) {
            $html = file_get_contents('https://www.shoptok.si/tv-prijamnici/cene/56?page=' . $i);

            $dom = new Crawler($html);

            $elements = $dom->filter('.col-6.col-xl-3.mb-3 .relative.item-box')
                ->each(function (Crawler $node, $i) use ($receiverID, &$tvModels, &$brands) {

                    $brandID = $this->saveBrands($brands, $node);
                    $this->saveData($tvModels, $node, $brandID, $receiverID);
                });
        }

        $this->info('Receivers models fetched!');
    }

    public function saveBrands(Collection $brands, Crawler $node): int
    {
        $brand = preg_replace(
            '/\x{A0}/u',
            '',
            $node->filter('.font-semibold')->text('N/A')
        );

        $currentBrand = $brands->where('name', $brand);


        if(!in_array($brand, $brands->toArray(), true) && $currentBrand->isEmpty()) {

            $model = Brand::create([
                'name' => $brand,
            ]);

            $brands[] = $model;

            $brandID = $model->id;

        }

        return $brandID ?? $currentBrand->first()->id;
    }


    protected function saveData(Collection $tvModels, Crawler $node, int $brandID, int $categoryID): void
    {

        $tvModel = preg_replace(
            '/\x{A0}/u',
            ' ',
            $node->filter('.text-black.text-15')->text('N/A'));

        $tvModelSplit = preg_split('/\s+/', $tvModel, 2);
        $currentTvModel = $tvModels->where('name', $tvModelSplit[1]);

        if(!in_array(
                $tvModelSplit[1], $tvModels->toArray(), true)
            && $currentTvModel->isEmpty()){

            $filename = null;

            try {
                $image = $node->filter('img')->attr('src');

                $contents = file_get_contents($image);

                $filename = basename($image);

                Storage::put('public/' . $filename, $contents);
            } catch (\Exception $e) {
                $filename = null;
            }

            $data = TvModel::create([
                'name' => $tvModelSplit[1],
                'price' => $node->filter('.text-blue strong')->text() ?? 'N/A',
                'link' => $node->filter('a')->first()->attr('href', 'N/A'),
                'image' => $filename,
                'brand_id' => $brandID,
                'category_id' => $categoryID,
            ]);

            $tvModels[] = $data;
        }
    }
}
