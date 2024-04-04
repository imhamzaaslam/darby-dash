<?php

namespace Database\Factories;

use App\Models\ArticleCategory;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->word(),
            'content' => '{"time":1666900581757,"blocks":[{"id":"3vOcOSWKHw","type":"paragraph","data":{"text":"Lorem ipsum dolor sit amet consectetur adipisicing elit. Maxime mollitia,"}},{"id":"qNvxqsNWpp","type":"paragraph","data":{"text":"molestiae quas vel sint commodi repudiandae consequuntur voluptatum laborum"}},{"id":"3qRMQHsT1P","type":"paragraph","data":{"text":"numquam blanditiis harum quisquam eius sed odit fugiat iusto fuga praesentium"}},{"id":"IDf-szqLKN","type":"paragraph","data":{"text":"optio, eaque rerum! Provident similique accusantium nemo autem. Veritatis"}},{"id":"X3kMWROsj-","type":"paragraph","data":{"text":"obcaecati tenetur iure eius earum ut molestias architecto voluptate aliquam"}},{"id":"mlCDDjLEZu","type":"paragraph","data":{"text":"nihil, eveniet aliquid culpa officia aut! Impedit sit sunt quaerat, odit,"}},{"id":"-90qI3MLAZ","type":"paragraph","data":{"text":"tenetur error, harum nesciunt ipsum debitis quas aliquid. Reprehenderit,"}},{"id":"qGdgHm_5S9","type":"paragraph","data":{"text":"quia. Quo neque error repudiandae fuga? Ipsa laudantium mol"}},{"id":"06mJOkQU3R","type":"list","data":{"style":"unordered","items":["My second point","Testing 12","test"]}}],"version":"2.25.0"}',
            'category_id' => ArticleCategory::factory(),
        ];
    }
}
