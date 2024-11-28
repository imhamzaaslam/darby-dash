<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Company;

class CheckCompanyStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isTenant = tenancy()->tenant ? true : false;

        if($isTenant)
        {
            $tenantId = tenancy()->tenant->id;
            $tenantName = str_replace('_', ' ', $tenantId);
            $company = Company::where('name', $tenantName)->first();
    
            if (!$company || $company->status == 0) {
                return response()->json(['error' => 'Access denied for inactive company'], 403);
            }
            
            return $next($request);
        }

        return $next($request);
    }
}
