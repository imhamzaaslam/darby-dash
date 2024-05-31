<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\ProjectList;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lists = [
            ['name' => 'To Do', 'initial_name' => 'todo', 'display_order' => 1],
            ['name' => 'In Progress', 'initial_name' => 'in_progress', 'display_order' => 2],
            ['name' => 'Completed', 'initial_name' => 'completed', 'display_order' => 3],
        ];
        $project_manager_id = 2;
        $firstProject = Project::create([
            'title' => 'Darby',
            'description' => 'Darby is a project management system.',
            'project_type_id' => 1,
            'project_manager_id' => $project_manager_id,
            'est_hours' => 100,
            'est_budget' => 1000,
        ]);
        $firstProjectMembers = [4, 5, 6];
        foreach ($firstProjectMembers as $memberId) {
            ProjectMember::create([
                'project_id' => $firstProject->id,
                'user_id' => $memberId,
            ]);
        }
        foreach ($lists as $list) {
            ProjectList::create([
                'project_id' => $firstProject->id,
                'name' => $list['name'],
                'display_order' => $list['display_order'],
                'is_deletable' => 0
            ]);
        }

        $secondProject = Project::create([
            'title' => 'Proborrower',
            'description' => 'Proborrower is a loan management system.',
            'project_type_id' => 1,
            'project_manager_id' => $project_manager_id,
            'est_hours' => 200,
            'est_budget' => 2000,
        ]);
        $secondProjectMembers = [4, 5];
        foreach ($secondProjectMembers as $memberId) {
            ProjectMember::create([
                'project_id' => $secondProject->id,
                'user_id' => $memberId,
            ]);
        }
        foreach ($lists as $list) {
            ProjectList::create([
                'project_id' => $secondProject->id,
                'name' => $list['name'],
                'display_order' => $list['display_order'],
                'is_deletable' => 0
            ]);
        }

        $thirdProject = Project::create([
            'title' => 'Testing',
            'description' => 'This is a testing project.',
            'project_type_id' => 1,
            'project_manager_id' => $project_manager_id,
            'est_hours' => 300,
            'est_budget' => 3000,
        ]);
        $thirdProjectMembers = [4, 5, 6];
        foreach ($thirdProjectMembers as $memberId) {
            ProjectMember::create([
                'project_id' => $thirdProject->id,
                'user_id' => $memberId,
            ]);
        }
        foreach ($lists as $list) {
            ProjectList::create([
                'project_id' => $thirdProject->id,
                'name' => $list['name'],
                'display_order' => $list['display_order'],
                'is_deletable' => 0
            ]);
        }

        $fourthProject = Project::create([
            'title' => 'Happy Nest Media',
            'description' => 'Happy Nest Media is a media company.',
            'project_type_id' => 2,
            'project_manager_id' => $project_manager_id,
            'est_hours' => 400,
            'est_budget' => 4000,
        ]);
        $fourthProjectMembers = [4, 5];
        foreach ($fourthProjectMembers as $memberId) {
            ProjectMember::create([
                'project_id' => $fourthProject->id,
                'user_id' => $memberId,
            ]);
        }
        foreach ($lists as $list) {
            ProjectList::create([
                'project_id' => $fourthProject->id,
                'name' => $list['name'],
                'display_order' => $list['display_order'],
                'is_deletable' => 0
            ]);
        }

        $fifthProject = Project::create([
            'title' => 'Bigberry',
            'description' => 'Bigberry is a software company.',
            'project_type_id' => 2,
            'project_manager_id' => $project_manager_id,
            'est_hours' => 500,
            'est_budget' => 5000,
        ]);
        $fifthProjectMembers = [4, 5, 6];
        foreach ($fifthProjectMembers as $memberId) {
            ProjectMember::create([
                'project_id' => $fifthProject->id,
                'user_id' => $memberId,
            ]);
        }
        foreach ($lists as $list) {
            ProjectList::create([
                'project_id' => $fifthProject->id,
                'name' => $list['name'],
                'display_order' => $list['display_order'],
                'is_deletable' => 0
            ]);
        }

        $sixthProject = Project::create([
            'title' => 'IdeaAffix',
            'description' => 'IdeaAffix is a project management system.',
            'project_type_id' => 3,
            'project_manager_id' => $project_manager_id,
            'est_hours' => 600,
            'est_budget' => 6000,
        ]);
        $sixthProjectMembers = [4, 5];
        foreach ($sixthProjectMembers as $memberId) {
            ProjectMember::create([
                'project_id' => $sixthProject->id,
                'user_id' => $memberId,
            ]);
        }
        foreach ($lists as $list) {
            ProjectList::create([
                'project_id' => $sixthProject->id,
                'name' => $list['name'],
                'display_order' => $list['display_order'],
                'is_deletable' => 0
            ]);
        }
    }
}
