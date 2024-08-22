<?php

namespace Database\Seeders;

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
        $projects = [
            [
                'title' => 'Darby',
                'description' => 'Darby is a project management system.',
                'project_type_id' => 1,
                'budget_amount' => 10000,
                'bucks_share' => 100,
                'bucks_share_type' => 'fixed',
            ],
            [
                'title' => 'Proborrower',
                'description' => 'Proborrower is a loan management system.',
                'project_type_id' => 1,
                'budget_amount' => 20000,
                'bucks_share' => 10,
                'bucks_share_type' => 'percentage',
            ],
            [
                'title' => 'Testing',
                'description' => 'This is a testing project.',
                'project_type_id' => 1,
                'budget_amount' => 30000,
                'bucks_share' => 200,
                'bucks_share_type' => 'fixed',
            ],
            [
                'title' => 'Happy Nest Media',
                'description' => 'Happy Nest Media is a media company.',
                'project_type_id' => 2,
                'budget_amount' => 40000,
                'bucks_share' => 15,
                'bucks_share_type' => 'percentage',
            ],
            [
                'title' => 'Bigberry',
                'description' => 'Bigberry is a software company.',
                'project_type_id' => 2,
                'budget_amount' => 50000,
                'bucks_share' => 15,
                'bucks_share_type' => 'percentage',
            ],
            [
                'title' => 'IdeaAffix',
                'description' => 'IdeaAffix is a project management system.',
                'project_type_id' => 3,
                'budget_amount' => 60000,
                'bucks_share' => 150,
                'bucks_share_type' => 'fixed',
            ],
        ];

        $projectMembers = [
            ['id' => 2, 'role_id' => 2],
            ['id' => 3, 'role_id' => 3],
            ['id' => 4, 'role_id' => 4],
            ['id' => 5, 'role_id' => 4],
            ['id' => 6, 'role_id' => 4],
        ];

        $lists = [
            ['name' => 'Backlog', 'display_order' => 1],
        ];

        foreach ($projects as $projectData) {
            $project = Project::create($projectData);
            $this->addMembersToProject($project, $projectMembers);
            $this->addListsToProject($project, $lists);
        }
    }

    /**
     * Add members to a project.
     */
    private function addMembersToProject(Project $project, array $members): void
    {
        foreach ($members as $member) {
            ProjectMember::create([
                'project_id' => $project->id,
                'user_id' => $member['id'],
                'role_id' => $member['role_id'],
            ]);
        }
    }

    /**
     * Add lists to a project.
     */
    private function addListsToProject(Project $project, array $lists): void
    {
        foreach ($lists as $list) {
            ProjectList::create([
                'project_id' => $project->id,
                'name' => $list['name'],
                'display_order' => $list['display_order'],
                'is_deletable' => 0,
            ]);
        }
    }
}
