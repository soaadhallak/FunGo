<?php
namespace App\Filters;


use Illuminate\Http\Request;
use App\Models\Place;
use Illuminate\Support\Collection;

class PlacesFilter
{
    protected $request;
    protected $places;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->places = Place::with(['activities' => function ($q) {
        $q->withPivot('min_price');
    }, 'sales', 'reviews','stories']) ->withAvg('reviews as avg_rating', 'rating');
    }

    public function apply()
    {
        $this->applyFilters();

        $placesCollection = $this->places->get();

       
        if ($this->request->filled('filters')) {
            $filters = explode(',', $this->request->filters);

            if (in_array('offers', $filters)) {
                $placesCollection = $placesCollection->filter(fn($place) => $place->sales->isNotEmpty());
            }

           
            foreach ($filters as $filter) {
                switch ($filter) {
                    case 'nearest':
                        if ($this->request->latitude && $this->request->longitude) {
                            $lat = $this->request->latitude;
                            $lng = $this->request->longitude;
                            $placesCollection = $placesCollection->sortBy(function ($place) use ($lat, $lng) {
                                return $this->distance($lat, $lng, $place->latitude, $place->longitude);
                            });
                        }
                        break;

                    case 'cheapest':
                        $placesCollection = $placesCollection->sortBy(function ($place) {
                        $minPrice = $place->activities->pluck('pivot.min_price')->filter()->min();
                                return $minPrice ?? PHP_INT_MAX;
                        });
                         break;

                    case 'rating':
                        $placesCollection = $placesCollection->sortByDesc('avg_rating');
                        break;

                    
                }
            }
        }

        return $placesCollection->values();
    }

    protected function applyFilters()
    {
        $request = $this->request;

        $this->places = $this->places
            ->when($request->search, function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('address', 'like', "%$search%")
                      ->orWhere('description', 'like', "%$search%");
                });
            })
            ->when($request->governorate, fn($q) => $q->where('governorate', $request->governorate))
            ->when($request->activity_type_id, function ($query) use ($request) {
                $activityId = $request->activity_type_id;
                $query->whereHas('activities', fn($q) => $q->where('activity_type_id', $activityId));
            });

        
        if ($request->filled('filters')) {
            $filters = explode(',', $request->filters);
            if (in_array('nearest', $filters) && $request->latitude && $request->longitude) {
                $lat = $request->latitude;
                $lng = $request->longitude;
                $this->places = $this->places->selectRaw('places.*, 
                    (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
                    cos(radians(longitude) - radians(?)) + 
                    sin(radians(?)) * sin(radians(latitude)))) AS distance', 
                    [$lat, $lng, $lat])
                    ->orderBy('distance');
            }
        }
    }
   
    protected function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  
                cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $km = $miles * 1.609344;
        return $km;
    }


 }
