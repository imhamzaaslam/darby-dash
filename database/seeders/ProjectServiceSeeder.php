<?php

namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProjectService;
use App\Models\File;
use App\Enums\FileType;
use Illuminate\Support\Facades\Storage;

class ProjectServiceSeeder extends Seeder
{
    protected $fileUploadService;

    public function __construct()
    {
        $this->fileUploadService = app()->make('App\Services\FileUploadService');
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define the project services data with corresponding image file names
        $projectServices = [
            // Web Designs (project_type_id = 1)
            [
                'title' => 'Creative Website Design',
                'description' => '<p>Our <strong>creative website designs</strong> are tailored to engage your audience and deliver a memorable user experience.</p>',
                'project_type_id' => 1,
            ],
            [
                'title' => 'E-Commerce Website Solutions',
                'description' => '<p>Boost your online sales with our custom <strong>e-commerce website designs</strong> that provide a seamless shopping experience.</p>',
                'project_type_id' => 1,
            ],
            [
                'title' => 'Responsive Web Design',
                'description' => '<p>Our <strong>responsive web designs</strong> ensure your website looks and performs great on all devices, from desktops to mobiles.</p>',
                'project_type_id' => 1,
            ],
            [
                'title' => 'Landing Page Design',
                'description' => '<p>Capture leads with high-converting <strong>landing page designs</strong> tailored to your business needs.</p>',
                'project_type_id' => 1,
            ],
            [
                'title' => 'UI/UX Design Services',
                'description' => '<p>Enhance user experience with our expert <strong>UI/UX design services</strong>, creating intuitive and beautiful interfaces.</p>',
                'project_type_id' => 1,
            ],

            // SEO Programs (project_type_id = 2)
            [
                'title' => 'Local SEO Optimization',
                'description' => '<p>Increase your local visibility with our <strong>local SEO optimization services</strong> designed to bring customers to your business.</p>',
                'project_type_id' => 2,
            ],
            [
                'title' => 'Technical SEO Services',
                'description' => '<p>Fix all the <strong>technical SEO</strong> issues on your site to ensure that search engines can index your content properly.</p>',
                'project_type_id' => 2,
            ],
            [
                'title' => 'Content Marketing and SEO',
                'description' => '<p>Boost organic traffic with our strategic <strong>content marketing and SEO</strong> approach that focuses on relevant content creation.</p>',
                'project_type_id' => 2,
            ],
            [
                'title' => 'On-Page SEO Optimization',
                'description' => '<p>Optimize your website\'s pages for better rankings with our <strong>on-page SEO</strong> services.</p>',
                'project_type_id' => 2,
            ],
            [
                'title' => 'SEO Audit Services',
                'description' => '<p>Get a complete analysis of your website with our comprehensive <strong>SEO audit services</strong>.</p>',
                'project_type_id' => 2,
            ],

            // Google Ads (project_type_id = 3)
            [
                'title' => 'Google Ads Campaign Management',
                'description' => '<p>We manage your <strong>Google Ads campaigns</strong> to ensure maximum return on investment and targeted reach.</p>',
                'project_type_id' => 3,
            ],
            [
                'title' => 'Google Ads Audit and Optimization',
                'description' => '<p>Optimize your ad spend with a detailed <strong>Google Ads audit</strong> and ongoing optimization services.</p>',
                'project_type_id' => 3,
            ],
            [
                'title' => 'PPC Campaign Strategy',
                'description' => '<p>Develop a winning strategy for your <strong>Pay-Per-Click (PPC) campaigns</strong> on Google Ads with our expertise.</p>',
                'project_type_id' => 3,
            ],
            [
                'title' => 'Display Ads on Google Network',
                'description' => '<p>Engage new audiences with <strong>display ads</strong> across the Google Display Network.</p>',
                'project_type_id' => 3,
            ],
            [
                'title' => 'Google Shopping Ads',
                'description' => '<p>Promote your products with <strong>Google Shopping Ads</strong>, increasing sales and visibility in the shopping search results.</p>',
                'project_type_id' => 3,
            ],
        ];

        $placeholderImagePath = public_path('images/servicePlaceholder.png');

        foreach ($projectServices as $serviceData) {
            $projectService = ProjectService::create([
                'title' => $serviceData['title'],
                'description' => $serviceData['description'],
                'project_type_id' => $serviceData['project_type_id'],
            ]);

            $uploadedFile = new \Illuminate\Http\UploadedFile($placeholderImagePath, basename($placeholderImagePath));
            $disk = app()->environment('testing') ? 'testing' : 'public';
            $fileData = $this->fileUploadService->uploadFile(
                $uploadedFile,
                'project_service_images',
                $disk,
                basename($placeholderImagePath)
            );
            $imageSize = $uploadedFile->getSize();
            File::create([
                 'fileable_type' => ProjectService::class,
                 'fileable_id' => $projectService->id,
                 'name' => 'servicePlaceholder.png',
                 'type' => FileType::AVATAR->value,
                 'path' => $fileData->path,
                 'url' => $fileData->url,
                 'mime_type' => $fileData->mimeType,
                 'size' => $imageSize,
            ]);
        }
    }
}
