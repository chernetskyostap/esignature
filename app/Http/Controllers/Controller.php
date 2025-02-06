<?php

namespace App\Http\Controllers;

use App\Domain\User\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Info(
 *     title="E-signature documentation",
 *     version="1.0.0",
 *     description="This is the API for E-Signature app",
 *     @OA\Contact(
 *         email="support@example.com"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Local Development Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer"
 * )
 * @OA\Schema(
 *     schema="Pagination",
 *     description="Pagination object",
 *     required={"pagination"},
 *     @OA\Property(
 *         property="pagination",
 *         nullable=false,
 *         type="object",
 *         allOf={
 *             @OA\Schema(
 *                 required={"count", "current_page", "per_page", "total", "total_pages", "links"},
 *                 @OA\Property(
 *                     property="count",
 *                     type="integer",
 *                     nullable=false,
 *                     description="Count result on page",
 *                 ),
 *                 @OA\Property(
 *                     property="current_page",
 *                     type="integer",
 *                     nullable=false,
 *                     description="Current page number",
 *                 ),
 *                 @OA\Property(
 *                     property="per_page",
 *                     type="integer",
 *                     nullable=false,
 *                     description="Curent page limit",
 *                 ),
 *                 @OA\Property(
 *                     property="total",
 *                     type="integer",
 *                     nullable=false,
 *                     description="Total results",
 *                 ),
 *                 @OA\Property(
 *                     property="total_pages",
 *                     type="integer",
 *                     nullable=false,
 *                     description="Total pages",
 *                 ),
 *                 @OA\Property(
 *                     property="links",
 *                     type="object",
 *                     nullable=false,
 *                     description="Links for pagination",
 *                     allOf={
 *                         @OA\Schema(
 *                             @OA\Property(
 *                                 property="next",
 *                                 type="string",
 *                                 nullable=true,
 *                                 description="Link for next page",
 *                             ),
 *                             @OA\Property(
 *                                 property="previous",
 *                                 type="string",
 *                                 nullable=true,
 *                                 description="Link for previous page",
 *                             ),
 *                         )
 *                     }
 *                 ),
 *             )
 *         }
 *     )
 * )
 */

abstract class Controller
{
    protected function currentUser(): User
    {
        return Auth::user();
    }
}
