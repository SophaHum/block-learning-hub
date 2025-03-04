<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Programming
            [
                'name' => 'CSS',
                'slug' => 'css',
                'description' => 'CSS',
                'color' => '#3b82f6',
            ],
            [
                'name' => 'Laravel',
                'slug' => 'laravel',
                'description' => 'The Laravel framework',
                'color' => '#FF0000',
            ],
            [
                'name' => 'Vue.js',
                'slug' => 'vue-js',
                'description' => 'The Vue.js framework',
                'color' => '#00FF00',
            ],
            [
                'name' => 'Tailwind CSS',
                'slug' => 'tailwind-css',
                'description' => 'The Tailwind CSS framework',
                'color' => '#0000FF',
            ],
            [
                'name' => 'DevOps',
                'slug' => 'devops',
                'description' => 'The DevOps framework',
                'color' => '#FFFF00',
            ],
            [
                'name' => 'Docker',
                'slug' => 'docker',
                'description' => 'The Docker framework',
                'color' => '#00FFFF',
            ],
            [
                'name' => 'Kubernetes',
                'slug' => 'kubernetes',
                'description' => 'The Kubernetes framework',
                'color' => '#FF0000',
            ],
            [
                'name' => 'PHP',
                'slug' => 'php',
                'description' => 'The PHP framework',
                'color' => '#00FF00',
            ],
            [
                'name' => 'JavaScript',
                'slug' => 'javascript',
                'description' => 'The JavaScript framework',
                'color' => '#0000FF',
            ],
            [
                'name' => 'Typescript',
                'slug' => 'typescript',
                'description' => 'The Typescript Lanaguage',
                'color' => '#0000FF',
            ],
            [
                'name' => 'Node.js',
                'slug' => 'node-js',
                'description' => 'The Node.js framework',
                'color' => '#FF00FF',
            ],
            [
                'name' => 'React',
                'slug' => 'react',
                'description' => 'The React framework',
                'color' => '#FFFF00',
            ],
            [
                'name' => 'Python',
                'slug' => 'python',
                'description' => 'The Python framework',
                'color' => '#FF0000',
            ],
            [
                'name' => 'Go',
                'slug' => 'go',
                'description' => 'The Go framework',
                'color' => '#0000FF',
            ],
            // Database
            [
                'name' => 'PostgreSQL',
                'slug' => 'postgresql',
                'description' => 'The PostgreSQL framework',
                'color' => '#00FF00',
            ],
            [
                'name' => "MangoDB",
                'slug' => 'mangodb',
                'description' => 'The MangoDB framework',
                'color' => '#FF00FF',
            ],
            [
                'name' => 'Redis',
                'slug' => 'redis',
                'description' => 'The Redis framework',
                'color' => '#00FF00',
            ],
            // Cloud server
            [
                'name' => 'AWS',
                'slug' => 'aws',
                'description' => 'The AWS framework',
                'color' => '#FF00FF',
            ],
            [
                'name' => 'Nginx',
                'slug' => 'nginx',
                'description' => 'The Nginx framework',
                'color' => '#FFFF00',
            ],
            [
                'name' => 'Google Cloud',
                'slug' => 'google-cloud',
                'description' => 'The Google Cloud framework',
                'color' => '#FFFF00',
            ],
            [
                'name' => 'Azure',
                'slug' => 'azure',
                'description' => 'The Azure framework',
                'color' => '#FF0000',
            ],
            // CI/CD
            [
                'name' => 'Jenkins',
                'slug' => 'jenkins',
                'description' => 'The Jenkins framework',
                'color' => '#00FF00',
            ],
            [
                'name' => 'CircleCI',
                'slug' => 'circleci',
                'description' => 'The CircleCI framework',
                'color' => '#0000FF',
            ],
            [
                'name' => 'GitHub Actions',
                'slug' => 'github-actions',
                'description' => 'The GitHub Actions framework',
                'color' => '#FF00FF',
            ],
            // Monitoring
            [
                'name' => 'Prometheus',
                'slug' => 'prometheus',
                'description' => 'The Prometheus framework',
                'color' => '#FFFF00',
            ],
            [
                'name' => 'Grafana',
                'slug' => 'grafana',
                'description' => 'The Grafana framework',
                'color' => '#FF0000',
            ],
            // linux
            [
                'name' => 'Linux',
                'slug' => 'linux',
                'description' => 'The Linux framework',
                'color' => '#00FF00',
            ],
            [
                'name' => 'Ubuntu',
                'slug' => 'ubuntu',
                'description' => 'The Ubuntu framework',
                'color' => '#0000FF',
            ],
            [
                'name' => 'Debian',
                'slug' => 'debian',
                'description' => 'The Debian framework',
                'color' => '#FFFF00',
            ],
            //mathematics
            [
                'name' => 'Matchematics',
                'slug' => 'math',
                'description' => 'The Mathematics ',
            ]
        ];

        foreach($categories as $category){
            Category::create($category);
        }
    }
}
