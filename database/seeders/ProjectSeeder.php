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
            ['name' => 'Backlog', 'display_order' => 1],
        ];
        $firstProject = Project::create([
            'title' => 'Darby',
            'description' => 'Darby is a project management system.',
            'project_type_id' => 1,
            'budget_amount' => 10000,
            'bucks_share' => 100,
            'bucks_share_type' => 'fixed',
        ]);
        $firstProjectMembers = [2, 4, 5, 6];
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
            'budget_amount' => 20000,
            'bucks_share' => 10,
            'bucks_share_type' => 'percentage',
        ]);
        $secondProjectMembers = [2, 4, 5];
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
            'budget_amount' => 30000,
            'bucks_share' => 200,
            'bucks_share_type' => 'fixed',
        ]);
        $thirdProjectMembers = [2, 4, 5, 6];
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
            'budget_amount' => 40000,
            'bucks_share' => 15,
            'bucks_share_type' => 'percentage',
        ]);
        $fourthProjectMembers = [2, 4, 5];
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
            'budget_amount' => 50000,
            'bucks_share' => 15,
            'bucks_share_type' => 'percentage',
        ]);
        $fifthProjectMembers = [2, 4, 5, 6];
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
            'budget_amount' => 60000,
            'bucks_share' => 150,
            'bucks_share_type' => 'fixed',
        ]);
        $sixthProjectMembers = [2, 4, 5];
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
