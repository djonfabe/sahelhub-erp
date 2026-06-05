<?php

namespace Workdo\Hrm\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class SystemSetupController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $sections = [
            'manage-branches'               => 'hrm.branches.index',
            'manage-departments'            => 'hrm.departments.index',
            'manage-designations'           => 'hrm.designations.index',
            'manage-employee-document-types'=> 'hrm.employee-document-types.index',
            'manage-award-types'            => 'hrm.award-types.index',
            'manage-termination-types'      => 'hrm.termination-types.index',
            'manage-warning-types'          => 'hrm.warning-types.index',
            'manage-complaint-types'        => 'hrm.complaint-types.index',
            'manage-holiday-types'          => 'hrm.holiday-types.index',
            'manage-document-categories'    => 'hrm.document-categories.index',
            'manage-announcement-categories'=> 'hrm.announcement-categories.index',
            'manage-event-types'            => 'hrm.event-types.index',
            'manage-allowance-types'        => 'hrm.allowance-types.index',
            'manage-deduction-types'        => 'hrm.deduction-types.index',
            'manage-loan-types'             => 'hrm.loan-types.index',
            'manage-working-days'           => 'hrm.working-days.index',
            'manage-ip-restricts'           => 'hrm.ip-restricts.index',
        ];

        foreach ($sections as $permission => $routeName) {
            if ($user->can($permission)) {
                return redirect()->route($routeName);
            }
        }

        return back()->with('error', __('Permission denied'));
    }
}
