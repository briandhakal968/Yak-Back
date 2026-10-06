<?php

namespace Database\Seeders;

use App\Models\Tour;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds one sample tour in each of the three new categories
 * (wellness-journey, fast-packing-trips, heli-tours) so the public
 * pages and admin list have content to display.
 */
class NewCategoryToursSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'title' => 'Himalayan Wellness Retreat',
                'category' => 'wellness-journey',
                'overview' => 'A rejuvenating 7-day wellness retreat in the foothills of the Annapurna region. Daily yoga, meditation, organic meals, and guided nature walks in the Himalayan foothills.',
                'price' => 1800,
                'duration_value' => 7,
                'duration_unit' => 'days',
                'max_altitude' => '1,400m',
                'best_season' => 'Year-round',
                'group_size' => '4-12 persons',
                'region' => 'Annapurna Region',
            ],
            [
                'title' => 'Express Everest Adventure',
                'category' => 'fast-packing-trips',
                'overview' => 'A fast-paced 5-day guided adventure to the foot of Mt. Everest with helicopter return. Perfect for travellers with limited time who want a taste of the high Himalayas.',
                'price' => 3500,
                'duration_value' => 5,
                'duration_unit' => 'days',
                'max_altitude' => '5,364m',
                'best_season' => 'Mar-May, Sep-Nov',
                'group_size' => '2-8 persons',
                'region' => 'Everest Region',
            ],
            [
                'title' => 'Everest Base Camp Heli Tour',
                'category' => 'heli-tours',
                'overview' => 'A breathtaking helicopter tour to Everest Base Camp with a breakfast stop at the world\'s highest-elevation hotel. See the Khumbu Icefall and surrounding peaks from the air in a single day.',
                'price' => 4200,
                'duration_value' => 1,
                'duration_unit' => 'days',
                'max_altitude' => '5,364m',
                'best_season' => 'Mar-May, Sep-Nov',
                'group_size' => '2-5 persons',
                'region' => 'Everest Region',
            ],
        ];

        foreach ($samples as $sample) {
            $existing = Tour::where('slug', Str::slug($sample['title']))->first();
            if ($existing) {
                continue;
            }

            $gallery = [[
                'type' => 'image',
                'url' => 'https://demo.dilscashmere.com/storage/media/images/1.jpeg',
                'alt' => $sample['title'],
            ]];

            Tour::create([
                'title' => $sample['title'],
                'slug' => Str::slug($sample['title']),
                'external_id' => (string) Str::uuid(),
                'overview' => $sample['overview'],
                'highlights' => [
                    'Authentic local experiences',
                    'Expert guides and full support',
                    'Comfortable accommodation',
                ],
                'itinerary' => [
                    [
                        'day' => 1,
                        'title' => 'Arrival',
                        'description' => $sample['overview'],
                        'activities' => ['Transfer to hotel', 'Welcome briefing'],
                        'trail_time' => null,
                        'sleeping_altitude' => null,
                        'meal' => 'Dinner',
                        'accommodation' => 'Hotel',
                    ],
                ],
                'inclusions' => ['Professional guide', 'Accommodation', 'Meals as per itinerary'],
                'exclusions' => ['International flights', 'Personal expenses', 'Travel insurance'],
                'gallery' => $gallery,
                'video' => null,
                'faqs' => [],
                'route_info' => [],
                'duration_value' => $sample['duration_value'],
                'duration_unit' => $sample['duration_unit'],
                'departure_info' => [],
                'price_amount' => $sample['price'],
                'price_currency' => 'USD',
                'price_label' => '/Person',
                'discount_percent' => 0,
                'max_altitude' => $sample['max_altitude'],
                'best_season' => $sample['best_season'],
                'group_size' => $sample['group_size'],
                'region' => $sample['region'],
                'category' => $sample['category'],
                'is_popular' => false,
                'status' => 'active',
                'published_at' => now(),
                'trip_grade' => 'Moderate',
                'country' => 'Nepal',
                'activities' => ucwords(str_replace('-', ' ', $sample['category'])),
            ]);
        }
    }
}
